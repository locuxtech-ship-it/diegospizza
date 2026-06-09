const http = require('http');
const https = require('https');
const fs = require('fs');
const path = require('path');
const { exec } = require('child_process');
const os = require('os');

const PORT = 8192;
const CONFIG_PATH = path.join(os.homedir(), '.diegospizza-print-agent.json');
const POLL_INTERVAL = 4000;

function loadConfig() {
    try {
        return JSON.parse(fs.readFileSync(CONFIG_PATH, 'utf8'));
    } catch {
        return { server_url: 'https://diegospizzabq.click', api_key: '', printer: '', last_printed_id: 0 };
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
        if (err) { callback([]); return; }
        callback(stdout.trim().split('\n').map(s => s.trim()).filter(Boolean));
    });
}

function fetchJson(url, callback) {
    const client = url.startsWith('https') ? https : http;
    client.get(url, { rejectUnauthorized: false }, (res) => {
        let data = '';
        res.on('data', c => data += c);
        res.on('end', () => {
            try { callback(null, JSON.parse(data)); }
            catch (e) { callback(e, null); }
        });
    }).on('error', (e) => callback(e, null));
}

function pollServer() {
    const config = loadConfig();
    if (!config.api_key || !config.server_url) return;

    const url = config.server_url + '/api/agent/pendientes?key=' + encodeURIComponent(config.api_key) + '&after_id=' + config.last_printed_id;

    fetchJson(url, (err, data) => {
        if (err || !data || !data.ok || !data.orders) return;

        data.orders.forEach(function(order) {
            if (!order.raw_text) return;
            printText(order.raw_text, config.printer, function(result) {
                if (result.ok) {
                    console.log('Impreso: Pedido #' + order.numero_pedido + ' (ID ' + order.id + ')');
                    var cfg = loadConfig();
                    if (order.id > cfg.last_printed_id) {
                        cfg.last_printed_id = order.id;
                        saveConfig(cfg);
                    }
                } else {
                    console.log('Error imprimiendo Pedido #' + order.numero_pedido + ': ' + (result.error || 'desconocido'));
                }
            });
        });

        if (data.orders.length > 0) {
            var maxId = data.orders.reduce(function(m, o) { return o.id > m ? o.id : m; }, 0);
            var cfg = loadConfig();
            if (maxId > cfg.last_printed_id) {
                cfg.last_printed_id = maxId;
                saveConfig(cfg);
            }
        }
    });
}

// HTTP server for config/status
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

    if (req.method === 'OPTIONS') { send({ ok: true }); return; }

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
        var c = loadConfig();
        send({ ok: true, config: { printer: c.printer, server_url: c.server_url, last_printed_id: c.last_printed_id } });
        return;
    }

    if (req.method === 'POST' && url.pathname === '/config') {
        let body = '';
        req.on('data', c => body += c);
        req.on('end', () => {
            try {
                const data = JSON.parse(body);
                var cfg = loadConfig();
                if (data.printer !== undefined) cfg.printer = data.printer;
                if (data.server_url !== undefined) cfg.server_url = data.server_url;
                if (data.api_key !== undefined) cfg.api_key = data.api_key;
                saveConfig(cfg);
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
    console.log(`Servidor local: http://localhost:${PORT}`);
    console.log('Polling cada ' + (POLL_INTERVAL / 1000) + 's');
    console.log('');

    const config = loadConfig();
    if (!config.api_key) {
        console.log('[!] API key no configurada.');
        console.log('    Ejecuta setup.bat para configurar.');
    } else {
        console.log('API key: ' + config.api_key.substring(0, 4) + '...');
        console.log('Servidor: ' + config.server_url);
        console.log('Impresora: ' + (config.printer || 'por defecto'));
        console.log('Ultimo ID impreso: ' + config.last_printed_id);
    }

    setInterval(pollServer, POLL_INTERVAL);
});
