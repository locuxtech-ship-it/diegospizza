<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportProdData extends Command
{
    protected $signature = 'db:import-prod {backup : Ruta al archivo .tar.gz o directorio extraido}';
    protected $description = 'Importa datos de produccion (backup) al entorno local (SQLite)';

    private array $tablasEnOrden = [];

    public function handle()
    {
        $path = $this->argument('backup');

        if (!file_exists($path)) {
            $this->error("No existe: $path");
            return 1;
        }

        // Extraer si es .tar.gz
        $workDir = $path;
        if (str_ends_with($path, '.tar.gz')) {
            $workDir = sys_get_temp_dir() . '/diegospizza_import_' . time();
            mkdir($workDir, 0777, true);
            try {
                $phar = new \PharData($path);
                $phar->extractTo($workDir, null, true);
            } catch (\Exception $e) {
                $this->warn("PharData fallo, intentando tar CLI: " . $e->getMessage());
                $this->runCmd("tar -xzf \"$path\" -C \"$workDir\" 2>/dev/null", true);
                if (!glob("$workDir/database.sql*")) {
                    $workDir = $path;
                }
            }
        }

        // Buscar database.sql.gz o .sql
        $sqlFile = null;
        foreach (glob("$workDir/database.sql*") as $f) {
            $sqlFile = $f;
        }
        if (!$sqlFile) {
            $this->error("No se encontro database.sql(.gz) en $workDir. Archivos: " . implode(', ', glob("$workDir/*")));
            return 1;
        }

        // Descomprimir si .gz
        if (str_ends_with($sqlFile, '.gz')) {
            $out = preg_replace('/\.gz$/', '', $sqlFile);
            try {
                $gz = gzopen($sqlFile, 'rb');
                $outF = fopen($out, 'wb');
                while ($chunk = gzread($gz, 65536)) {
                    fwrite($outF, $chunk);
                }
                fclose($outF);
                gzclose($gz);
                $sqlFile = $out;
            } catch (\Exception $e) {
                $this->error("Error descomprimiendo .gz: " . $e->getMessage());
                return 1;
            }
        }

        $this->line("Archivo SQL: $sqlFile");

        // 1. Determinar orden de tablas
        $this->tablasEnOrden = $this->getTableOrder();

        // 2. Desactivar FK y truncar todo
        $this->info('Limpiando base de datos local...');
        DB::statement('PRAGMA foreign_keys = OFF');
        foreach (array_reverse($this->tablasEnOrden) as $tabla) {
            DB::table($tabla)->truncate();
        }

        // 3. Importar INSERTs del dump
        $this->info('Importando datos desde dump de MySQL...');
        $imported = $this->importInsertsFromDump($sqlFile);

        // 4. Re-importar tablas que fallaron (por dependencias)
        // (los inserts multi-tabla ya respetan orden)

        DB::statement('PRAGMA foreign_keys = ON');

        $this->info("Importacion completada. $imported registros importados.");

        // 5. Copiar storage si existe
        $storageSource = null;
        $volStorage = "$workDir/vol_storage_data.tar.gz";
        if (file_exists($volStorage)) {
            $this->info('Extrayendo storage desde volume backup...');
            $storageExtract = sys_get_temp_dir() . '/diegospizza_storage_' . time();
            mkdir($storageExtract, 0777, true);
            try {
                $phar = new \PharData($volStorage);
                $phar->extractTo($storageExtract, null, true);
                $storageSource = $storageExtract;
            } catch (\Exception $e) {
                $this->warn("No se pudo extraer volume storage: " . $e->getMessage());
            }
        }
        if ($storageSource && is_dir($storageSource)) {
            $this->info('Copiando archivos de storage...');
            $target = storage_path('app/public');
            $this->recurseCopy($storageSource, $target);
            $this->info('Storage copiado.');
        }

        // Limpiar temporales
        if ($workDir !== $path) {
            $this->recurseDelete($workDir);
        }
        if (isset($storageExtract) && file_exists($storageExtract)) {
            $this->recurseDelete($storageExtract);
        }

        return 0;
    }

    private function getTableOrder(): array
    {
        // Orden respetando FK: padres antes que hijos
        return [
            'negocio_settings',
            'users',
            'categorias',
            'productos',
            'producto_variants',
            'clientes',
            'cliente_direcciones',
            'pedidos',
            'pedido_productos',
            'pagos',
            'puntos',
            'reviews',
            'cierres_caja',
            'gastos_cierre',
            'cache',
            'cache_locks',
            'sessions',
            'jobs',
            'failed_jobs',
        ];
    }

    private function importInsertsFromDump(string $sqlFile): int
    {
        $count = 0;
        $handle = fopen($sqlFile, 'r');
        if (!$handle) {
            $this->error("No se pudo abrir $sqlFile");
            return 0;
        }

        $buffer = '';
        $bar = $this->output->createProgressBar(filesize($sqlFile));
        $bar->start();

        while (($line = fgets($handle)) !== false) {
            $bar->advance(strlen($line));
            $trimmed = trim($line);

            // Saltar comentarios, SET, CREATE, ALTER, LOCK, UNLOCK
            if (empty($trimmed) || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '#')
                || str_starts_with($trimmed, '/*') || preg_match('/^(SET|CREATE|ALTER|DROP|LOCK|UNLOCK)/i', $trimmed)) {
                continue;
            }

            $buffer .= $line;

            if (str_ends_with(trim($buffer), ';')) {
                // Es un INSERT?
                if (preg_match('/^INSERT\s+INTO/i', trim($buffer))) {
                    try {
                        DB::statement($buffer);
                        $count++;
                    } catch (\Exception $e) {
                        $this->warn("\nError en INSERT: " . $e->getMessage());
                    }
                }
                $buffer = '';
            }
        }

        $bar->finish();
        $this->line('');
        fclose($handle);

        return $count;
    }

    private function recurseCopy(string $src, string $dst): void
    {
        if (!is_dir($src)) return;
        if (!is_dir($dst)) mkdir($dst, 0777, true);
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($src, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($items as $item) {
            $target = $dst . DIRECTORY_SEPARATOR . $items->getSubPathname();
            if ($item->isDir()) {
                if (!is_dir($target)) mkdir($target, 0777, true);
            } else {
                copy($item, $target);
            }
        }
    }

    private function recurseDelete(string $dir): void
    {
        if (!is_dir($dir)) return;
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($items as $item) {
            $item->isDir() ? rmdir($item) : unlink($item);
        }
        rmdir($dir);
    }

    private function runCmd(string $cmd, bool $ignoreErrors = false): bool
    {
        $output = [];
        $code = 0;
        exec($cmd, $output, $code);
        return $code === 0 || $ignoreErrors;
    }
}
