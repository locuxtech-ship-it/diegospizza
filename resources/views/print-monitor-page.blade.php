<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Monitor - Diego's Pizza</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:system-ui,sans-serif;background:#111827;color:#fff;display:flex;align-items:center;justify-content:center;min-height:100vh;text-align:center;padding:20px}
.dot{display:inline-block;width:14px;height:14px;border-radius:50%;background:#22c55e;animation:pulse 2s infinite;vertical-align:middle;margin-right:12px}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.3}}
h1{font-size:24px;font-weight:700;display:inline;vertical-align:middle}
.sub{color:#9ca3af;font-size:14px;margin-top:8px}
#ultimo{margin-top:20px;font-size:14px;color:#6b7280}
#contador{font-size:12px;color:#4b5563;margin-top:4px}
</style>
</head>
<body>
<div>
<div class="dot" id="led"></div><h1>Monitor</h1>
<p class="sub">Esperando pedidos...</p>
<p id="ultimo">--</p>
<p id="contador"></p>
</div>
<script>
var ultimoId=0,cont=0,key='diegospizza_print_2024',
ultimo=document.getElementById('ultimo'),contador=document.getElementById('contador'),led=document.getElementById('led');
(function c(){
var x=new XMLHttpRequest();
x.open('GET','/api/agent/pendientes?key='+key+'&after_id='+ultimoId,1);
x.onload=function(){try{
var r=JSON.parse(x.responseText);
if(r.ok&&r.orders&&r.orders.length){
r.orders.forEach(function(o){
cont++;ultimo.innerHTML='🖨️ Pedido #'+o.numero_pedido;
contador.textContent='Hoy: '+cont;
led.style.background='#f59e0b';
window.open('/api/agent/ticket/'+o.id+'?key='+key,'pw');
if(o.id>ultimoId)ultimoId=o.id;
});
}else{led.style.background='#22c55e'}
}catch(e){}
setTimeout(c,4000)};
x.onerror=function(){setTimeout(c,4000)};
x.send()})();
</script>
</body>
</html>
