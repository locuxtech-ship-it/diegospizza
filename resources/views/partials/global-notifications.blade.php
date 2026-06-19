<script>
(function(){
    var lastIds = [];
    var audioCtx = null;

    function sonido() {
        try {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            if (audioCtx.state === 'suspended') audioCtx.resume();
            var t = audioCtx.currentTime;
            function campana(freq, harm, start, dur, vol) {
                var o1 = audioCtx.createOscillator(), g1 = audioCtx.createGain();
                o1.type='sine'; o1.frequency.setValueAtTime(freq,start);
                o1.connect(g1); g1.connect(audioCtx.destination);
                g1.gain.setValueAtTime(0,start); g1.gain.linearRampToValueAtTime(vol,start+0.01);
                g1.gain.exponentialRampToValueAtTime(0.001,start+dur);
                o1.start(start); o1.stop(start+dur);
                var o2 = audioCtx.createOscillator(), g2 = audioCtx.createGain();
                o2.type='sine'; o2.frequency.setValueAtTime(harm,start);
                o2.connect(g2); g2.connect(audioCtx.destination);
                g2.gain.setValueAtTime(0,start); g2.gain.linearRampToValueAtTime(vol*0.4,start+0.01);
                g2.gain.exponentialRampToValueAtTime(0.001,start+dur*0.6);
                o2.start(start); o2.stop(start+dur);
            }
            campana(1047,1397,t,0.35,0.25); campana(784,1175,t+0.28,0.45,0.3);
        } catch(e){}
    }

    function sonidoFallback() {
        try {
            var a = new Audio();
            a.src = 'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACAf39/f3+AgH9/f3+AgH9/f3+AgICAgICAgICAf39/f3+AgH9/f3+AgH9/f3+AgICAgICAgICAf39/f39/f39/f39/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgICAgICAgICAf39/f3+AgH9/f3+AgH9/f39/f39/f3+AgH9/f3+AgH9/f3+AgH9/f39/f3+AgH9/f3+AgICAgICAgICAf39/f39/f3+AgH9/f3+AgH9/f39/f39/f3+AgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC';
            a.play().catch(function(){});
        } catch(e){}
    }

    var toastPendientes = null;

    function mostrarToast(p) {
        if (!toastPendientes || !toastPendientes.parentNode) {
            toastPendientes = document.createElement('div');
            toastPendientes.style.cssText = 'position:fixed;bottom:20px;right:20px;z-index:99999;background:#ef4444;color:white;border-radius:12px;padding:14px 18px;box-shadow:0 10px 40px rgba(0,0,0,0.3);max-width:380px;animation:pdvSlideIn 0.35s cubic-bezier(0.16,1,0.3,1);font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;display:flex;align-items:center;gap:10px;cursor:pointer;';
            toastPendientes.addEventListener('click', function(){ window.location.href = '/admin/comandas'; });
            var cerrar = document.createElement('span');
            cerrar.textContent = '×';
            cerrar.style.cssText = 'font-size:20px;opacity:0.7;margin-left:8px;align-self:flex-start;';
            cerrar.addEventListener('click', function(e){
                e.stopPropagation();
                toastPendientes.style.transition = 'all 0.3s ease';
                toastPendientes.style.opacity = '0';
                toastPendientes.style.transform = 'translateX(100px)';
                setTimeout(function(){ if(toastPendientes && toastPendientes.parentNode){ toastPendientes.remove(); toastPendientes = null; } }, 300);
            });
            toastPendientes.innerHTML = '<div style="background:rgba(255,255,255,0.2);border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">🆕</div><div style="flex:1;min-width:0;"><div style="font-size:13px;font-weight:700;" id="pdv-toast-msg">Pedidos pendientes</div><div style="font-size:11px;opacity:0.8;">Haz clic para ir al PDV</div></div>';
            toastPendientes.appendChild(cerrar);
            document.body.appendChild(toastPendientes);
        }
        var msg = toastPendientes.querySelector('#pdv-toast-msg');
        var count = parseInt(toastPendientes.getAttribute('data-count') || '0') + 1;
        toastPendientes.setAttribute('data-count', count);
        msg.textContent = '🔔 ' + count + ' pedido(s) pendiente(s)';
    }

    function notifSistema(p) {
        if (!('Notification' in window) || Notification.permission === 'denied') return;
        if (Notification.permission === 'granted') {
            try {
                var n = new Notification('🍕 Nuevo pedido #' + (p.numero_pedido||p.id), {
                    body: (p.cliente?.nombre||'') + ' (' + (p.origen||'PDV').toUpperCase() + ')',
                    tag: 'pdv-pedido-' + p.id,
                    requireInteraction: true
                });
                setTimeout(function(){ n.close(); }, 10000);
                n.onclick = function(){ window.focus(); this.close(); };
            } catch(e){}
        }
    }

    function actualizarBadge(count) {
        var badges = document.querySelectorAll('.fi-sidebar-item-badge');
        badges.forEach(function(b) {
            var item = b.closest('li') || b.parentElement;
            if (item && item.textContent.includes('PDV')) {
                if (count > 0) { b.textContent = count; b.style.display = 'inline-flex'; }
                else { b.style.display = 'none'; }
            }
        });
    }

    function limpiarToast() {
        if (toastPendientes && toastPendientes.parentNode) {
            toastPendientes.remove();
            toastPendientes = null;
        }
    }

    function verificarNuevosPedidos() {
        if (window.location.pathname.includes('/admin/comandas')) {
            limpiarToast();
            return;
        }

        fetch('/api/pedidos/pendientes')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var pedidos = data.pedidos || [];
                var nuevosIds = pedidos.map(function(p){ return p.id; });
                actualizarBadge(pedidos.length);

                if (nuevosIds.length === 0) {
                    limpiarToast();
                }

                if (lastIds.length > 0) {
                    var nuevos = [];
                    pedidos.forEach(function(p) {
                        if (lastIds.indexOf(p.id) === -1) {
                            nuevos.push(p);
                        }
                    });
                    if (nuevos.length > 0) {
                        nuevos.forEach(function(p) {
                            sonido();
                            sonidoFallback();
                            mostrarToast(p);
                            notifSistema(p);
                        });
                        try { navigator.vibrate && navigator.vibrate([200,100,200]); } catch(e){}
                    }
                }
                lastIds = nuevosIds;
            })
            .catch(function(){});
    }

    if (typeof Notification !== 'undefined' && Notification.permission === 'default') {
        setTimeout(function(){ Notification.requestPermission(); }, 3000);
    }

    if (!document.querySelector('style#pdv-notif-keyframes')) {
        var s = document.createElement('style');
        s.id = 'pdv-notif-keyframes';
        s.textContent = '@keyframes pdvSlideIn{from{opacity:0;transform:translateX(100px) scale(0.95);}to{opacity:1;transform:translateX(0) scale(1);}}';
        document.head.appendChild(s);
    }

    setTimeout(verificarNuevosPedidos, 3000);
    setInterval(verificarNuevosPedidos, 8000);
})();
</script>
