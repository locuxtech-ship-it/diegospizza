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

    function mostrarToast(p) {
        var c = document.getElementById('pdv-global-notif-container');
        if (!c) {
            c = document.createElement('div');
            c.id = 'pdv-global-notif-container';
            c.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px;';
            document.body.appendChild(c);
        }
        var n = document.createElement('div');
        n.style.cssText = 'background:#1e293b;color:white;border-radius:12px;padding:14px 18px;box-shadow:0 10px 40px rgba(0,0,0,0.3);max-width:360px;animation:pdvSlideIn 0.35s cubic-bezier(0.16,1,0.3,1);font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;display:flex;align-items:flex-start;gap:10px;cursor:pointer;';
        n.innerHTML = '<div style="background:#22c55e;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">🍕</div><div style="flex:1;min-width:0;"><div style="font-size:13px;font-weight:700;margin-bottom:2px;">Nuevo Pedido #'+(p.numero_pedido||p.id)+'</div><div style="font-size:12px;color:#94a3b8;margin-bottom:3px;">'+(p.cliente?.nombre||'')+'</div><div style="font-size:11px;color:#64748b;"><span style="background:'+(p.origen==='web'?'#2563eb':'#d97706')+';padding:1px 6px;border-radius:4px;color:white;">'+(p.origen||'PDV').toUpperCase()+'</span></div></div><div style="font-size:16px;color:#64748b;margin-left:auto;align-self:flex-start;">×</div>';
        n.addEventListener('click', function() {
            n.style.transition = 'all 0.3s ease';
            n.style.opacity = '0';
            n.style.transform = 'translateX(100px)';
            setTimeout(function() { if (n.parentNode) n.remove(); }, 300);
        });
        c.appendChild(n);
        setTimeout(function() {
            n.style.transition = 'all 0.3s ease';
            n.style.opacity = '0';
            n.style.transform = 'translateX(100px)';
            setTimeout(function() { if (n.parentNode) n.remove(); }, 300);
        }, 8000);
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

    function verificarNuevosPedidos() {
        if (window.location.pathname.includes('/admin/comandas')) return;

        fetch('/api/pedidos/pendientes')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var pedidos = data.pedidos || [];
                var nuevosIds = pedidos.map(function(p){ return p.id; });
                actualizarBadge(pedidos.length);

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
