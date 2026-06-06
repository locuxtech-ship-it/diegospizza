const http = require('http');
const { exec } = require('child_process');

const PORT = 9199;

const server = http.createServer((req, res) => {
    const cors = {
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Methods': 'POST, GET, OPTIONS',
        'Access-Control-Allow-Headers': 'Content-Type',
    };

    if (req.method === 'OPTIONS') {
        res.writeHead(204, cors);
        res.end();
        return;
    }

    if (req.method === 'GET' && req.url === '/ping') {
        res.writeHead(200, { 'Content-Type': 'application/json', ...cors });
        res.end(JSON.stringify({ ok: true }));
        return;
    }

    if (req.method === 'POST' && req.url === '/print') {
        let body = '';
        req.on('data', chunk => body += chunk);
        req.on('end', () => {
            try {
                const data = JSON.parse(body);
                const { pedido_id, server_url } = data;

                if (!pedido_id || !server_url) {
                    res.writeHead(400, { 'Content-Type': 'application/json', ...cors });
                    res.end(JSON.stringify({ error: 'Faltan pedido_id o server_url' }));
                    return;
                }

                const ticketUrl = server_url.replace(/\/+$/, '') + '/admin/ticket/' + pedido_id + '?t=' + Date.now();
                console.log('Abriendo ticket #' + pedido_id + ': ' + ticketUrl);

                // Force Chrome/Edge to open the URL directly
                // The ticket page has window.onload = window.print() so it prints automatically
                exec('start msedge "' + ticketUrl + '"', { timeout: 10000 }, (err, stdout, stderr) => {
                    if (err) {
                        exec('start chrome "' + ticketUrl + '"', { timeout: 10000 }, (err2) => {
                            if (err2) {
                                exec('start "" "' + ticketUrl + '"', { timeout: 10000 }, (err3) => {
                                    if (err3) {
                                        console.error('Error abriendo ticket #' + pedido_id);
                                        res.writeHead(500, { 'Content-Type': 'application/json', ...cors });
                                        res.end(JSON.stringify({ error: err3.message }));
                                    } else {
                                        console.log('Ticket #' + pedido_id + ' abierto (navegador por defecto)');
                                        res.writeHead(200, { 'Content-Type': 'application/json', ...cors });
                                        res.end(JSON.stringify({ ok: true }));
                                    }
                                });
                            } else {
                                console.log('Ticket #' + pedido_id + ' abierto en Chrome');
                                res.writeHead(200, { 'Content-Type': 'application/json', ...cors });
                                res.end(JSON.stringify({ ok: true }));
                            }
                        });
                    } else {
                        console.log('Ticket #' + pedido_id + ' abierto en Edge');
                        res.writeHead(200, { 'Content-Type': 'application/json', ...cors });
                        res.end(JSON.stringify({ ok: true }));
                    }
                });
            } catch (e) {
                res.writeHead(500, { 'Content-Type': 'application/json', ...cors });
                res.end(JSON.stringify({ error: e.message }));
            }
        });
        return;
    }

    res.writeHead(404, cors);
    res.end();
});

server.listen(PORT, '127.0.0.1', () => {
    console.log('');
    console.log('=== Diego\'s Pizza — Agente de Impresión ===');
    console.log('Escuchando en http://127.0.0.1:' + PORT);
    console.log('Presiona Ctrl+C para detener');
    console.log('');
});
