const http = require('http');
const fs = require('fs');
const path = require('path');
const { exec } = require('child_process');
const os = require('os');

const PORT = 8192;
const CONFIG_PATH = path.join(os.homedir(), '.diegospizza-print-agent.json');

function loadConfig() {
    try {
        return JSON.parse(fs.readFileSync(CONFIG_PATH, 'utf8'));
    } catch {
        return { printer: '' };
    }
}

function saveConfig(config) {
    fs.writeFileSync(CONFIG_PATH, JSON.stringify(config, null, 2));
}

function printText(text, printerName, callback) {
    const tmpFile = path.join(os.tmpdir(), 'ticket_' + Date.now() + '.txt');
    fs.writeFileSync(tmpFile, text, 'utf8');

    let cmd;
    if (printerName) {
        cmd = `powershell -Command "Get-Content '${tmpFile}' | Out-Printer -Name '${printerName}'"`;
    } else {
        cmd = `powershell -Command "Get-Content '${tmpFile}' | Out-Printer"`;
    }

    exec(cmd, (err, stdout, stderr) => {
        try { fs.unlinkSync(tmpFile); } catch {}
        if (err) {
            callback({ ok: false, error: stderr || err.message });
        } else {
            callback({ ok: true });
        }
    });
}

function listPrinters(callback) {
    exec('powershell -Command "Get-Printer | Select-Object -ExpandProperty Name"', (err, stdout) => {
        if (err) {
            callback([]);
            return;
        }
        callback(stdout.trim().split('\n').map(s => s.trim()).filter(Boolean));
    });
}

const server = http.createServer((req, res) => {
    const send = (data, code = 200) => {
        res.writeHead(code, {
            'Content-Type': 'application/json',
            'Access-Control-Allow-Origin': '*',
            'Access-Control-Allow-Methods': 'GET, POST, OPTIONS',
            'Access-Control-Allow-Headers': 'Content-Type',
        });
        res.end(JSON.stringify(data));
    };

    if (req.method === 'OPTIONS') {
        send({ ok: true });
        return;
    }

    const url = new URL(req.url, `http://localhost:${PORT}`);

    if (req.method === 'GET' && url.pathname === '/ping') {
        send({ ok: true, hostname: os.hostname() });
        return;
    }

    if (req.method === 'GET' && url.pathname === '/printers') {
        listPrinters(printers => send({ ok: true, printers }));
        return;
    }

    if (req.method === 'GET' && url.pathname === '/config') {
        send({ ok: true, config: loadConfig() });
        return;
    }

    if (req.method === 'POST' && url.pathname === '/config') {
        let body = '';
        req.on('data', c => body += c);
        req.on('end', () => {
            try {
                const data = JSON.parse(body);
                saveConfig({ printer: data.printer || '' });
                send({ ok: true });
            } catch { send({ ok: false, error: 'Invalid JSON' }, 400); }
        });
        return;
    }

    if (req.method === 'POST' && url.pathname === '/print') {
        let body = '';
        req.on('data', c => body += c);
        req.on('end', () => {
            try {
                const data = JSON.parse(body);
                const config = loadConfig();
                const printerName = data.printer || config.printer || '';
                printText(data.text, printerName, result => send(result));
            } catch { send({ ok: false, error: 'Invalid JSON' }, 400); }
        });
        return;
    }

    send({ ok: false, error: 'Not found' }, 404);
});

server.listen(PORT, '127.0.0.1', () => {
    console.log('=== Diego\'s Pizza Print Agent ===');
    console.log(`Servidor iniciado en http://localhost:${PORT}`);
    console.log('Configuracion: ~/.diegospizza-print-agent.json');
    console.log('');
    console.log('Endpoints:');
    console.log('  GET  /ping       - Verificar agente');
    console.log('  GET  /printers   - Listar impresoras');
    console.log('  GET  /config     - Ver config actual');
    console.log('  POST /config     - Guardar config');
    console.log('  POST /print      - Imprimir ticket');
});
