<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Monitor Impresión - Diego's Pizza</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:system-ui,sans-serif;background:#111827;color:#fff;display:flex;align-items:center;justify-content:center;min-height:100vh}
.container{text-align:center;padding:20px}
.status{display:flex;align-items:center;justify-content:center;gap:12px;margin-bottom:24px}
.dot{width:14px;height:14px;border-radius:50%;background:#22c55e;animation:pulse 2s infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.3}}
h1{font-size:24px;margin:0 0 4px}
.sub{color:#9ca3af;font-size:14px;margin:0}
#ultimo{margin-top:20px;font-size:14px;color:#6b7280}
#contador{margin-top:8px;font-size:12px;color:#4b5563}
</style>
</head>
<body>
<div class="container">
<div class="status"><div class="dot" id="led"></div><div><h1>Monitor</h1><p class="sub">Esperando pedidos...</p></div></div>
<div id="ultimo">--</div>
<div id="contador"></div>
</div>
<script>
var ultimoId=0,cont=0,key='diegospizza_print_2024';
(function c(){
var x=new XMLHttpRequest();
x.open('GET','/api/agent/pendientes?key='+key+'&after_id='+ultimoId,1);
x.onload=function(){
try{
var r=JSON.parse(x.responseText);
if(r.ok&&r.orders&&r.orders.length){
r.orders.forEach(function(o){
cont++;
document.getElementById('ultimo').innerHTML='🖨️ Pedido #'+o.numero_pedido;
document.getElementById('contador').textContent='Hoy: '+cont;
document.getElementById('led').style.background='#f59e0b';
var w=window.open('/api/agent/ticket/'+o.id+'?key='+key,'pw');
if(!w||w.closed)setTimeout(function(){
var w2=window.open('/api/agent/ticket/'+o.id+'?key='+key,'pw');
if(!w2)location.href='/api/agent/ticket/'+o.id+'?key='+key;
},100);
if(o.id>ultimoId)ultimoId=o.id;
});
}else document.getElementById('led').style.background='#22c55e';
}catch(e){}
setTimeout(c,4000);
};
x.onerror=function(){setTimeout(c,4000)};
x.send();
})();
</script>
</body>
</html>
