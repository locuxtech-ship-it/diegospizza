<span style="display:inline-flex;align-items:center;gap:6px;padding:4px 14px;border-radius:9999px;font-size:12px;font-weight:700;letter-spacing:0.5px;text-transform:uppercase;{{ $role === 'admin' ? 'background:#fef2f2;color:#dc2626;border:1px solid #fecaca;' : 'background:#f0f9ff;color:#2563eb;border:1px solid #bfdbfe;' }}">
    @if($role === 'admin')
        <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
    @else
        <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    @endif
    {{ $role === 'admin' ? 'Administrador' : 'Cajero' }}
</span>
