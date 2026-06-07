<div class="min-h-screen bg-white text-gray-900 font-sans" style="font-family: 'Poppins', sans-serif;">
    {{-- Hero / Header --}}
    <div class="bg-gray-900 text-white py-6 sm:py-10">
        <div class="max-w-4xl mx-auto px-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    @if($negocio->logo)
                        <img src="{{ asset('storage/' . $negocio->logo) }}" alt="{{ $negocio->nombre_negocio }}" class="h-12 sm:h-16 rounded-xl shadow-lg border-2 border-white/20 flex-shrink-0">
                    @else
                        <div class="h-12 sm:h-16 w-12 sm:w-16 bg-orange-500/20 rounded-full flex items-center justify-center border-2 border-white/20 flex-shrink-0">
                            <span class="text-2xl sm:text-3xl">🍕</span>
                        </div>
                    @endif
                    <div class="min-w-0">
                        <h1 class="text-xl sm:text-3xl font-bold tracking-tight truncate">
                            {{ $negocio->nombre_negocio ?? "Diego's Pizza" }}
                        </h1>
                        <p class="text-gray-400 mt-0.5 text-xs sm:text-sm truncate">Bienvenido a Diego's Pizza Alameda</p>
                    </div>
                </div>

                <div class="flex items-center gap-1.5 bg-gray-800 px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-full border border-gray-700 self-start sm:self-auto">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full
                            @if($estaAbierto) bg-green-400 @else bg-red-400 @endif opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5
                            @if($estaAbierto) bg-green-500 @else bg-red-500 @endif"></span>
                    </span>
                    <span class="inline sm:hidden text-xs font-semibold @if($estaAbierto) text-green-400 @else text-red-400 @endif">
                        @if($estaAbierto) ABIERTO @else CERRADO @endif
                    </span>
                    <span class="hidden sm:inline text-xs font-semibold @if($estaAbierto) text-green-400 @else text-red-400 @endif">
                        @if($estaAbierto) ABIERTO @else CERRADO @endif
                    </span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:flex-wrap gap-1.5 sm:gap-4 mt-4 text-xs sm:text-sm text-gray-400">
                <span>📍 Domicilio Solo en el Sector de Alameda Del Rio</span>
                <span>⏰ {{ \App\Models\NegocioSetting::getTodayHours()['apertura'] }} - {{ \App\Models\NegocioSetting::getTodayHours()['cierre'] }}</span>
                <span>📞 {{ $negocio->telefono ?? '(555) 1234-5678' }}</span>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="max-w-4xl mx-auto px-4 py-6">
        @if(!$estaAbierto)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4 text-center">
            <p class="text-red-700 font-medium">⏰ Actualmente estamos cerrados</p>
            <p class="text-gray-500 text-sm mt-1">Horarios: {{ \App\Models\NegocioSetting::getTodayHours()['apertura'] }} - {{ \App\Models\NegocioSetting::getTodayHours()['cierre'] }}</p>
        </div>
        @endif

        {{-- Categories Pills --}}
        <div class="flex gap-1.5 sm:gap-2 overflow-x-auto scrollbar-hide py-3 sm:py-4 sticky top-0 bg-white/95 z-20 backdrop-blur-sm">
            <button wire:click="filtrar(null)"
                class="flex-shrink-0 px-3.5 sm:px-5 py-2 sm:py-2.5 rounded-full text-xs sm:text-sm font-semibold transition-all duration-200 {{ is_null($categoriaId) ? 'bg-gray-900 text-white shadow-lg' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Todas
            </button>
            @foreach($categorias as $cat)
                <button wire:click="filtrar({{ $cat->id }})"
                    class="flex-shrink-0 px-3.5 sm:px-5 py-2 sm:py-2.5 rounded-full text-xs sm:text-sm font-semibold transition-all duration-200 {{ $categoriaId == $cat->id ? 'bg-gray-900 text-white shadow-lg' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $cat->nombre }}
                </button>
            @endforeach
        </div>

        {{-- Products Grid --}}
        @foreach($categoriasFiltradas as $categoria)
            <div class="mb-10" id="cat-{{ $categoria->id }}">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-bold text-gray-900">{{ $categoria->nombre }}</h2>
                    @if(!is_null($categoriaId))
                        <button wire:click="filtrar(null)" class="text-sm font-semibold" style="color: #FF8D08;">Ver todas</button>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($categoria->productosDisponibles as $producto)
                        @php $hasVariants = $producto->variants && $producto->variants->isNotEmpty(); @endphp
                        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-200">
                            <div class="h-36 sm:h-40 bg-gray-50 flex items-center justify-center relative overflow-hidden">
                                @if($producto->imagen)
                                    <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-6xl">🍕</span>
                                @endif
                                <span class="absolute top-3 right-3 font-bold text-sm px-3 py-1 rounded-full bg-white shadow-sm" style="color: #FF8D08;">
                                    @if($producto->es_personalizable)
                                        Mitad y Mitad
                                    @elseif($hasVariants)
                                    Desde ${{ number_format($producto->variants->min('precio'), 0, ',', '.') }}
                                    @else
                                    ${{ number_format($producto->precio, 0, ',', '.') }}
                                    @endif
                                </span>
                            </div>

                            <div class="p-4">
                                <h3 class="font-bold text-gray-900 text-base">{{ $producto->nombre }}</h3>
                                @if($producto->ingredientes)
                                    <p class="text-xs text-gray-400 mt-1.5 line-clamp-2">
                                        @foreach(explode(',', $producto->ingredientes) as $ingrediente)
                                            <span class="inline-block bg-gray-50 rounded-full px-2 py-0.5 text-xs mr-1 mb-1 text-gray-500">{{ trim($ingrediente) }}</span>
                                        @endforeach
                                    </p>
                                @endif
                                <button @if($hasVariants || $producto->es_personalizable) wire:click="seleccionarProducto({{ $producto->id }})" @else wire:click="$dispatch('productoAgregado', { productoId: {{ $producto->id }} })" @endif
                                    class="mt-3 w-full py-2.5 rounded-xl text-sm font-bold text-white transition-all duration-200 active:scale-95 shadow-sm"
                                    style="background-color: #FF8D08;"
                                    @if(!$estaAbierto) disabled @endif>
                                    @if($estaAbierto) + Agregar @else Cerrado @endif
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-10">
                            <span class="text-4xl block mb-2">😕</span>
                            <p class="text-gray-400">No hay productos en esta categoría</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    {{-- Variant Selector Modal --}}
    @if($selectedProduct)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center" wire:click.self="cerrarVariantes">
        <div class="fixed inset-0 bg-black/40" wire:click="cerrarVariantes"></div>
        <div class="relative bg-white w-full sm:max-w-sm rounded-t-2xl sm:rounded-2xl p-6 shadow-xl animate-slide-up">
            <button wire:click="cerrarVariantes" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            @if($selectedProduct->es_personalizable)
                {{-- Mitad y Mitad flavor picker --}}
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-orange-50 rounded-2xl flex items-center justify-center mx-auto mb-3 text-3xl">🍕</div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $selectedProduct->nombre }}</h3>
                    <p class="text-sm text-gray-400 mt-1">Selecciona 2 sabores</p>
                    <p class="text-xs text-gray-400 mt-1" style="color: #ea580c;">* Solo aplica para pizza de tamaño Mediana</p>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Primer sabor</label>
                        <select wire:model.live="mitad1" class="w-full border-2 border-gray-200 rounded-xl p-3 text-sm font-medium focus:border-orange-400 focus:outline-none" style="border-color: #FF8D08;">
                            <option value="">-- Seleccionar --</option>
                            @foreach($saboresPizza as $sabor)
                            <option value="{{ $sabor->id }}">{{ $sabor->nombre }} - ${{ number_format($sabor->precio, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Segundo sabor</label>
                        <select wire:model.live="mitad2" class="w-full border-2 border-gray-200 rounded-xl p-3 text-sm font-medium focus:border-orange-400 focus:outline-none" style="border-color: #FF8D08;">
                            <option value="">-- Seleccionar --</option>
                            @foreach($saboresPizza as $sabor)
                            <option value="{{ $sabor->id }}">{{ $sabor->nombre }} - ${{ number_format($sabor->precio, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($mitad1 && $mitad2)
                        @php
                            $p1 = \App\Models\Producto::find($mitad1);
                            $p2 = \App\Models\Producto::find($mitad2);
                            $v1 = $p1 ? \App\Models\ProductoVariant::where('producto_id', $p1->id)->get() : collect();
                            $v2 = $p2 ? \App\Models\ProductoVariant::where('producto_id', $p2->id)->get() : collect();
                            $m1 = $v1->first(fn($v) => stripos($v->tamanio, 'mediana') !== false || stripos($v->tamanio, 'mediano') !== false);
                            $m2 = $v2->first(fn($v) => stripos($v->tamanio, 'mediana') !== false || stripos($v->tamanio, 'mediano') !== false);
                            $pr1 = (float) ($m1?->precio ?? $v1->first()?->precio ?? $p1?->precio ?? 0);
                            $pr2 = (float) ($m2?->precio ?? $v2->first()?->precio ?? $p2?->precio ?? 0);
                            $precioFinal = max($pr1, $pr2);
                        @endphp
                        <div class="text-center py-2">
                            <span class="text-sm text-gray-500">Precio: </span>
                            <span class="text-lg font-bold" style="color: #FF8D08;">${{ number_format($precioFinal, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <button wire:click="agregarMitadYMitad" @if(!$mitad1 || !$mitad2) disabled @endif
                        class="w-full py-3 rounded-xl text-sm font-bold text-white transition-all duration-200 active:scale-95 shadow-sm"
                        style="background-color: #FF8D08; {{ !$mitad1 || !$mitad2 ? 'opacity: 50%;' : '' }}"
                        @if(!$estaAbierto) disabled @endif>
                        + Agregar al pedido
                    </button>
                </div>
            @else
                {{-- Regular variant selector --}}
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-orange-50 rounded-2xl flex items-center justify-center mx-auto mb-3 text-3xl">🍕</div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $selectedProduct->nombre }}</h3>
                    <p class="text-sm text-gray-400 mt-1">Selecciona un tamaño</p>
                </div>
                <div class="space-y-3">
                    @foreach($selectedProduct->variants as $variant)
                        <button wire:click="agregarConVariante({{ $selectedProduct->id }}, {{ $variant->id }})"
                            class="w-full flex items-center justify-between p-4 rounded-xl border-2 transition-all duration-200 active:scale-[0.98]"
                            style="border-color: #FF8D08;"
                            @if(!$estaAbierto) disabled @endif>
                            <span class="font-bold text-gray-900">{{ $variant->tamanio }}</span>
                            <span class="font-bold" style="color: #FF8D08;">${{ number_format($variant->precio, 0, ',', '.') }}</span>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Footer --}}
    <div class="bg-gray-900 text-gray-400 mt-12 py-8">
        <div class="max-w-4xl mx-auto px-4 text-center text-sm">
            <p class="text-white font-bold text-base sm:text-lg">{{ $negocio->nombre_negocio ?? "Diego's Pizza" }}</p>
            <p class="mt-1 text-xs sm:text-sm">Bienvenido a Diego's Pizza Alameda</p>
            <div class="flex flex-col sm:flex-row sm:justify-center gap-1.5 sm:gap-6 mt-4 text-xs">
                <span>📍 Domicilio Solo en el Sector de Alameda Del Rio</span>
                <span>⏰ {{ \App\Models\NegocioSetting::getTodayHours()['apertura'] }} - {{ \App\Models\NegocioSetting::getTodayHours()['cierre'] }}</span>
                <span>📞 {{ $negocio->telefono ?? '(555) 1234-5678' }}</span>
            </div>
            <p class="mt-6 text-xs opacity-50">© {{ date('Y') }} {{ $negocio->nombre_negocio ?? "Diego's Pizza" }}. Todos los derechos reservados.</p>
        </div>
    </div>
</div>
