<div class="space-y-4">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Tablero de Pedidos</h1>
            <p class="text-sm text-gray-500 mt-1">Gestiona los pedidos en tiempo real</p>
        </div>
        <div class="flex gap-1 text-xs text-gray-400">
            <span class="bg-gray-100 px-2 py-1 rounded">🔄 Actualizado</span>
        </div>
    </div>

    {{-- Board --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Columna: Recibidos --}}
        <div class="bg-gray-50 rounded-xl p-4 min-h-[300px]">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-gray-600 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-yellow-400 inline-block"></span>
                    Recibidos
                </h2>
                <span class="text-xs bg-yellow-100 text-yellow-800 font-bold px-2 py-0.5 rounded-full">{{ count($pendientes) }}</span>
            </div>
            <div class="space-y-3">
                @forelse($pendientes as $pedido)
                    <div class="bg-white rounded-lg shadow-sm border border-yellow-100 p-3 hover:shadow-md transition cursor-pointer" onclick="Livewire.navigate('{{ url("/admin/pedidos/{$pedido['id']}/edit") }}')">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <span class="text-xs font-bold text-gray-400">#{{ $pedido['numero_pedido'] }}</span>
                                <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pedido['cliente']['nombre'] ?? '—' }}</p>
                            </div>
                            <span class="text-xs font-bold text-red-600">${{ number_format($pedido['total'], 0, ',', '.') }}</span>
                        </div>
                        <p class="text-xs text-gray-500">📱 {{ $pedido['cliente']['telefono'] ?? '—' }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">📍 {{ Str::limit(collect([$pedido['cliente']['conjunto'] ?? '', $pedido['cliente']['torre'] ?? '', $pedido['cliente']['apto'] ?? ''])->filter()->implode(', ') ?: '—', 50) }}</p>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-50">
                            <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($pedido['created_at'], 'UTC')->setTimezone('America/Bogota')->diffForHumans() }}</span>
                            <div class="flex gap-1">
                                <button wire:click="cancelarPedido({{ $pedido['id'] }})" class="text-xs text-red-500 hover:text-red-700 px-2 py-1 rounded hover:bg-red-50 font-medium">Cancelar</button>
                                <button wire:click="cambiarEstado({{ $pedido['id'] }}, 'en_proceso')" class="text-xs text-white bg-yellow-500 hover:bg-yellow-600 px-2.5 py-1 rounded font-medium">Preparar</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-400 text-sm py-8">✅ Sin pedidos pendientes</p>
                @endforelse
            </div>
        </div>

        {{-- Columna: Preparación --}}
        <div class="bg-gray-50 rounded-xl p-4 min-h-[300px]">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-gray-600 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-orange-400 inline-block"></span>
                    Preparación
                </h2>
                <span class="text-xs bg-orange-100 text-orange-800 font-bold px-2 py-0.5 rounded-full">{{ count($enProceso) }}</span>
            </div>
            <div class="space-y-3">
                @forelse($enProceso as $pedido)
                    <div class="bg-white rounded-lg shadow-sm border border-orange-100 p-3 hover:shadow-md transition cursor-pointer" onclick="Livewire.navigate('{{ url("/admin/pedidos/{$pedido['id']}/edit") }}')">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <span class="text-xs font-bold text-gray-400">#{{ $pedido['numero_pedido'] }}</span>
                                <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pedido['cliente']['nombre'] ?? '—' }}</p>
                            </div>
                            <span class="text-xs font-bold text-red-600">${{ number_format($pedido['total'], 0, ',', '.') }}</span>
                        </div>
                        <p class="text-xs text-gray-500">📱 {{ $pedido['cliente']['telefono'] ?? '—' }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">📍 {{ Str::limit(collect([$pedido['cliente']['conjunto'] ?? '', $pedido['cliente']['torre'] ?? '', $pedido['cliente']['apto'] ?? ''])->filter()->implode(', ') ?: '—', 50) }}</p>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-50">
                            <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($pedido['created_at'], 'UTC')->setTimezone('America/Bogota')->diffForHumans() }}</span>
                            <div class="flex gap-1">
                                <button wire:click="cambiarEstado({{ $pedido['id'] }}, 'pendiente')" class="text-xs text-gray-500 hover:text-gray-700 px-2 py-1 rounded hover:bg-gray-100 font-medium">↩</button>
                                <button wire:click="cambiarEstado({{ $pedido['id'] }}, 'en_camino')" class="text-xs text-white bg-orange-500 hover:bg-orange-600 px-2.5 py-1 rounded font-medium">Enviar</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-400 text-sm py-8">⏳ Sin pedidos en preparación</p>
                @endforelse
            </div>
        </div>

        {{-- Columna: En Camino --}}
        <div class="bg-gray-50 rounded-xl p-4 min-h-[300px]">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-gray-600 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-400 inline-block"></span>
                    En Camino
                </h2>
                <span class="text-xs bg-green-100 text-green-800 font-bold px-2 py-0.5 rounded-full">{{ count($enCamino) }}</span>
            </div>
            <div class="space-y-3">
                @forelse($enCamino as $pedido)
                    <div class="bg-white rounded-lg shadow-sm border border-green-100 p-3 hover:shadow-md transition cursor-pointer" onclick="Livewire.navigate('{{ url("/admin/pedidos/{$pedido['id']}/edit") }}')">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <span class="text-xs font-bold text-gray-400">#{{ $pedido['numero_pedido'] }}</span>
                                <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pedido['cliente']['nombre'] ?? '—' }}</p>
                            </div>
                            <span class="text-xs font-bold text-red-600">${{ number_format($pedido['total'], 0, ',', '.') }}</span>
                        </div>
                        <p class="text-xs text-gray-500">📱 {{ $pedido['cliente']['telefono'] ?? '—' }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">📍 {{ Str::limit(collect([$pedido['cliente']['conjunto'] ?? '', $pedido['cliente']['torre'] ?? '', $pedido['cliente']['apto'] ?? ''])->filter()->implode(', ') ?: '—', 50) }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">💳 {{ ucfirst($pedido['metodo_pago'] ?? '—') }}</p>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-50">
                            <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($pedido['created_at'], 'UTC')->setTimezone('America/Bogota')->diffForHumans() }}</span>
                            <button wire:click="cambiarEstado({{ $pedido['id'] }}, 'entregado')" class="text-xs text-white bg-green-500 hover:bg-green-600 px-2.5 py-1 rounded font-medium">Entregado</button>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-400 text-sm py-8">🛵 Sin pedidos en camino</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
