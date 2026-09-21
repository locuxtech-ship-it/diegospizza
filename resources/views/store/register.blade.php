@extends('layouts.landing')

@section('content')

    <div class="pt-16 min-h-screen bg-gray-50">
        <div class="max-w-md mx-auto px-4 py-12">
            <div class="text-center mb-8">
                <img src="{{ asset('icons/facilo-icon.svg') }}" alt="Facilo" class="h-12 w-12 mx-auto mb-4">
                <h1 class="text-2xl font-extrabold text-gray-900">Crea tu tienda</h1>
                <p class="text-gray-500 text-sm mt-2">Configura tu restaurante en menos de 5 minutos</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-2xl p-4 mb-6">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('store.register.store') }}" class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                @csrf

                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nombre del negocio</label>
                    <input type="text" name="nombre_negocio" value="{{ old('nombre_negocio') }}" required
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#F1590C] focus:border-transparent"
                        placeholder="Mi restaurante">
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nombre de tu tienda (URL)</label>
                    <div class="flex items-center gap-0">
                        <input type="text" name="slug" value="{{ old('slug') }}" required
                            class="flex-1 rounded-l-xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#F1590C] focus:border-transparent"
                            placeholder="mi-restaurante">
                        <span class="bg-gray-100 border border-gray-200 border-l-0 rounded-r-xl px-4 py-3 text-sm text-gray-500">.facilo.app</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Ej: <strong>mi-restaurante</strong>.facilo.app — tu clientes verán este enlace</p>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Plan</label>
                    <select name="plan" id="plan" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#F1590C] focus:border-transparent">
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->slug }}" @selected(old('plan', $selectedPlan?->slug) === $plan->slug)>
                                {{ $plan->nombre }} — ${{ number_format($plan->precio_mensual, 0) }}/mes
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nombre del administrador</label>
                    <input type="text" name="nombre_admin" value="{{ old('nombre_admin') }}" required
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#F1590C] focus:border-transparent"
                        placeholder="Tu nombre">
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#F1590C] focus:border-transparent"
                        placeholder="tu@email.com">
                </div>

                <div class="mb-5 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Contraseña</label>
                        <input type="password" name="password" required
                            class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#F1590C] focus:border-transparent"
                            placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Confirmar</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#F1590C] focus:border-transparent"
                            placeholder="••••••••">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-[#F1590C] text-white font-bold py-3.5 rounded-xl hover:bg-[#D94D0A] transition-colors">
                    Crear mi tienda gratis
                </button>

                <p class="text-xs text-gray-400 text-center mt-4">Prueba gratis 14 días · Sin tarjeta de crédito</p>
            </form>
        </div>
    </div>

@endsection
