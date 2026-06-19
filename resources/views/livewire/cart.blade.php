<div>
    {{-- Overlay --}}
    <div class="overlay {{ $open ? 'open' : '' }}" wire:click="cerrar"></div>

    {{-- Slide-over Cart --}}
    <div class="cart-slideover {{ $open ? 'open' : '' }}">
        <div class="flex flex-col h-full">
            {{-- Header --}}
            <div class="flex items-center justify-between p-4 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Tu Pedido</h2>
                <button wire:click="cerrar" class="text-gray-400 hover:text-gray-600 p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Items --}}
            @if(empty($items))
                <div class="flex-1 flex items-center justify-center text-center p-8">
                    <div>
                        <span class="text-5xl block mb-3">🛒</span>
                        <p class="text-gray-500 font-medium">Tu carrito está vacío</p>
                        <p class="text-gray-400 text-sm mt-1">Agrega productos del menú</p>
                    </div>
                </div>
            @else
                <div class="flex-1 overflow-y-auto p-4 space-y-3">
                    @foreach($items as $key => $item)
                        <div class="flex items-center gap-2 sm:gap-3 bg-gray-50 rounded-xl p-2 sm:p-3">
                            <div class="w-12 h-12 rounded-lg bg-orange-50 flex items-center justify-center flex-shrink-0 text-xl">
                                🍕
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $item['nombre'] }}</p>
                                <p class="text-xs text-gray-400">
                                    @if($item['variant_tamanio'] ?? null)
                                        <span class="inline-block bg-orange-100 rounded px-1.5 py-0.5 text-[10px] font-medium" style="color: #FF8D08;">{{ $item['variant_tamanio'] }}</span> ·
                                    @endif
                                    @if($item['mitades'] ?? null)
                                        <span class="text-xs" style="color: #ea580c;">
                                            {{ collect($item['mitades'])->pluck('nombre')->implode(' / ') }}
                                        </span> ·
                                    @endif
                                    ${{ number_format($item['precio'], 0, ',', '.') }} c/u
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="actualizarCantidad('{{ $key }}', {{ $item['cantidad'] - 1 }})" class="w-7 h-7 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-red-500 hover:border-red-200 transition text-sm font-bold">−</button>
                                <span class="text-sm font-bold text-gray-900 w-6 text-center">{{ $item['cantidad'] }}</span>
                                <button wire:click="actualizarCantidad('{{ $key }}', {{ $item['cantidad'] + 1 }})" class="w-7 h-7 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-green-600 hover:border-green-200 transition text-sm font-bold">+</button>
                            </div>
                            <div class="text-right flex-shrink-0 min-w-[60px]">
                                <p class="text-sm font-bold" style="color: #FF8D08;">${{ number_format($item['precio'] * $item['cantidad'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Footer --}}
                <div class="border-t border-gray-100 p-4 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-semibold text-gray-900">${{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-base font-bold">
                        <span class="text-gray-900">Total</span>
                        <span style="color: #FF8D08;">${{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <button wire:click="cerrar" class="w-full flex items-center justify-center gap-1.5 py-2 rounded-lg text-xs font-semibold transition-all duration-200 active:scale-[0.98] border" style="border-color: #FF8D08; color: #FF8D08;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Seguir agregando
                    </button>
                    <button wire:click="vaciarCarrito" class="w-full text-center text-xs text-gray-400 hover:text-red-500 transition py-1">
                        Vaciar carrito
                    </button>
                    <a href="{{ route('checkout') }}" wire:navigate
                        class="block w-full text-center text-white py-3 rounded-xl font-bold transition-all duration-200 active:scale-[0.98] shadow-sm"
                        style="background-color: #FF8D08;">
                        Ir a pagar · ${{ number_format($total, 0, ',', '.') }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Floating Cart Button --}}
    @if($cantidad > 0)
        <button wire:click="abrir" class="cart-fab">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
            </svg>
            <span class="cart-fab-badge">{{ $cantidad }}</span>
        </button>
    @endif
</div>
