<?php
$ticketSize = ($negocio->ticket_size ?? '80') === '57' ? '57' : '80';
$escala = max(50, min(300, ($negocio->ticket_escala ?? 100))) / 100;
$mostrarLogo = $negocio->ticket_mostrar_logo ?? true;
$negritas = $negocio->ticket_negritas ?? true;
$fuente = ($negocio->ticket_fuente ?? 'courier') === 'arial' ? 'Arial, Helvetica, sans-serif' : "'Courier New', Courier, monospace";
$lineHeight = match($negocio->ticket_interlineado ?? 'normal') { 'compacto' => 1.0, 'espaciado' => 1.6, default => 1.3 };
$espaciadoProd = match($negocio->ticket_espaciado ?? 'normal') { 'compacto' => 0, 'espaciado' => 4, default => 1 };
$margen = floatval($negocio->ticket_margen ?? 0.8);

if ($ticketSize === '57') {
    $fsBase = round(7 * $escala);
    $fsH1 = round(10 * $escala);
    $fsSub = round(6 * $escala);
    $fsRef = round(9 * $escala);
    $fsInfo = round(7 * $escala);
    $fsSection = round(8 * $escala);
    $fsProduct = round(7 * $escala);
    $fsVariant = round(6 * $escala);
    $fsTotal = round(9 * $escala);
    $fsFooter = round(7 * $escala);
    $fsAddress = round(8 * $escala);
    $qtyWidth = round(14 * $escala);
    $priceWidth = round(28 * $escala);
    $paperMaxWidth = 48;
    $pageMarginMm = 4.5;
} else {
    $fsBase = round(9 * $escala);
    $fsH1 = round(14 * $escala);
    $fsSub = round(8 * $escala);
    $fsRef = round(12 * $escala);
    $fsInfo = round(9 * $escala);
    $fsSection = round(10 * $escala);
    $fsProduct = round(9 * $escala);
    $fsVariant = round(7 * $escala);
    $fsTotal = round(12 * $escala);
    $fsFooter = round(9 * $escala);
    $fsAddress = round(10 * $escala);
    $qtyWidth = round(22 * $escala);
    $priceWidth = round(42 * $escala);
    $paperMaxWidth = 80;
    $pageMarginMm = 0;
}
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Ticket #<?php echo e($pedido->numero_pedido); ?></title>
    <style>
        @page { margin: 0 <?php echo e($pageMarginMm); ?>mm; size: <?php echo e($ticketSize); ?>mm auto; }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            width: <?php echo e($paperMaxWidth); ?>mm;
            max-width: <?php echo e($paperMaxWidth); ?>mm;
            font-family: <?php echo e($fuente); ?>;
            color: #000;
            font-size: <?php echo e($fsBase); ?>px;
            line-height: <?php echo e($lineHeight); ?>;
        }

        .page-content { padding: <?php echo e($margen); ?>mm; }

        .header { text-align: center; margin-bottom: 2px; }
        .header h1 { font-size: <?php echo e($fsH1); ?>px; font-weight: <?php echo e($negritas ? 'bold' : 'normal'); ?>; }
        .header p { font-size: <?php echo e($fsSub); ?>px; margin-top: 0; }
        .divider { border-top: 1px dashed #000; margin: 3px 0; }
        .order-ref { text-align: center; font-size: <?php echo e($fsRef); ?>px; font-weight: <?php echo e($negritas ? 'bold' : 'normal'); ?>; margin-bottom: 2px; }
        .info-row { display: flex; justify-content: space-between; font-size: <?php echo e($fsInfo); ?>px; }
        .info-label { font-weight: <?php echo e($negritas ? 'bold' : 'normal'); ?>; }
        .address-box { margin: 3px 0; }
        .section-title { font-weight: <?php echo e($negritas ? 'bold' : 'normal'); ?>; font-size: <?php echo e($fsSection); ?>px; margin-bottom: 1px; margin-top: 1px; }
        .product-item { padding: <?php echo e($espaciadoProd); ?>px 0; }
        .product-row { display: flex; align-items: flex-start; font-size: <?php echo e($fsProduct); ?>px; }
        .product-qty { width: <?php echo e($qtyWidth); ?>px; text-align: right; flex-shrink: 0; }
        .product-name { flex: 1; padding: 0 2px; word-wrap: break-word; overflow-wrap: break-word; }
        .product-price { width: <?php echo e($priceWidth); ?>px; text-align: right; flex-shrink: 0; }
        .product-sub { font-size: <?php echo e($fsVariant); ?>px; padding-left: <?php echo e($qtyWidth); ?>px; }
        .totals { margin-top: 2px; }
        .total-row { display: flex; justify-content: space-between; font-size: <?php echo e($fsInfo); ?>px; padding: 0; }
        .grand-total { font-size: <?php echo e($fsTotal); ?>px; font-weight: <?php echo e($negritas ? 'bold' : 'normal'); ?>; border-top: 1px solid #000; padding-top: 2px; margin-top: 2px; }
        .payment-info { margin-top: 3px; }
        .payment-info p { font-size: <?php echo e($fsInfo); ?>px; }
        .footer { text-align: center; margin-top: 4px; font-size: <?php echo e($fsFooter); ?>px; }
        .notas { margin-top: 2px; font-size: <?php echo e($fsInfo); ?>px; font-style: italic; }
        .logo-img { max-width: <?php echo e($ticketSize === '57' ? '45' : '65'); ?>mm; max-height: <?php echo e($ticketSize === '57' ? '15' : '22'); ?>mm; margin-bottom: 2px; }
    </style>
</head>
<body>
    <div class="page-content">
        <div class="header">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mostrarLogo && $negocio->logo): ?>
                <img src="<?php echo e(asset('storage/'.$negocio->logo)); ?>" alt="Logo" class="logo-img">
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <h1><?php echo e($negocio->nombre_negocio ?? "Diego's Pizza"); ?></h1>
            <p><?php echo e($negocio->direccion ?? ''); ?></p>
        </div>

        <div class="divider"></div>

        <div class="order-ref">#<?php echo e($pedido->numero_pedido); ?></div>
        <div class="info-row">
            <span><?php echo e($pedido->created_at->format('d/m/Y H:i')); ?></span>
            <span><?php echo e(ucfirst($pedido->metodo_pago)); ?></span>
        </div>
        <div class="info-row" style="margin-top: 1px;">
            <span class="info-label">Tel:</span>
            <span><?php echo e($pedido->cliente->telefono); ?></span>
        </div>

        <div class="divider"></div>

        <div class="section-title">📍 DIRECCION</div>
        <div class="address-box">
            <div style="font-size: <?php echo e($fsInfo); ?>px;"><?php echo e($pedido->cliente->nombre); ?></div>
            <div style="font-size: <?php echo e($fsInfo); ?>px; margin-top: 1px;">Conjunto: <?php echo e($pedido->cliente->conjunto); ?></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pedido->cliente->torre): ?>
                <div style="font-size: <?php echo e($fsInfo); ?>px;">Torre: <?php echo e($pedido->cliente->torre); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pedido->cliente->apto): ?>
                <div style="font-size: <?php echo e($fsInfo); ?>px;">Apto: <?php echo e($pedido->cliente->apto); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div style="font-size: <?php echo e($fsInfo); ?>px;">Tel: <?php echo e($pedido->cliente->telefono); ?></div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pedido->notas): ?>
        <div class="notas">
            <span class="info-label">📌</span> <?php echo e($pedido->notas); ?>

        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="divider"></div>

        <div class="section-title">Productos</div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="product-item">
                <div class="product-row">
                    <span class="product-qty"><?php echo e($pp->cantidad); ?>x</span>
                    <span class="product-name"><?php echo e($pp->producto->nombre); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pp->mitades): ?> [<?php echo e(collect($pp->mitades)->pluck('nombre')->implode(' / ')); ?>]<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                    <span class="product-price">$<?php echo e(number_format($pp->subtotal, 0, ',', '.')); ?></span>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pp->variant_tamanio): ?>
                    <div class="product-sub">(<?php echo e($pp->variant_tamanio); ?>)</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

        <div class="divider"></div>

        <div class="totals">
            <div class="total-row">
                <span>Subtotal</span>
                <span>$<?php echo e(number_format($pedido->subtotal, 0, ',', '.')); ?></span>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pedido->descuento_puntos > 0): ?>
            <div class="total-row">
                <span>Desc. puntos</span>
                <span>-$<?php echo e(number_format($pedido->descuento_puntos, 0, ',', '.')); ?></span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pedido->descuento_manual > 0): ?>
            <div class="total-row">
                <span>Desc. manual</span>
                <span>-$<?php echo e(number_format($pedido->descuento_manual, 0, ',', '.')); ?></span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="total-row grand-total">
                <span>TOTAL</span>
                <span>$<?php echo e(number_format($pedido->total, 0, ',', '.')); ?></span>
            </div>
        </div>

        <div class="divider"></div>

        <div class="payment-info">
            <p><span class="info-label"><?php echo e(ucfirst($pedido->metodo_pago)); ?></span></p>
        </div>

        <div class="divider"></div>

        <div class="footer">
            <p>¡Gracias por tu pedido!</p>
            <p><?php echo e($negocio->nombre_negocio ?? "Diego's Pizza"); ?></p>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() { window.print(); }, 1000);
        };
        window.onafterprint = function() {
            setTimeout(function() { window.close(); }, 300);
        };
    </script>
</body>
</html>
<?php /**PATH D:\diegospizzaApp\diegospizza\resources\views/ticket.blade.php ENDPATH**/ ?>