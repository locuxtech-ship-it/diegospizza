@extends('layouts.landing')

@section('content')

    {{-- Nav sticky --}}
    <nav class="fixed top-0 left-0 right-0 bg-white/95 backdrop-blur-sm border-b border-gray-100 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2.5">
                <img src="{{ asset('icons/facilo-icon.svg') }}" alt="Facilo" class="h-9 w-9">
                <span class="text-xl font-extrabold text-gray-900 tracking-tight">Facilo</span>
            </a>
            <div class="flex items-center gap-4">
                <a href="/admin/login" class="text-sm font-semibold text-gray-600 hover:text-[#F1590C] transition-colors hidden sm:block">Iniciar sesión</a>
                <a href="{{ route('store.register') }}" class="bg-[#F1590C] text-white text-sm font-bold px-5 py-2.5 rounded-xl hover:bg-[#D94D0A] transition-all shadow-sm">¡Empezar ahora!</a>
            </div>
        </div>
    </nav>

    {{-- Hero: fondo claro, texto izquierda, mockup derecha (estilo OlaClick) --}}
    <section class="relative pt-16 overflow-hidden" style="background-color: #FFF3EB;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-2 gap-10 items-center min-h-[520px]">
                <div class="py-10 lg:py-20">
                    <h1 class="text-4xl sm:text-5xl lg:text-[56px] font-extrabold text-[#1a1a2e] leading-[1.08] mb-5">
                        Automatiza tu restaurante <span class="text-[#F1590C]">sin pagar de más</span>
                    </h1>
                    <p class="text-lg text-gray-500 leading-relaxed mb-8 max-w-md">
                        Menú digital, punto de venta y pedidos por WhatsApp desde <strong class="text-gray-900">$0/mes</strong>. Hecho para restaurantes que recién empiezan a automatizar, no para grandes cadenas.
                    </p>
                    <a href="{{ route('store.register') }}" class="inline-flex items-center gap-2 bg-[#F1590C] text-white font-extrabold px-8 py-4 rounded-xl text-base uppercase tracking-wide hover:bg-[#D94D0A] transition-all shadow-lg shadow-[#F1590C]/20">
                        Empezar gratis
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <div class="flex items-center gap-2 mt-6 text-sm text-gray-500">
                        <span class="text-[#F1590C]">★</span>
                        <span>Un sistema creado desde la experiencia real de atender un restaurante, no desde un escritorio corporativo.</span>
                    </div>
                </div>
                <div class="hidden lg:block">
                    <div class="relative">
                        {{-- Panel mockup --}}
                        <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
                            <div class="bg-gray-50 px-4 py-2.5 flex items-center gap-2 border-b border-gray-100">
                                <div class="flex gap-1.5">
                                    <div class="w-2.5 h-2.5 rounded-full bg-red-400"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-yellow-400"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-green-400"></div>
                                </div>
                                <div class="flex-1 text-center text-[11px] text-gray-400 font-medium">panel.facilo.app</div>
                            </div>
                            <div class="p-5">
                                <div class="flex items-center justify-between mb-5">
                                    <div>
                                        <div class="font-bold text-gray-900 text-sm">Buenos días 👋</div>
                                        <div class="text-xs text-gray-400">Resumen de hoy</div>
                                    </div>
                                    <div class="bg-green-100 text-green-700 text-[11px] font-bold px-2.5 py-1 rounded-full">● Abierto</div>
                                </div>
                                <div class="grid grid-cols-3 gap-2.5 mb-5">
                                    <div class="bg-[#F1590C]/5 rounded-xl p-3 text-center">
                                        <div class="text-xl font-black text-[#F1590C]">$2.4M</div>
                                        <div class="text-[10px] text-gray-400 mt-0.5">Ventas hoy</div>
                                    </div>
                                    <div class="bg-blue-50 rounded-xl p-3 text-center">
                                        <div class="text-xl font-black text-blue-600">47</div>
                                        <div class="text-[10px] text-gray-400 mt-0.5">Pedidos</div>
                                    </div>
                                    <div class="bg-green-50 rounded-xl p-3 text-center">
                                        <div class="text-xl font-black text-green-600">12</div>
                                        <div class="text-[10px] text-gray-400 mt-0.5">Pendientes</div>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between bg-gray-50 rounded-xl px-3 py-2.5">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 bg-orange-100 rounded-lg flex items-center justify-center text-xs">🍕</div>
                                            <div>
                                                <div class="text-xs font-semibold text-gray-900">#47 — Mariana López</div>
                                                <div class="text-[10px] text-gray-400">2x Pizza Margarita · $38.000</div>
                                            </div>
                                        </div>
                                        <span class="text-[10px] font-bold text-yellow-600 bg-yellow-100 px-2 py-0.5 rounded-full">Preparando</span>
                                    </div>
                                    <div class="flex items-center justify-between bg-gray-50 rounded-xl px-3 py-2.5">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 bg-orange-100 rounded-lg flex items-center justify-center text-xs">🍔</div>
                                            <div>
                                                <div class="text-xs font-semibold text-gray-900">#46 — Carlos Ruiz</div>
                                                <div class="text-[10px] text-gray-400">1x Doble Queso · $22.000</div>
                                            </div>
                                        </div>
                                        <span class="text-[10px] font-bold text-green-600 bg-green-100 px-2 py-0.5 rounded-full">Listo</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Floating notification --}}
                        <div class="absolute -top-3 -right-3 bg-white rounded-xl shadow-lg border border-gray-100 px-3 py-2.5 flex items-center gap-2">
                            <span class="text-lg">🔔</span>
                            <div>
                                <div class="text-[11px] font-bold text-gray-900">¡Nuevo pedido!</div>
                                <div class="text-[10px] text-gray-400">WhatsApp · Hace 5s</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats bar --}}
    <section class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
            <h2 class="text-center text-xl sm:text-2xl font-extrabold text-[#1a1a2e] mb-10">¿Por qué los pequeños restaurantes eligen Facilo?</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-[#F1590C]/10 rounded-2xl flex items-center justify-center flex-shrink-0 text-xl">🍽️</div>
                    <div>
                        <div class="font-bold text-gray-900 text-sm mb-0.5">Todo en uno</div>
                        <div class="text-xs text-gray-400 leading-relaxed">Menú, pedidos y WhatsApp en una sola plataforma</div>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-[#F1590C]/10 rounded-2xl flex items-center justify-center flex-shrink-0 text-xl">💰</div>
                    <div>
                        <div class="font-bold text-gray-900 text-sm mb-0.5">Sin grandes desembolsos</div>
                        <div class="text-xs text-gray-400 leading-relaxed">Desde $0/mes y sin comisiones por pedido</div>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-[#F1590C]/10 rounded-2xl flex items-center justify-center flex-shrink-0 text-xl">🤖</div>
                    <div>
                        <div class="font-bold text-gray-900 text-sm mb-0.5">Automatización real</div>
                        <div class="text-xs text-gray-400 leading-relaxed">Bot que recibe pedidos y notifica estados</div>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-[#F1590C]/10 rounded-2xl flex items-center justify-center flex-shrink-0 text-xl">⚡</div>
                    <div>
                        <div class="font-bold text-gray-900 text-sm mb-0.5">Hecho para tu tamaño</div>
                        <div class="text-xs text-gray-400 leading-relaxed">Simple de usar, sin equipos ni costos extra</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features grid (6 cards) --}}
    <section class="py-20 sm:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <h2 class="text-center text-3xl sm:text-4xl font-extrabold text-[#1a1a2e] mb-4">¡Maneja todo desde un solo lugar!</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-12">
                {{-- Menú digital --}}
                <div class="bg-gray-50 rounded-3xl p-7 group hover:bg-[#F1590C]/5 transition-colors duration-300">
                    <div class="w-12 h-12 bg-[#F1590C]/10 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-[#F1590C]/20 transition-colors text-xl">🎨</div>
                    <h3 class="font-bold text-gray-900 mb-1">Menú digital</h3>
                    <p class="text-xs text-gray-400 mb-4">Tus clientes piden 24/7</p>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Categorías y filtros
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Tamaños con precio propio
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Combina 2 sabores en una pizza
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Abierto/cerrado por horarios
                        </li>
                    </ul>
                </div>
                {{-- Punto de venta --}}
                <div class="bg-gray-50 rounded-3xl p-7 group hover:bg-[#F1590C]/5 transition-colors duration-300">
                    <div class="w-12 h-12 bg-[#F1590C]/10 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-[#F1590C]/20 transition-colors text-xl">🍽️</div>
                    <h3 class="font-bold text-gray-900 mb-1">Toma de pedidos</h3>
                    <p class="text-xs text-gray-400 mb-4">No pierdas ningún pedido</p>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Pedidos organizados por estado
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Pagos por partes o combinados
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Descuentos manuales
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Orden manual desde mostrador
                        </li>
                    </ul>
                </div>
                {{-- Bot WhatsApp --}}
                <div class="bg-gray-50 rounded-3xl p-7 group hover:bg-[#F1590C]/5 transition-colors duration-300">
                    <div class="w-12 h-12 bg-[#F1590C]/10 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-[#F1590C]/20 transition-colors text-xl">💬</div>
                    <h3 class="font-bold text-gray-900 mb-1">Bot de WhatsApp</h3>
                    <p class="text-xs text-gray-400 mb-4">Automatiza la atención</p>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Mensaje de bienvenida propio
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Menú de opciones por teclas
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Notificaciones de estado del pedido
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Solicitar reseñas post-pedido
                        </li>
                    </ul>
                </div>
                {{-- Cierre de caja --}}
                <div class="bg-gray-50 rounded-3xl p-7 group hover:bg-[#F1590C]/5 transition-colors duration-300">
                    <div class="w-12 h-12 bg-[#F1590C]/10 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-[#F1590C]/20 transition-colors text-xl">💵</div>
                    <h3 class="font-bold text-gray-900 mb-1">Cierre de caja</h3>
                    <p class="text-xs text-gray-400 mb-4">Controla tu efectivo del día</p>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Totales por método de pago
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Registro de gastos del día
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Efectivo esperado vs real
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Diferencia y observaciones
                        </li>
                    </ul>
                </div>
                {{-- Reportes --}}
                <div class="bg-gray-50 rounded-3xl p-7 group hover:bg-[#F1590C]/5 transition-colors duration-300">
                    <div class="w-12 h-12 bg-[#F1590C]/10 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-[#F1590C]/20 transition-colors text-xl">📈</div>
                    <h3 class="font-bold text-gray-900 mb-1">Reportes</h3>
                    <p class="text-xs text-gray-400 mb-4">Decide con datos reales</p>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Ventas por día / periodo
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Horas pico y día más vendido
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Productos y tamaños más vendidos
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Promedio por pedido
                        </li>
                    </ul>
                </div>
                {{-- Fidelidad --}}
                <div class="bg-gray-50 rounded-3xl p-7 group hover:bg-[#F1590C]/5 transition-colors duration-300">
                    <div class="w-12 h-12 bg-[#F1590C]/10 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-[#F1590C]/20 transition-colors text-xl">🎁</div>
                    <h3 class="font-bold text-gray-900 mb-1">Fidelidad y puntos</h3>
                    <p class="text-xs text-gray-400 mb-4">Retén a tus clientes</p>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Puntos por cada compra
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Recompensas canjeables (descuento)
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Descuento automático al pagar
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Detección de cliente por teléfono
                        </li>
                    </ul>
                </div>
                {{-- Clientes --}}
                <div class="bg-gray-50 rounded-3xl p-7 group hover:bg-[#F1590C]/5 transition-colors duration-300">
                    <div class="w-12 h-12 bg-[#F1590C]/10 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-[#F1590C]/20 transition-colors text-xl">👥</div>
                    <h3 class="font-bold text-gray-900 mb-1">Gestión de clientes</h3>
                    <p class="text-xs text-gray-400 mb-4">Conoce a tus clientes</p>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Ficha con dirección de envío
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Historial de pedidos por cliente
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Puntos acumulados por cliente
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Notas y clasificación
                        </li>
                    </ul>
                </div>
                {{-- Reseñas --}}
                <div class="bg-gray-50 rounded-3xl p-7 group hover:bg-[#F1590C]/5 transition-colors duration-300">
                    <div class="w-12 h-12 bg-[#F1590C]/10 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-[#F1590C]/20 transition-colors text-xl">⭐</div>
                    <h3 class="font-bold text-gray-900 mb-1">Reseñas de clientes</h3>
                    <p class="text-xs text-gray-400 mb-4">Mejora tu reputación</p>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Calificación 1 a 5 estrellas
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Comentario de los clientes
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Reseña por pedido (enlace WhatsApp)
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Moderar qué reseñas se muestran
                        </li>
                    </ul>
                </div>
                {{-- Pedidos online + impresión --}}
                <div class="bg-gray-50 rounded-3xl p-7 group hover:bg-[#F1590C]/5 transition-colors duration-300">
                    <div class="w-12 h-12 bg-[#F1590C]/10 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-[#F1590C]/20 transition-colors text-xl">🖨️</div>
                    <h3 class="font-bold text-gray-900 mb-1">Pedidos online y comprobantes</h3>
                    <p class="text-xs text-gray-400 mb-4">Del pedido a tu cocina</p>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Carrito y pedido en línea
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Comprobante de pedido imprimible
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Impresión automática de pedidos
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Historial de pedidos
                        </li>
                    </ul>
                </div>
                {{-- Multi-usuario + pagos --}}
                <div class="bg-gray-50 rounded-3xl p-7 group hover:bg-[#F1590C]/5 transition-colors duration-300">
                    <div class="w-12 h-12 bg-[#F1590C]/10 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-[#F1590C]/20 transition-colors text-xl">👥</div>
                    <h3 class="font-bold text-gray-900 mb-1">Varios usuarios y pagos</h3>
                    <p class="text-xs text-gray-400 mb-4">Control y flexibilidad</p>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Dueño y empleados con permisos
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Efectivo, tarjeta y transferencia
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Billeteras digitales (Daviplata, Nequi)
                        </li>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Métodos de pago a tu gusto
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Sección producto: Menú digital (alternada, sin imagen real) --}}
    <section class="py-20 sm:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-[#1a1a2e] mb-4">Tu menú digital, listo para recibir pedidos</h2>
                    <p class="text-gray-500 leading-relaxed mb-6">Publica tu carta con categorías, fotos, precios y tamaños. Tus clientes piden desde el celular el día que esté abierto.</p>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Categorías y productos con foto
                        </li>
                        <li class="flex items-center gap-3 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Tamaños (mediana/familiar) con precio
                        </li>
                        <li class="flex items-center gap-3 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Combina 2 sabores en una pizza
                        </li>
                        <li class="flex items-center gap-3 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Carrito y pago con datos de envío
                        </li>
                        <li class="flex items-center gap-3 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Horarios de apertura y cierre
                        </li>
                    </ul>
                </div>
                <div class="flex justify-center">
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden w-full max-w-sm">
                        <div class="h-44 bg-gradient-to-br from-[#F1590C] to-[#FF8C42] flex items-center justify-center relative">
                            <span class="text-6xl">🍕</span>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center justify-between mb-4">
                                <div class="font-bold text-gray-900">Menú Digital</div>
                                <span class="text-xs text-green-600 bg-green-50 px-2 py-0.5 rounded-full font-bold">● Abierto</span>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-3">
                                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-xl">🍕</div>
                                    <div class="flex-1">
                                        <div class="text-sm font-bold text-gray-900">Pizza Margarita</div>
                                        <div class="text-xs text-gray-400">Queso mozzarella, tomate, albahaca</div>
                                    </div>
                                    <div class="text-sm font-bold text-[#F1590C]">$19.000</div>
                                </div>
                                <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-3">
                                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-xl">🍔</div>
                                    <div class="flex-1">
                                        <div class="text-sm font-bold text-gray-900">Doble Queso</div>
                                        <div class="text-xs text-gray-400">Doble carne, queso cheddar</div>
                                    </div>
                                    <div class="text-sm font-bold text-[#F1590C]">$22.000</div>
                                </div>
                                <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-3">
                                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-xl">🥤</div>
                                    <div class="flex-1">
                                        <div class="text-sm font-bold text-gray-900">Limonada Natural</div>
                                        <div class="text-xs text-gray-400">Limón fresco, hielo</div>
                                    </div>
                                    <div class="text-sm font-bold text-[#F1590C]">$8.000</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Sección producto: WhatsApp Chatbot --}}
    <section class="py-20 sm:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="flex justify-center">
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-5 w-full max-w-xs">
                        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-100">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-900">Tu Restaurante</div>
                                <div class="text-xs text-green-500">en línea</div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="bg-green-50 rounded-xl rounded-tl-none px-3 py-2 max-w-[85%]">
                                <div class="text-xs text-gray-700">¡Hola! ¿Qué deseas ordenar?</div>
                                <div class="text-[10px] text-gray-400 mt-1">10:30 AM</div>
                            </div>
                            <div class="bg-gray-100 rounded-xl rounded-tr-none px-3 py-2 max-w-[85%] ml-auto">
                                <div class="text-xs text-gray-700">Quiero 2x Pizza Margarita 🍕</div>
                                <div class="text-[10px] text-gray-400 mt-1 text-right">10:31 AM</div>
                            </div>
                            <div class="bg-green-50 rounded-xl rounded-tl-none px-3 py-2 max-w-[85%]">
                                <div class="text-xs text-gray-700">¡Perfecto! $38.000. ¿Confirmas?</div>
                                <div class="text-[10px] text-gray-400 mt-1">10:31 AM</div>
                            </div>
                            <div class="bg-gray-100 rounded-xl rounded-tr-none px-3 py-2 max-w-[85%] ml-auto">
                                <div class="text-xs text-gray-700">¡Confirmo! 👍</div>
                                <div class="text-[10px] text-gray-400 mt-1 text-right">10:32 AM</div>
                            </div>
                            <div class="bg-green-50 rounded-xl rounded-tl-none px-3 py-2 max-w-[85%]">
                                <div class="text-xs text-gray-700">¡Listo! Pedido #47 en preparación. Te notificamos 🛵</div>
                                <div class="text-[10px] text-gray-400 mt-1">10:32 AM</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-[#1a1a2e] mb-4">Automatiza tu atención con un bot de WhatsApp</h2>
                    <p class="text-gray-500 leading-relaxed mb-6">Tu negocio en WhatsApp: recibe pedidos, notifica a tus clientes el estado y solicita reseñas, todo automáticamente.</p>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Mensaje de bienvenida personalizado
                        </li>
                        <li class="flex items-center gap-3 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Menú de opciones por teclas
                        </li>
                        <li class="flex items-center gap-3 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Notificación automática del estado del pedido
                        </li>
                        <li class="flex items-center gap-3 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Solicitud de reseña tras el pedido
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Fidelidad, reseñas y reportes --}}
    <section class="py-20 sm:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <h2 class="text-center text-3xl sm:text-4xl font-extrabold text-[#1a1a2e] mb-12">Herramientas para fidelizar y crecer</h2>
            <div class="grid sm:grid-cols-3 gap-8">
                <div class="bg-white rounded-3xl p-7 border border-gray-100 text-center">
                    <div class="w-14 h-14 bg-[#F1590C]/10 rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl">🎁</div>
                    <h3 class="font-bold text-gray-900 mb-2">Programa de fidelidad</h3>
                    <p class="text-sm text-gray-400">Puntos por cada compra, canjeables por descuentos al pagar.</p>
                </div>
                <div class="bg-white rounded-3xl p-7 border border-gray-100 text-center">
                    <div class="w-14 h-14 bg-[#F1590C]/10 rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl">⭐</div>
                    <h3 class="font-bold text-gray-900 mb-2">Reseñas de clientes</h3>
                    <p class="text-sm text-gray-400">Calificaciones de 1 a 5 estrellas con comentario por pedido.</p>
                </div>
                <div class="bg-white rounded-3xl p-7 border border-gray-100 text-center">
                    <div class="w-14 h-14 bg-[#F1590C]/10 rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl">📈</div>
                    <h3 class="font-bold text-gray-900 mb-2">Reportes de ventas</h3>
                    <p class="text-sm text-gray-400">Día más vendido, horas pico y productos top para decidir.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Planes (dinámico desde BD) --}}
    <section id="planes" class="py-20 sm:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <h2 class="text-center text-3xl sm:text-4xl font-extrabold text-[#1a1a2e] mb-4">Precios que un restaurante real puede pagar</h2>
            <p class="text-center text-gray-500 text-lg mb-12">Sin comisiones por pedido. Elige el que se ajuste a tu tamaño hoy.</p>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($plans as $plan)
                    <div class="bg-white rounded-3xl border-2 {{ $plan->slug === 'negocio' ? 'border-[#F1590C] shadow-lg shadow-[#F1590C]/10' : 'border-gray-100' }} p-8 flex flex-col relative transition-all duration-300 hover:border-[#F1590C]/40">
                        @if ($plan->slug === 'negocio')
                            <div class="absolute -top-3.5 left-1/2 -translate-x-1/2">
                                <span class="bg-[#F1590C] text-white text-xs font-bold px-4 py-1 rounded-full">MÁS POPULAR</span>
                            </div>
                        @endif
                        <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $plan->nombre }}</h3>
                        <p class="text-gray-400 text-sm mb-6">{{ $plan->descripcion }}</p>
                        <div class="mb-6">
                            <span class="text-4xl font-black text-gray-900">${{ number_format($plan->precio_mensual, 0) }}</span>
                            <span class="text-gray-400 text-sm">/mes</span>
                        </div>
                        <ul class="space-y-3 mb-8 flex-1">
                            @foreach ($plan->abilities() as $label)
                                <li class="flex items-center gap-3 text-sm text-gray-600">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    {{ $label }}
                                </li>
                            @endforeach
                            @if ($plan->features()->where('enabled', true)->count() === 0)
                                <li class="text-sm text-gray-400">Funciones básicas</li>
                            @endif
                        </ul>
                        <a href="{{ $plan->precio_mensual > 0 ? route('store.register.plan', $plan->slug) : route('store.register') }}"
                           class="block text-center font-bold py-3 rounded-xl transition-all shadow-sm {{ $plan->slug === 'negocio' ? 'bg-[#F1590C] text-white hover:bg-[#D94D0A] shadow-lg shadow-[#F1590C]/30' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ $plan->precio_mensual > 0 ? 'Elegir ' . $plan->nombre : 'Empezar gratis' }}
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA final --}}
    <section id="registro" class="py-20 sm:py-24" style="background-color: #FFF3EB;">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-[#1a1a2e] mb-4">Empieza gratis hoy, sin pagar de más</h2>
            <p class="text-gray-500 text-lg mb-4">No necesitas ser una gran cadena para automatizar tu restaurante. Empieza gratis y sube de plan solo cuando tu negocio lo pida.</p>
            <p class="text-gray-400 text-sm mb-8">Hecho por alguien que atiende restaurantes todos los días.</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('store.register') }}" class="inline-flex items-center gap-2 bg-[#F1590C] text-white font-extrabold px-8 py-4 rounded-xl text-base uppercase tracking-wide hover:bg-[#D94D0A] transition-all shadow-lg shadow-[#F1590C]/20">
                    Empezar gratis
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <a href="#planes" class="inline-flex items-center gap-2 bg-white text-gray-700 font-bold px-8 py-4 rounded-xl text-base border border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition-all">
                    Ver planes premium
                </a>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-gray-950 text-gray-400 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
                <div>
                    <div class="flex items-center gap-2.5 mb-4">
                        <img src="{{ asset('icons/facilo-icon.svg') }}" alt="Facilo" class="h-8 w-8">
                        <span class="font-extrabold text-xl text-white">Facilo</span>
                    </div>
                    <p class="text-sm text-gray-500 leading-relaxed">El sistema inteligente que los restaurantes aman. Hecho en Colombia, para Colombia.</p>
                </div>
                <div>
                    <h4 class="font-bold mb-4 text-white">Productos</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-[#F1590C] transition-colors">Punto de venta</a></li>
                        <li><a href="#" class="hover:text-[#F1590C] transition-colors">Menú digital</a></li>
                        <li><a href="#" class="hover:text-[#F1590C] transition-colors">WhatsApp Bot</a></li>
                        <li><a href="#" class="hover:text-[#F1590C] transition-colors">Sistema de cocina</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4 text-white">Soporte</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-[#F1590C] transition-colors">Centro de ayuda</a></li>
                        <li><a href="#" class="hover:text-[#F1590C] transition-colors">Comunidad</a></li>
                        <li><a href="#" class="hover:text-[#F1590C] transition-colors">Notas de versión</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4 text-white">Saber más</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-[#F1590C] transition-colors">Blog</a></li>
                        <li><a href="#" class="hover:text-[#F1590C] transition-colors">API de Facilo</a></li>
                        <li><a href="#" class="hover:text-[#F1590C] transition-colors">Términos</a></li>
                        <li><a href="#" class="hover:text-[#F1590C] transition-colors">Privacidad</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-gray-600">Facilo Inc ® {{ date('Y') }} | Todos los derechos reservados.</p>
                <div class="flex items-center gap-4">
                    <a href="#" class="text-gray-600 hover:text-[#F1590C] transition-colors" aria-label="Instagram">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    <a href="#" class="text-gray-600 hover:text-[#F1590C] transition-colors" aria-label="Facebook">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="#" class="text-gray-600 hover:text-[#F1590C] transition-colors" aria-label="WhatsApp">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Botón flotante WhatsApp --}}
    <a href="https://wa.me/573001234567?text=Hola%2C%20quiero%20m%C3%A1s%20informaci%C3%B3n%20sobre%20Facilo" target="_blank" rel="noopener" class="fixed bottom-6 right-6 z-50 bg-green-500 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg shadow-green-500/30 hover:bg-green-600 hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5">
        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    </a>

@endsection
