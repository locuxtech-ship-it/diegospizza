<?php

namespace App\Console\Commands;

use App\Models\Cliente;
use Illuminate\Console\Command;

class ImportarClientesOlaClick extends Command
{
    protected $signature = 'clientes:importar-olaclick
        {file? : Ruta al archivo CSV de OlaClick (default: storage/app/olaclick.csv)}
        {--dry-run : Solo mostrar qué se importaría sin guardar}';

    protected $description = 'Importa clientes desde CSV de OlaClick (nombre, teléfono, puntos)';

    public function handle(): int
    {
        $file = $this->argument('file') ?? storage_path('app/olaclick.csv');
        $dryRun = $this->option('dry-run');

        if (!file_exists($file)) {
            $this->error("Archivo no encontrado: $file");
            return self::FAILURE;
        }

        $handle = fopen($file, 'r');
        if (!$handle) {
            $this->error("No se pudo abrir el archivo: $file");
            return self::FAILURE;
        }

        $header = fgetcsv($handle, 0, ';', '"', '\\');
        if (!$header) {
            $this->error('No se pudo leer la cabecera del CSV');
            fclose($handle);
            return self::FAILURE;
        }

        $colNombre = 0;
        $colTelefono = 1;
        $colPuntos = 5;

        $importados = 0;
        $actualizados = 0;
        $saltados = 0;
        $errores = 0;

        $bar = $this->output->createProgressBar();
        $bar->start();

        while (($row = fgetcsv($handle, 0, ';', '"', '\\')) !== false) {
            $bar->advance();

            $nombre = trim($row[$colNombre] ?? '');
            $telefonoRaw = trim($row[$colTelefono] ?? '');
            $puntosRaw = trim($row[$colPuntos] ?? '0');

            if (empty($nombre) || empty($telefonoRaw)) {
                $saltados++;
                continue;
            }

            $telefono = $this->limpiarTelefono($telefonoRaw);
            if (!preg_match('/^3\d{9}$/', $telefono)) {
                if (strlen($telefono) > 0 && $this->option('dry-run') && $saltados < 5) {
                    $this->warn("\n  Saltado (no colombiano): '$telefonoRaw' -> '$telefono' para '$nombre'");
                }
                $saltados++;
                continue;
            }

            $puntos = is_numeric($puntosRaw) ? (int) $puntosRaw : 0;

            $existe = Cliente::where('telefono', $telefono)->first();

            if ($dryRun) {
                $importados++;
                continue;
            }

            try {
                if ($existe) {
                    $nuevosPuntos = max($existe->puntos_acumulados, $puntos);
                    $existe->update([
                        'nombre' => $nombre,
                        'puntos_acumulados' => $nuevosPuntos,
                    ]);
                    $actualizados++;
                } else {
                    Cliente::create([
                        'nombre' => $nombre,
                        'telefono' => $telefono,
                        'direccion' => 'Sin dirección',
                        'puntos_acumulados' => $puntos,
                    ]);
                    $importados++;
                }
            } catch (\Exception $e) {
                $this->error("\n  Error al procesar '$nombre' ($telefono): " . $e->getMessage());
                $errores++;
            }
        }

        $bar->finish();
        fclose($handle);

        $this->newline(2);
        $this->table(
            ['', 'Cantidad'],
            [
                [$dryRun ? 'Importaría' : 'Importados', $importados],
                [$dryRun ? 'Actualizaría' : 'Actualizados', $actualizados],
                ['Saltados', $saltados],
                ['Errores', $errores],
            ]
        );

        return self::SUCCESS;
    }

    private function limpiarTelefono(string $telefono): string
    {
        $telefono = trim($telefono);
        $telefono = preg_replace('/^57\s*/', '', $telefono);
        $telefono = preg_replace('/[^0-9]/', '', $telefono);
        return $telefono;
    }
}
