<div class="min-h-screen bg-white text-gray-900 font-sans" style="font-family: 'Poppins', sans-serif;">
    {{-- Hero / Header --}}
    <div class="relative text-white overflow-hidden bg-gray-900" style="background-image: linear-gradient(to bottom, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.6) 50%, rgba(0,0,0,0.5) 100%), url('/storage/images/llanera.jpeg'); background-size: cover; background-position: center;">
        <div class="max-w-4xl mx-auto px-4 py-6 sm:py-10 text-center">
            <div class="flex flex-col items-center gap-3">
                @if($negocio->logo)
                    <img src="{{ asset('storage/' . $negocio->logo) }}" alt="{{ $negocio->nombre_negocio }}" class="h-14 sm:h-20 rounded-xl shadow-lg border-2 border-white/20">
                @else
                    <div class="h-14 sm:h-20 w-14 sm:w-20 bg-orange-500/20 rounded-full flex items-center justify-center border-2 border-white/20">
                        <span class="text-2xl sm:text-4xl">🍕</span>
                    </div>
                @endif
                <div>
                    <h1 class="text-xl sm:text-3xl font-bold drop-shadow-lg">
                        {{ $negocio->nombre_negocio ?? "Diego's Pizza" }}
                    </h1>
                    <p class="text-gray-300 text-xs sm:text-sm mt-0.5 drop-shadow">Bienvenido a Diego's Pizza Alameda</p>
                </div>

                <div class="flex items-center gap-1.5 bg-black/50 backdrop-blur-sm px-3 py-1.5 rounded-full border border-white/20">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full @if($estaAbierto) bg-green-400 @else bg-red-400 @endif opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 @if($estaAbierto) bg-green-500 @else bg-red-500 @endif"></span>
                    </span>
                    <span class="text-xs font-semibold @if($estaAbierto) text-green-400 @else text-red-400 @endif">
                        @if($estaAbierto) ABIERTO @else CERRADO @endif
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-x-2.5 gap-y-1 mt-4 sm:mt-5 text-xs text-gray-300">
                <span>📍 Alameda Del Rio</span>
                <span class="text-gray-500 hidden sm:inline">|</span>
                <span>⏰ {{ date('g:i A', strtotime(\App\Models\NegocioSetting::getTodayHours()['apertura'])) }} - {{ date('g:i A', strtotime(\App\Models\NegocioSetting::getTodayHours()['cierre'])) }}</span>
                <span class="text-gray-500 hidden sm:inline">|</span>
                <span class="hidden sm:inline">📞 {{ $negocio->telefono ?? '(555) 1234-5678' }}</span>
                <div class="flex items-center gap-1.5">
                    <a href="https://wa.me/573106444759?text=Hola%2C%20quisiera%20m%C3%A1s%20informaci%C3%B3n%20sobre%20sus%20productos" target="_blank" rel="noopener" class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/15 hover:bg-white/25 transition-all duration-200 border border-white/20 hover:border-white/40" title="Escríbenos por WhatsApp">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>
                    <a href="https://www.instagram.com/diegospizzabq/" target="_blank" rel="noopener" class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/15 hover:bg-white/25 transition-all duration-200 border border-white/20 hover:border-white/40" title="Síguenos en Instagram">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="max-w-4xl mx-auto px-4 py-6">
        @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4 text-center">
            <p class="text-red-700 font-medium">{{ session('error') }}</p>
        </div>
        @endif

        @if(\App\Models\NegocioSetting::isPaused())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4 text-center">
            <p class="text-yellow-700 font-medium">🟡 Estamos en pausa por alta demanda</p>
            <p class="text-gray-500 text-sm mt-1">Estamos trabajando para atenderte pronto. Vuelve a intentar más tarde.</p>
        </div>
        @endif

        @if(!$estaAbierto)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4 text-center">
            <p class="text-red-700 font-medium">⏰ Actualmente estamos cerrados</p>
            <p class="text-gray-500 text-sm mt-1">Horarios: {{ date('g:i A', strtotime(\App\Models\NegocioSetting::getTodayHours()['apertura'])) }} - {{ date('g:i A', strtotime(\App\Models\NegocioSetting::getTodayHours()['cierre'])) }}</p>
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

        {{-- Destacados / Ofertas --}}
        @if($destacados->isNotEmpty() && is_null($categoriaId))
            <div class="mb-10">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        @if($tieneDescuentos ?? false)
                            <span style="background:#dc2626;color:white;font-size:11px;font-weight:800;padding:2px 8px;border-radius:4px;">OFERTAS</span>
                        @else
                            ⭐
                        @endif
                        {{ $tieneDescuentos ? 'Ofertas' : 'Los más pedidos' }}
                    </h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($destacados as $producto)
                        @php
                            $hasVariants = $producto->variants && $producto->variants->isNotEmpty();
                            $desc = $descuentosMap['porProducto'][$producto->id] ?? $descuentosMap['porCategoria'][$producto->categoria_id] ?? null;
                        @endphp
                        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-200 flex flex-col @if($desc) ring-2 ring-red-200 @endif">
                            <div class="h-36 sm:h-40 bg-gray-50 flex items-center justify-center relative overflow-hidden flex-shrink-0">
                                @if($desc)
                                    <div style="position:absolute;top:10px;left:0;background:#dc2626;color:white;font-size:11px;font-weight:800;padding:3px 10px 3px 8px;border-radius:0 6px 6px 0;z-index:2;box-shadow:0 2px 4px rgba(0,0,0,0.2);">
                                        {{ $desc->getLabel() }}
                                    </div>
                                @endif
                                @if($producto->imagen)
                                    <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-6xl">🍕</span>
                                @endif
                                <span class="absolute top-3 right-3 font-bold text-sm px-3 py-1 rounded-full bg-white shadow-sm" style="color: #FF8D08;">
                                    @if($desc)
                                        <span style="text-decoration:line-through;color:#9ca3af;margin-right:4px;">
                                            @if($hasVariants)
                                                ${{ number_format($producto->variants->min('precio'), 0, ',', '.') }}
                                            @else
                                                ${{ number_format($producto->precio, 0, ',', '.') }}
                                            @endif
                                        </span>
                                        ${{ number_format($desc->calcularPrecio((float) ($hasVariants ? $producto->variants->min('precio') : $producto->precio)), 0, ',', '.') }}
                                    @elseif($producto->es_personalizable)
                                        Mitad y Mitad
                                    @elseif($hasVariants)
                                    Desde ${{ number_format($producto->variants->min('precio'), 0, ',', '.') }}
                                    @else
                                    ${{ number_format($producto->precio, 0, ',', '.') }}
                                    @endif
                                </span>
                            </div>
                            <div class="p-4 flex flex-col flex-1">
                                <h3 class="font-bold text-gray-900 text-base">{{ $producto->nombre }}</h3>
                                @if($producto->ingredientes)
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-400 mt-1.5 line-clamp-2">
                                            @foreach(explode(',', $producto->ingredientes) as $ingrediente)
                                                <span class="inline-block bg-gray-50 rounded-full px-2 py-0.5 text-xs mr-1 mb-1 text-gray-500">{{ trim($ingrediente) }}</span>
                                            @endforeach
                                        </p>
                                    </div>
                                @else
                                    <div class="flex-1"></div>
                                @endif
                                <button @if($hasVariants || $producto->es_personalizable) wire:click="seleccionarProducto({{ $producto->id }})" @else wire:click="$dispatch('productoAgregado', { productoId: {{ $producto->id }} })" @endif
                                    class="mt-3 w-full py-2.5 rounded-xl text-sm font-bold text-white transition-all duration-200 active:scale-95 shadow-sm flex-shrink-0"
                                    style="background-color: #FF8D08;"
                                    @if(!$estaAbierto) disabled @endif>
                                    @if($estaAbierto) + Agregar @else Cerrado @endif
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

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
                        @php
                            $hasVariants = $producto->variants && $producto->variants->isNotEmpty();
                            $desc = $descuentosMap['porProducto'][$producto->id] ?? $descuentosMap['porCategoria'][$producto->categoria_id] ?? null;
                        @endphp
                        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-200 flex flex-col">
                            <div class="h-36 sm:h-40 bg-gray-50 flex items-center justify-center relative overflow-hidden flex-shrink-0">
                                @if($desc)
                                    <div style="position:absolute;top:10px;left:0;background:#dc2626;color:white;font-size:11px;font-weight:800;padding:3px 10px 3px 8px;border-radius:0 6px 6px 0;z-index:2;box-shadow:0 2px 4px rgba(0,0,0,0.2);">
                                        {{ $desc->getLabel() }}
                                    </div>
                                @endif
                                @if($producto->imagen)
                                    <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-6xl">🍕</span>
                                @endif
                                <span class="absolute top-3 right-3 font-bold text-sm px-3 py-1 rounded-full bg-white shadow-sm" style="color: #FF8D08;">
                                    @if($desc)
                                        <span style="text-decoration:line-through;color:#9ca3af;margin-right:4px;">
                                            @if($hasVariants)
                                                ${{ number_format($producto->variants->min('precio'), 0, ',', '.') }}
                                            @else
                                                ${{ number_format($producto->precio, 0, ',', '.') }}
                                            @endif
                                        </span>
                                        ${{ number_format($desc->calcularPrecio((float) ($hasVariants ? $producto->variants->min('precio') : $producto->precio)), 0, ',', '.') }}
                                    @elseif($producto->es_personalizable)
                                        Mitad y Mitad
                                    @elseif($hasVariants)
                                    Desde ${{ number_format($producto->variants->min('precio'), 0, ',', '.') }}
                                    @else
                                    ${{ number_format($producto->precio, 0, ',', '.') }}
                                    @endif
                                </span>
                            </div>

                            <div class="p-4 flex flex-col flex-1">
                                <h3 class="font-bold text-gray-900 text-base">{{ $producto->nombre }}</h3>
                                @if($producto->ingredientes)
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-400 mt-1.5 line-clamp-2">
                                            @foreach(explode(',', $producto->ingredientes) as $ingrediente)
                                                <span class="inline-block bg-gray-50 rounded-full px-2 py-0.5 text-xs mr-1 mb-1 text-gray-500">{{ trim($ingrediente) }}</span>
                                            @endforeach
                                        </p>
                                    </div>
                                @else
                                    <div class="flex-1"></div>
                                @endif
                                <button @if($hasVariants || $producto->es_personalizable) wire:click="seleccionarProducto({{ $producto->id }})" @else wire:click="$dispatch('productoAgregado', { productoId: {{ $producto->id }} })" @endif
                                    class="mt-3 w-full py-2.5 rounded-xl text-sm font-bold text-white transition-all duration-200 active:scale-95 shadow-sm flex-shrink-0"
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
                <span>⏰ {{ date('g:i A', strtotime(\App\Models\NegocioSetting::getTodayHours()['apertura'])) }} - {{ date('g:i A', strtotime(\App\Models\NegocioSetting::getTodayHours()['cierre'])) }}</span>
                <span>📞 {{ $negocio->telefono ?? '(555) 1234-5678' }}</span>
            </div>
            <p class="mt-6 text-xs opacity-50">© {{ date('Y') }} {{ $negocio->nombre_negocio ?? "Diego's Pizza" }}. Todos los derechos reservados.</p>
        </div>
    </div>
</div>
