<?php
$db = new PDO('sqlite:database/database.sqlite');
$tables = ['categorias', 'productos', 'producto_variants', 'negocio_settings'];
$r = [];
foreach ($tables as $t) {
    $r[$t] = $db->query("SELECT * FROM $t")->fetchAll(PDO::FETCH_ASSOC);
}
file_put_contents('database/export.json', json_encode($r, JSON_PRETTY_PRINT));
echo "Exportado: " . filesize('database/export.json') . " bytes\n";
