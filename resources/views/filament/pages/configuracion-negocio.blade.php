<x-filament-panels::page>
    <div class="max-w-xl mx-auto p-4">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Configuración</h1>
        
        <form method="POST" action="{{ route('config.guardar') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div class="bg-white p-4 rounded-lg border">
                <h2 class="font-semibold mb-3 text-gray-700">Negocio</h2>
                <input type="text" name="nombre_negocio" value="{{ $nombre_negocio }}" placeholder="Nombre del negocio" class="w-full border rounded p-2 mb-2">
                <input type="text" name="telefono" value="{{ $telefono }}" placeholder="Teléfono" class="w-full border rounded p-2 mb-2">
                <input type="text" name="direccion" value="{{ $direccion }}" placeholder="Dirección" class="w-full border rounded p-2">
            </div>
            
            <div class="bg-white p-4 rounded-lg border">
                <h2 class="font-semibold mb-3 text-gray-700">Horario</h2>
                <div class="flex gap-4 mb-2">
                    <div class="flex-1">
                        <label class="text-xs text-gray-500">Apertura</label>
                        <input type="time" name="horario_apertura" value="{{ $horario_apertura }}" class="w-full border rounded p-2">
                    </div>
                    <div class="flex-1">
                        <label class="text-xs text-gray-500">Cierre</label>
                        <input type="time" name="horario_cierre" value="{{ $horario_cierre }}" class="w-full border rounded p-2">
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $dia)
                        <label class="text-sm">
                            <input type="checkbox" name="dias_laborales[]" value="{{ $dia }}" {{ in_array($dia, $dias_laborales ?? []) ? 'checked' : '' }}> {{ $dia }}
                        </label>
                    @endforeach
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border">
                <h2 class="font-semibold mb-3 text-gray-700">Puntos</h2>
                <div class="flex gap-4">
                    <input type="number" name="puntos_por_dolar" value="{{ $puntos_por_dolar }}" placeholder="Puntos por $1" class="w-1/2 border rounded p-2">
                    <input type="number" step="0.01" name="descuento_por_punto" value="{{ $descuento_por_punto }}" placeholder="Descuento por punto" class="w-1/2 border rounded p-2">
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border">
                <h2 class="font-semibold mb-3 text-gray-700">Impresión</h2>
                <label class="flex items-center gap-2 mb-2">
                    <input type="checkbox" name="imprimir_automaticamente" {{ $imprimir_automaticamente ? 'checked' : '' }}> Auto-imprimir
                </label>
                <input type="text" name="impresora_nombre" value="{{ $impresora_nombre }}" placeholder="Nombre de impresora" class="w-full border rounded p-2">
            </div>
            
            <button type="submit" class="w-full bg-red-600 text-white py-3 rounded font-semibold hover:bg-red-700">
                Guardar
            </button>
        </form>
    </div>
</x-filament-panels::page>