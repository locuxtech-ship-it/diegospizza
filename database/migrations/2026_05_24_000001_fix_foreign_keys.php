<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=off');
        } else {
            DB::statement('SET foreign_key_checks=0');
        }

        $tables = [
            'pedido_productos' => function ($newName) {
                DB::statement("CREATE TABLE \"{$newName}\" (
                    \"id\" integer primary key autoincrement not null,
                    \"pedido_id\" integer not null references \"pedidos\"(\"id\") on delete cascade,
                    \"producto_id\" integer not null references \"productos\"(\"id\"),
                    \"cantidad\" integer not null,
                    \"precio_unitario\" numeric not null,
                    \"subtotal\" numeric not null,
                    \"notas\" text,
                    \"created_at\" datetime,
                    \"updated_at\" datetime,
                    \"variant_id\" integer references \"producto_variants\"(\"id\") on delete set null,
                    \"variant_tamanio\" varchar
                )");
                DB::statement("INSERT INTO \"{$newName}\" SELECT * FROM \"pedido_productos\"");
            },
            'pagos' => function ($newName) {
                DB::statement("CREATE TABLE \"{$newName}\" (
                    \"id\" integer primary key autoincrement not null,
                    \"pedido_id\" integer not null references \"pedidos\"(\"id\") on delete cascade,
                    \"monto\" numeric not null,
                    \"metodo\" varchar check (\"metodo\" in ('efectivo', 'tarjeta', 'transferencia', 'puntos')) not null,
                    \"referencia\" varchar,
                    \"confirmado\" tinyint(1) not null default '0',
                    \"fecha_pago\" datetime,
                    \"created_at\" datetime,
                    \"updated_at\" datetime
                )");
                DB::statement("INSERT INTO \"{$newName}\" SELECT * FROM \"pagos\"");
            },
            'puntos' => function ($newName) {
                DB::statement("CREATE TABLE \"{$newName}\" (
                    \"id\" integer primary key autoincrement not null,
                    \"cliente_id\" integer not null references \"clientes\"(\"id\") on delete cascade,
                    \"puntos\" integer not null,
                    \"concepto\" varchar not null,
                    \"pedido_id\" integer references \"pedidos\"(\"id\") on delete set null,
                    \"created_at\" datetime,
                    \"updated_at\" datetime
                )");
                DB::statement("INSERT INTO \"{$newName}\" SELECT * FROM \"puntos\"");
            },
        ];

        foreach ($tables as $oldName => $createCallback) {
            $newName = $oldName . '_new';
            $createCallback($newName);
            DB::statement("DROP TABLE \"{$oldName}\"");
            DB::statement("ALTER TABLE \"{$newName}\" RENAME TO \"{$oldName}\"");
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=on');
        } else {
            DB::statement('SET foreign_key_checks=1');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=off');
        } else {
            DB::statement('SET foreign_key_checks=0');
        }

        $tables = [
            'puntos' => "CREATE TABLE \"puntos_new\" (
                \"id\" integer primary key autoincrement not null,
                \"cliente_id\" integer not null references \"clientes\"(\"id\") on delete cascade,
                \"puntos\" integer not null,
                \"concepto\" varchar not null,
                \"pedido_id\" integer references \"pedidos_temp\"(\"id\") on delete set null,
                \"created_at\" datetime,
                \"updated_at\" datetime
            )",
            'pagos' => "CREATE TABLE \"pagos_new\" (
                \"id\" integer primary key autoincrement not null,
                \"pedido_id\" integer not null references \"pedidos_temp\"(\"id\") on delete cascade,
                \"monto\" numeric not null,
                \"metodo\" varchar check (\"metodo\" in ('efectivo', 'tarjeta', 'transferencia', 'puntos')) not null,
                \"referencia\" varchar,
                \"confirmado\" tinyint(1) not null default '0',
                \"fecha_pago\" datetime,
                \"created_at\" datetime,
                \"updated_at\" datetime
            )",
            'pedido_productos' => "CREATE TABLE \"pedido_productos_new\" (
                \"id\" integer primary key autoincrement not null,
                \"pedido_id\" integer not null references \"pedidos_temp\"(\"id\") on delete cascade,
                \"producto_id\" integer not null references \"productos\"(\"id\"),
                \"cantidad\" integer not null,
                \"precio_unitario\" numeric not null,
                \"subtotal\" numeric not null,
                \"notas\" text,
                \"created_at\" datetime,
                \"updated_at\" datetime,
                \"variant_id\" integer references \"producto_variants\"(\"id\") on delete set null,
                \"variant_tamanio\" varchar
            )",
        ];

        foreach ($tables as $oldName => $createSql) {
            $newName = $oldName . '_new';
            DB::statement($createSql);
            DB::statement("INSERT INTO \"{$newName}\" SELECT * FROM \"{$oldName}\"");
            DB::statement("DROP TABLE \"{$oldName}\"");
            DB::statement("ALTER TABLE \"{$newName}\" RENAME TO \"{$oldName}\"");
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=on');
        } else {
            DB::statement('SET foreign_key_checks=1');
        }
    }
};
