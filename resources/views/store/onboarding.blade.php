@extends('layouts.landing')

@section('content')

    <div class="pt-16 min-h-screen bg-gray-50">
        <div class="max-w-2xl mx-auto px-4 py-12">
            <div class="text-center mb-10">
                <img src="{{ asset('icons/facilo-icon.svg') }}" alt="Facilo" class="h-12 w-12 mx-auto mb-4">
                <h1 class="text-2xl font-extrabold text-gray-900">¡Bienvenido, {{ $tenant->nombre_negocio }}! 🎉</h1>
                <p class="text-gray-500 text-sm mt-2">Tu tienda se creó con éxito. Completa tu información para empezar a vender.</p>
                <div class="inline-flex items-center gap-2 bg-green-50 text-green-700 text-xs font-bold px-3 py-1.5 rounded-full mt-4">
                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                    Plan {{ $tenant->plan?->nombre ?? 'Gratis' }} — Prueba activa
                </div>
            </div>

            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-2xl p-4 mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 mb-8">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Información de tu tienda</h2>

                <form method="POST" action="{{ route('store.onboarding.update', $tenant->uuid) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-5">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Teléfono / WhatsApp</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $settings->telefono) }}"
                            class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#F1590C] focus:border-transparent"
                            placeholder="+57 300 000 0000">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Dirección</label>
                        <input type="text" name="direccion" value="{{ old('direccion', $settings->direccion) }}"
                            class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#F1590C] focus:border-transparent"
                            placeholder="Calle 123 #45-67, Bogotá">
                    </div>

                    <button type="submit"
                        class="w-full bg-[#F1590C] text-white font-bold py-3.5 rounded-xl hover:bg-[#D94D0A] transition-colors">
                        Guardar y continuar
                    </button>
                </form>
            </div>

            <div class="text-center">
                <a href="/admin/login" class="text-sm font-bold text-[#F1590C] hover:text-[#D94D0A]">
                    Ir al panel de administración →
                </a>
            </div>
        </div>
    </div>

@endsection
