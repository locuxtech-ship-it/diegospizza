<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function showForm(string $pedido)
    {
        $pedidoModel = Pedido::where('id', $pedido)
            ->whereIn('estado', ['entregado', 'finalizado'])
            ->firstOrFail();

        $review = Review::where('pedido_id', $pedidoModel->id)->first();

        return view('public.review-form', ['pedido' => $pedidoModel, 'review' => $review]);
    }

    public function store(Request $request, string $pedido)
    {
        $pedidoModel = Pedido::where('id', $pedido)
            ->whereIn('estado', ['entregado', 'finalizado'])
            ->firstOrFail();

        $existing = Review::where('pedido_id', $pedidoModel->id)->first();
        if ($existing) {
            return back()->with('error', 'Ya dejaste una reseña para este pedido.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:1000',
        ]);

        $cliente = $pedidoModel->cliente;

        Review::create([
            'pedido_id' => $pedidoModel->id,
            'cliente_id' => $cliente?->id,
            'nombre' => $cliente?->nombre ?? 'Cliente',
            'telefono' => $cliente?->telefono,
            'rating' => $validated['rating'],
            'comentario' => $validated['comentario'],
        ]);

        return back()->with('success', 'Gracias por tu reseña! 🍕');
    }
}
