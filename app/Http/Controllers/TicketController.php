<?php

namespace App\Http\Controllers;

use App\Models\CierreCaja;
use App\Models\GastoCierre;
use App\Models\NegocioSetting;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\PedidoProducto;

class TicketController extends Controller
{
    public function show(Pedido $pedido)
    {
        $pedido->load('cliente');
        $productos = PedidoProducto::with('producto')
            ->where('pedido_id', $pedido->id)
            ->get();
        $negocio = NegocioSetting::getSettings();

        return view('ticket', compact('pedido', 'productos', 'negocio'));
    }

    public function raw(Pedido $pedido)
    {
        $pedido->load('cliente');
        $productos = PedidoProducto::with('producto')
            ->where('pedido_id', $pedido->id)
            ->get();
        $negocio = NegocioSetting::getSettings();
        $cols = 32;

        $line = str_repeat('=', $cols);

        $t = '';

        // Header
        $t .= $this->center($negocio->nombre_negocio ?? "Diego's Pizza", $cols) . "\n";
        if ($negocio->direccion) {
            $t .= $this->center($negocio->direccion, $cols) . "\n";
        }
        $t .= $line . "\n";

        // Order ref
        $t .= $this->center('Pedido #' . $pedido->numero_pedido, $cols) . "\n";
        $t .= $this->center($pedido->created_at->setTimezone('America/Bogota')->format('d/m/Y H:i'), $cols) . "\n";
        $t .= $line . "\n";

        // Address
        $t .= 'DIRECCION' . "\n";
        $t .= $pedido->cliente->nombre . "\n";
        $t .= 'Conjunto: ' . $pedido->direccion_conjunto . "\n";
        if ($pedido->direccion_torre) {
            $t .= 'Torre: ' . $pedido->direccion_torre . "\n";
        }
        if ($pedido->direccion_apto) {
            $t .= 'Apto: ' . $pedido->direccion_apto . "\n";
        }
        $t .= 'Tel: ' . $pedido->cliente->telefono . "\n";

        if ($pedido->notas) {
            $t .= 'Notas: ' . $pedido->notas . "\n";
        }

        $t .= $line . "\n";
        $t .= 'Productos' . "\n";

        // Products
        foreach ($productos as $pp) {
            $nombre = $pp->producto->nombre;
            if ($pp->mitades) {
                $sabores = collect($pp->mitades)->pluck('nombre')->implode(' / ');
                $nombre .= ' [' . $sabores . ']';
            }
            $subtotalStr = '$' . number_format($pp->subtotal, 0, ',', '.');
            $cantStr = $pp->cantidad . 'x';
            $linea = $cantStr . ' ' . $nombre;
            // Truncate if too long (leave room for price)
            $maxName = $cols - strlen($subtotalStr) - 1;
            if (mb_strlen($linea) > $maxName) {
                $linea = mb_substr($linea, 0, $maxName - 3) . '...';
            }
            $t .= $linea . str_repeat(' ', $cols - mb_strlen($linea) - mb_strlen($subtotalStr)) . $subtotalStr . "\n";

            if ($pp->variant_tamanio) {
                $t .= '  (' . $pp->variant_tamanio . ')' . "\n";
            }
        }

        $t .= $line . "\n";

        // Totals
        $t .= $this->row('Subtotal', '$' . number_format($pedido->subtotal, 0, ',', '.'), $cols) . "\n";
        if ($pedido->descuento_puntos > 0) {
            $t .= $this->row('Desc. puntos', '-$' . number_format($pedido->descuento_puntos, 0, ',', '.'), $cols) . "\n";
        }
        if ($pedido->descuento_manual > 0) {
            $t .= $this->row('Desc. manual', '-$' . number_format($pedido->descuento_manual, 0, ',', '.'), $cols) . "\n";
        }
        $t .= $this->row('TOTAL', '$' . number_format($pedido->total, 0, ',', '.'), $cols) . "\n";

        $t .= $line . "\n";

        // Payment
        $t .= ucfirst($pedido->metodo_pago) . "\n";
        $t .= $line . "\n";

        // Footer
        $t .= $this->center('Gracias por tu pedido!', $cols) . "\n";
        $t .= $this->center($negocio->nombre_negocio ?? "Diego's Pizza", $cols) . "\n";

        return response($t, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }

    private function center(string $text, int $cols): string
    {
        $text = trim($text);
        $len = mb_strlen($text);
        if ($len >= $cols) return $text;
        $pad = intval(($cols - $len) / 2);
        return str_repeat(' ', $pad) . $text;
    }

    private function row(string $label, string $value, int $cols): string
    {
        $space = $cols - mb_strlen($label) - mb_strlen($value);
        if ($space < 1) $space = 1;
        return $label . str_repeat(' ', $space) . $value;
    }

    public function cierre(CierreCaja $cierre)
    {
        $negocio = NegocioSetting::getSettings();
        $cierre->load('gastos', 'user');
        $esAdmin = auth()->user()?->isAdmin() ?? false;

        return view('ticket-cierre', compact('cierre', 'negocio', 'esAdmin'));
    }
}
