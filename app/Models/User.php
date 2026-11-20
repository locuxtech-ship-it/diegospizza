<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'puede_repartir',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, ['admin', 'cajero', 'repartidor']);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCajero(): bool
    {
        return $this->role === 'cajero';
    }

    public function isRepartidor(): bool
    {
        return $this->role === 'repartidor';
    }

    /**
     * Puede repartir: los que tienen rol repartidor O los usuarios con la
     * capacidad extra activada (p. ej. cajero que sale a entregar).
     * OJO: isRepartidor() sigue siendo rol puro (home/Dashboard lo usan para
     * aterrizar en Mis Entregas — el doble rol debe entrar a Comandas).
     */
    public function puedeRepartir(): bool
    {
        if ($this->role === 'admin') {
            return false; // el admin nunca reparte, ni con la bandera puesta
        }

        return $this->role === 'repartidor' || (bool) $this->puede_repartir;
    }

    /** Usuarios asignables como repartidor (rol repartidor o capacidad extra) */
    public function scopeRepartidores($query)
    {
        return $query
            ->where('role', '!=', 'admin')
            ->where(function ($q) {
                $q->where('role', 'repartidor')->orWhere('puede_repartir', true);
            });
    }

    public function canCancelarPedido(): bool
    {
        return in_array($this->role, ['admin', 'cajero']);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
