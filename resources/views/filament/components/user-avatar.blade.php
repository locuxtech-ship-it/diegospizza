<div style="display:flex;flex-direction:column;align-items:center;gap:4px;padding:16px 0 8px 0;">
    <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#f43f5e,#e11d48);display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.15);">
        <span style="color:white;font-size:28px;font-weight:700;font-family:'Poppins',sans-serif;">
            {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1)) }}
        </span>
    </div>
</div>
