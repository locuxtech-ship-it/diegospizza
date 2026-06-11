<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Monitor de Impresión - Diego's Pizza</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: system-ui, sans-serif; background: #111827; color: white; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
.container { text-align: center; }
.status { display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 24px; }
.dot { width: 14px; height: 14px; border-radius: 50%; background: #22c55e; animation: pulse 2s infinite; }
@keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.3; } }
h1 { font-size: 24px; margin-bottom: 8px; }
p { color: #9ca3af; font-size: 14px; }
#ultimo { margin-top: 16px; font-size: 13px; color: #6b7280; }
iframe { position: fixed; top: -9999px; left: -9999px; width: 1px; height: 1px; }
</style>
</head>
<body>
<div class="container">
    <div class="status"><div class="dot"></div><div><h1>Monitor activo</h1><p>Esperando pedidos nuevos...</p></div></div>
    <div id="ultimo">Último pedido: --</div>
</div>
<iframe id="pf"></iframe>
<script>
var ultimoId = 0;

(function cargar() {
    var x = new XMLHttpRequest();
    x.open('GET', '/api/agent/pendientes?key=diegospizza_print_2024&after_id=' + ultimoId, true);
    x.onload = function() {
        try {
            var r = JSON.parse(x.responseText);
            if (r.ok && r.orders && r.orders.length > 0) {
                r.orders.forEach(function(o) {
                    document.getElementById('ultimo').textContent = '🖨️ Imprimiendo Pedido #' + o.numero_pedido;
                    var f = document.getElementById('pf');
                    f.src = '/admin/ticket/' + o.id;
                    f.onload = function() { setTimeout(function() { try { f.contentWindow.print(); } catch(e) {} }, 800); };
                    if (o.id > ultimoId) ultimoId = o.id;
                });
                var c = new XMLHttpRequest();
                c.open('POST', '/api/agent/ultimo-id', true);
                c.setRequestHeader('Content-Type', 'application/json');
                c.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                c.send(JSON.stringify({id: ultimoId}));
            }
        } catch(e) {}
        setTimeout(cargar, 4000);
    };
    x.onerror = function() { setTimeout(cargar, 4000); };
    x.send();
})();
</script>
</body>
</html>
