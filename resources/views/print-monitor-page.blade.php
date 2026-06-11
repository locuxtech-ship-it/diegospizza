<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Monitor de Impresión - Diego's Pizza</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: system-ui, sans-serif; background: #111827; color: white; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
.container { text-align: center; padding: 20px; }
.status { display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 24px; }
.dot { width: 14px; height: 14px; border-radius: 50%; background: #22c55e; animation: pulse 2s infinite; }
@keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.3; } }
h1 { font-size: 24px; margin: 0 0 4px; }
.sub { color: #9ca3af; font-size: 14px; margin: 0; }
#ultimo { margin-top: 20px; font-size: 14px; color: #6b7280; }
#contador { margin-top: 8px; font-size: 12px; color: #4b5563; }
iframe { position: fixed; top: -9999px; left: -9999px; width: 1px; height: 1px; }
.key-input { margin-top: 24px; }
.key-input input { background: #1f2937; border: 1px solid #374151; color: white; padding: 8px 12px; border-radius: 6px; font-size: 14px; text-align: center; width: 200px; }
.key-input button { background: #22c55e; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-size: 14px; cursor: pointer; margin-left: 8px; }
</style>
</head>
<body>
<div class="container">
    <div class="status"><div class="dot" id="led"></div><div><h1>Monitor de Impresión</h1><p class="sub">Esperando pedidos...</p></div></div>
    <div id="ultimo">Último pedido: --</div>
    <div id="contador"></div>
</div>
<iframe id="pf"></iframe>

<script>
var ultimoId = 0;
var contador = 0;
var apiKey = 'diegospizza_print_2024';

(function loop() {
    var x = new XMLHttpRequest();
    x.open('GET', '/api/agent/pendientes?key=' + apiKey + '&after_id=' + ultimoId, true);
    x.onload = function() {
        try {
            var r = JSON.parse(x.responseText);
            if (r.ok && r.orders && r.orders.length > 0) {
                r.orders.forEach(function(o) {
                    contador++;
                    document.getElementById('ultimo').innerHTML = '🖨️ Imprimiendo <strong>Pedido #' + o.numero_pedido + '</strong>';
                    document.getElementById('contador').textContent = 'Impresos hoy: ' + contador;
                    document.getElementById('led').style.background = '#f59e0b';
                    var f = document.getElementById('pf');
                    f.src = '/api/agent/ticket/' + o.id + '?key=' + apiKey;
                    f.onload = function() {
                        setTimeout(function() {
                            try { f.contentWindow.print(); } catch(e) { console.log('print error', e); }
                        }, 1500);
                    };
                    if (o.id > ultimoId) { ultimoId = o.id; }
                });
            } else {
                document.getElementById('led').style.background = '#22c55e';
            }
        } catch(e) { console.log('parse error', e); }
        setTimeout(loop, 4000);
    };
    x.onerror = function() { setTimeout(loop, 4000); };
    x.send();
})();
</script>
</body>
</html>
