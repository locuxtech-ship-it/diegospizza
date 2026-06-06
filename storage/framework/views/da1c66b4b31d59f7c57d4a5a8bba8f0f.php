<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div style="display: flex; flex-direction: column; gap: 24px;" wire:poll.keep-alive.5s="cargarPedidos">
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 16px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; background: #fee2e2; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">⏳</div>
                    <div>
                        <p style="margin: 0; color: #ef4444; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Pend. Pago</p>
                        <p style="margin: 0; color: #991b1b; font-size: 24px; font-weight: 700;"><?php echo e(count($pendientePago)); ?></p>
                    </div>
                </div>
            </div>
            <div style="background: #f3e8ff; border: 1px solid #d8b4fe; border-radius: 12px; padding: 16px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; background: #ede9fe; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">📍</div>
                    <div>
                        <p style="margin: 0; color: #9333ea; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Ha Llegado</p>
                        <p style="margin: 0; color: #6b21a8; font-size: 24px; font-weight: 700;"><?php echo e(count($haLlegado)); ?></p>
                    </div>
                </div>
            </div>
            <div style="background: #fff7ed; border: 1px solid #fed7aa; border-radius: 12px; padding: 16px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; background: #ffedd5; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">👨‍🍳</div>
                    <div>
                        <p style="margin: 0; color: #ea580c; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Preparacion</p>
                        <p style="margin: 0; color: #9a3412; font-size: 24px; font-weight: 700;"><?php echo e(count($enProceso)); ?></p>
                    </div>
                </div>
            </div>
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 16px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; background: #dbeafe; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">🚗</div>
                    <div>
                        <p style="margin: 0; color: #2563eb; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">En Camino</p>
                        <p style="margin: 0; color: #1e3a5f; font-size: 24px; font-weight: 700;"><?php echo e(count($enCamino)); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 8px; margin-bottom: -16px;">
            <button wire:click="$set('vistaLista', false)" style="padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; border: 1px solid #d1d5db; cursor: pointer; <?php echo e(!$vistaLista ? 'background: #111827; color: white; border-color: #111827;' : 'background: white; color: #374151;'); ?>">
                📋 Kanban
            </button>
            <button wire:click="$set('vistaLista', true)" style="padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; border: 1px solid #d1d5db; cursor: pointer; <?php echo e($vistaLista ? 'background: #111827; color: white; border-color: #111827;' : 'background: white; color: #374151;'); ?>">
                📄 Lista
            </button>
            <button onclick="window.probarNotificacion()" style="padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; border: 1px solid #d1d5db; cursor: pointer; background: #fef3c7; color: #92400e;">
                🔔 Probar
            </button>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($vistaLista): ?>
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
            <div style="display: grid; grid-template-columns: 60px 1fr 1fr 1.5fr 70px 70px 80px 120px 140px; gap: 0; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                <div style="padding: 10px 12px;">#</div>
                <div style="padding: 10px 12px;">Cliente</div>
                <div style="padding: 10px 12px;">Teléfono</div>
                <div style="padding: 10px 12px;">Dirección</div>
                <div style="padding: 10px 12px; text-align: center;">Origen</div>
                <div style="padding: 10px 12px; text-align: right;">Total</div>
                <div style="padding: 10px 12px; text-align: center;">Pago</div>
                <div style="padding: 10px 12px; text-align: center;">Estado</div>
                <div style="padding: 10px 12px; text-align: center;">Acción</div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $todos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pedido): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                $productos = $this->getProductosPedido($pedido['id']);
                $pagado = $this->pagoCompleto($pedido['id']);
                $dir = collect([$pedido['cliente']['conjunto'] ?? '', $pedido['cliente']['torre'] ?? '', $pedido['cliente']['apto'] ?? ''])->filter()->implode(', ');
                $resumen = collect($productos)->map(fn($p) => $p['cantidad'] . 'x ' . (!empty($p['mitades']) ? 'Pizza Mediana Mitad y Mitad [' . collect($p['mitades'])->pluck('nombre')->implode('/') . ']' : $p['producto']['nombre'] . (!empty($p['variant_tamanio']) ? ' (' . $p['variant_tamanio'] . ')' : '')))->implode(', ');
                $iconoEstado = match($pedido['estado']) { 'pendiente_pago' => '⏳', 'en_proceso' => '👨‍🍳', 'en_camino' => '🚗', 'entregado' => '📍', default => '' };
                $siguiente = match($pedido['estado']) { 'pendiente_pago' => 'en_proceso', 'en_proceso' => 'en_camino', 'en_camino' => 'entregado', 'entregado' => 'finalizado', default => null };
            ?>
            <div wire:click="editarPedido(<?php echo e($pedido['id']); ?>)" style="display: grid; grid-template-columns: 60px 1fr 1fr 1.5fr 70px 70px 80px 120px 140px; gap: 0; border-bottom: 1px solid #f3f4f6; font-size: 13px; cursor: pointer; transition: background 0.15s;"
                 onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background=''">
                <div style="padding: 10px 12px; font-weight: 700; color: #111827;">#<?php echo e($pedido['numero_pedido']); ?></div>
                <div style="padding: 10px 12px; display: flex; align-items: center; gap: 6px; overflow: hidden;">
                    <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo e($pedido['cliente']['nombre']); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($pedido['cliente']['clasificacion'])): ?>
                        <span style="flex-shrink: 0; font-size: 9px; padding: 1px 5px; border-radius: 4px; font-weight: 600;
                            background: <?php echo e($pedido['cliente']['clasificacion'] === 'elite' ? '#fef3c7' : ($pedido['cliente']['clasificacion'] === 'frecuente' ? '#ffedd5' : '#f3f4f6')); ?>;
                            color: <?php echo e($pedido['cliente']['clasificacion'] === 'elite' ? '#92400e' : ($pedido['cliente']['clasificacion'] === 'frecuente' ? '#9a3412' : '#6b7280')); ?>;">
                            <?php echo e($pedido['cliente']['clasificacion'] === 'elite' ? '⭐' : ($pedido['cliente']['clasificacion'] === 'frecuente' ? '🔥' : '🆕')); ?>

                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div style="padding: 10px 12px; color: #6b7280;"><?php echo e($pedido['cliente']['telefono'] ?? ''); ?></div>
                <div style="padding: 10px 12px; font-size: 11px; color: #6b7280; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo e($dir); ?>"><?php echo e($dir); ?></div>
                <div style="padding: 10px 12px; text-align: center;">
                    <span style="font-size: 11px; padding: 2px 6px; border-radius: 6px; font-weight: 500; <?php echo e(($pedido['origen'] ?? 'pdv') === 'web' ? 'background: #dbeafe; color: #2563eb;' : 'background: #fef3c7; color: #d97706;'); ?>">
                        <?php echo e(strtoupper($pedido['origen'] ?? 'PDV')); ?>

                    </span>
                </div>
                <div style="padding: 10px 12px; text-align: right; font-weight: 600;">$<?php echo e(number_format($pedido['total'], 0, ',', '.')); ?></div>
                <div style="padding: 10px 12px; text-align: center;">
                    <span style="font-size: 11px; padding: 2px 6px; border-radius: 6px; font-weight: 500; <?php echo e($pagado ? 'background: #dcfce7; color: #16a34a;' : 'background: #fef3c7; color: #d97706;'); ?>">
                        <?php echo e($pagado ? 'Pagado' : 'Pend.'); ?>

                    </span>
                </div>
                <div style="padding: 10px 12px; text-align: center;">
                    <span style="font-size: 12px;"><?php echo e($iconoEstado); ?> <?php echo e(match($pedido['estado']) { 'pendiente_pago' => 'Pend. Pago', 'en_proceso' => 'Preparación', 'en_camino' => 'En Camino', 'entregado' => 'Ha Llegado', default => '' }); ?></span>
                </div>
                <div style="padding: 8px 12px; text-align: center; display: flex; gap: 4px; align-items: center; justify-content: center;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!in_array($pedido['estado'], ['finalizado', 'cancelado'])): ?>
                    <a href="/admin/pedidos/<?php echo e($pedido['id']); ?>/edit" style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; font-size: 11px; text-decoration: none; background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb;" title="Editar pedido">✏️</a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <a href="#" onclick="event.stopPropagation(); printPedido(<?php echo e($pedido['id']); ?>); return false;" style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; font-size: 11px; font-weight: 500; text-decoration: none; background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb;" title="Reimprimir ticket">🖨️</a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($pedido['cliente']['telefono'])): ?>
                        <?php $waTel = preg_replace('/[^0-9]/', '', $pedido['cliente']['telefono']); ?>
                        <a href="https://wa.me/<?php echo e($waTel); ?>?text=<?php echo e(urlencode('Hola! Te escribimos de Diego\'s Pizza por tu pedido #' . $pedido['numero_pedido'])); ?>" target="_blank" style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; font-size: 11px; font-weight: 500; text-decoration: none; background: #dcfce7; color: #25D366; border: 1px solid #bbf7d0;" title="WhatsApp cliente">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="#25D366" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($siguiente === 'en_proceso'): ?>
                        <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['wire:click.stop' => 'cambiarEstado('.e($pedido['id']).', \'en_proceso\')','size' => 'xs','color' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click.stop' => 'cambiarEstado('.e($pedido['id']).', \'en_proceso\')','size' => 'xs','color' => 'success']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            ✅ Aceptar
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['wire:click.stop' => 'rechazarPedido('.e($pedido['id']).')','size' => 'xs','color' => 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click.stop' => 'rechazarPedido('.e($pedido['id']).')','size' => 'xs','color' => 'danger']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            ❌ Rechazar
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                    <?php elseif($siguiente === 'finalizado'): ?>
                        <?php $pagoOk = $this->pagoCompleto($pedido['id']); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pagoOk): ?>
                            <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['wire:click.stop' => 'finalizarPedido('.e($pedido['id']).')','size' => 'xs','color' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click.stop' => 'finalizarPedido('.e($pedido['id']).')','size' => 'xs','color' => 'success']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                ✅ Finalizar
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                        <?php else: ?>
                            <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['wire:click.stop' => 'finalizarPedido('.e($pedido['id']).')','size' => 'xs','color' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click.stop' => 'finalizarPedido('.e($pedido['id']).')','size' => 'xs','color' => 'warning']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                💳 Pagar
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['wire:click.stop' => 'cambiarEstado('.e($pedido['id']).', \''.e($siguiente).'\')','size' => 'xs','color' => 'gray']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click.stop' => 'cambiarEstado('.e($pedido['id']).', \''.e($siguiente).'\')','size' => 'xs','color' => 'gray']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php echo e($siguiente == 'en_camino' ? '🚗 Enviar' : '📍 Llegó'); ?>

                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div style="text-align: center; padding: 32px 0; color: #9ca3af;">📋 Sin pedidos activos</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
            <?php
                $columnas = [
                    ['nombre' => 'Pendiente de Pago', 'color' => '#ef4444', 'bg' => '#fee2e2', 'icon' => '⏳', 'datos' => $pendientePago, 'siguiente' => 'en_proceso', 'accion' => 'Aceptar'],
                    ['nombre' => 'En Preparación', 'color' => '#ea580c', 'bg' => '#ffedd5', 'icon' => '👨‍🍳', 'datos' => $enProceso, 'siguiente' => 'en_camino', 'accion' => 'Enviar'],
                    ['nombre' => 'En Camino', 'color' => '#2563eb', 'bg' => '#dbeafe', 'icon' => '🚗', 'datos' => $enCamino, 'siguiente' => 'entregado', 'accion' => 'Llegó'],
                    ['nombre' => 'Ha Llegado', 'color' => '#9333ea', 'bg' => '#f3e8ff', 'icon' => '📍', 'datos' => $haLlegado, 'siguiente' => 'finalizado', 'accion' => 'Finalizar'],
                ];
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $columnas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                    <div style="background: <?php echo e($col['color']); ?>; padding: 12px 16px; display: flex; align-items: center; gap: 8px;">
                        <span><?php echo e($col['icon']); ?></span>
                        <span style="color: white; font-weight: 600;"><?php echo e($col['nombre']); ?> (<?php echo e(count($col['datos'])); ?>)</span>
                    </div>
                    <div style="padding: 12px; display: flex; flex-direction: column; gap: 12px; max-height: 500px; overflow-y: auto;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $col['datos']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pedido): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php $dir = collect([$pedido['cliente']['conjunto'] ?? '', $pedido['cliente']['torre'] ?? '', $pedido['cliente']['apto'] ?? ''])->filter()->implode(', '); ?>
                            <div wire:click="editarPedido(<?php echo e($pedido['id']); ?>)" style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); cursor: pointer;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span style="background: #111827; color: white; font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 4px;">#<?php echo e($pedido['numero_pedido']); ?></span>
                                        <span style="font-size: 10px; padding: 2px 6px; border-radius: 4px; font-weight: 600; <?php echo e(($pedido['origen'] ?? 'pdv') === 'web' ? 'background: #dbeafe; color: #2563eb;' : 'background: #fef3c7; color: #d97706;'); ?>">
                                            <?php echo e(strtoupper($pedido['origen'] ?? 'PDV')); ?>

                                        </span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span style="font-size: 12px; background: #f3f4f6; color: #6b7280; padding: 2px 8px; border-radius: 10px;"><?php echo e($this->tiempoTranscurrido($pedido['created_at'])); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($pedido['metodo_pago'])): ?>
                                            <span style="font-size: 12px; padding: 2px 8px; border-radius: 10px; font-weight: 500;
                                                 <?php if($pedido['metodo_pago'] == 'efectivo'): ?> background: #dcfce7; color: #16a34a;
                                                <?php elseif($pedido['metodo_pago'] == 'tarjeta'): ?> background: #dbeafe; color: #2563eb;
                                                <?php elseif($pedido['metodo_pago'] == 'mixto'): ?> background: #fef3c7; color: #d97706;
                                                <?php else: ?> background: #f3e8ff; color: #9333ea; <?php endif; ?>">
                                                <?php echo e($pedido['metodo_pago']); ?>

                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <p style="margin: 0; font-weight: 600; color: #111827; display: flex; align-items: center; gap: 4px;">
                                    <?php echo e($pedido['cliente']['nombre']); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($pedido['cliente']['clasificacion'])): ?>
                                        <span style="font-size: 9px; padding: 1px 5px; border-radius: 4px; font-weight: 600;
                                            background: <?php echo e($pedido['cliente']['clasificacion'] === 'elite' ? '#fef3c7' : ($pedido['cliente']['clasificacion'] === 'frecuente' ? '#ffedd5' : '#f3f4f6')); ?>;
                                            color: <?php echo e($pedido['cliente']['clasificacion'] === 'elite' ? '#92400e' : ($pedido['cliente']['clasificacion'] === 'frecuente' ? '#9a3412' : '#6b7280')); ?>;">
                                            <?php echo e($pedido['cliente']['clasificacion'] === 'elite' ? '⭐' : ($pedido['cliente']['clasificacion'] === 'frecuente' ? '🔥' : '🆕')); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </p>
                                <p style="margin: 4px 0 0 0; font-size: 12px; color: #9ca3af;" title="<?php echo e($dir); ?>"><?php echo e($dir); ?></p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($pedido['cliente']['telefono'])): ?>
                                    <p style="margin: 2px 0 0 0; font-size: 12px; color: #9ca3af;">📞 <?php echo e($pedido['cliente']['telefono']); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php $productos = $this->getProductosPedido($pedido['id']); ?>
                                <div style="margin-top: 12px; border-top: 1px solid #e5e7eb; padding-top: 8px;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <div style="display: flex; justify-content: space-between; font-size: 12px; padding: 2px 0;">
                                            <span style="color: #4b5563;"><strong><?php echo e($pp['cantidad']); ?>x</strong> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($pp['mitades'])): ?>Pizza Mediana Mitad y Mitad<span style="color: #ea580c; font-size: 10px;"> [<?php echo e(collect($pp['mitades'])->pluck('nombre')->implode(' / ')); ?>]</span><?php else: ?><?php echo e($pp['producto']['nombre']); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($pp['variant_tamanio'])): ?> <span style="color: #ea580c;">(<?php echo e($pp['variant_tamanio']); ?>)</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                                            <span style="font-weight: 500; color: #374151;">$<?php echo e(number_format($pp['subtotal'], 0, ',', '.')); ?></span>
                                        </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>

                                <div style="display: flex; justify-content: space-between; align-items: center; gap: 6px; margin-top: 12px; padding-top: 12px; border-top: 1px solid #e5e7eb;">
                                    <div style="display: flex; align-items: center; gap: 4px;">
                                        <span style="font-weight: 700; font-size: 16px;">$<?php echo e(number_format($pedido['total'], 0, ',', '.')); ?></span>
                                        <?php $pedidoPagoCompleto = $pedido['pago_completo'] ?? $this->pagoCompleto($pedido['id']); ?>
                                        <span style="font-size: 11px; padding: 2px 6px; border-radius: 10px; font-weight: 500; <?php echo e($pedidoPagoCompleto ? 'background: #dcfce7; color: #16a34a;' : 'background: #fef3c7; color: #d97706;'); ?>">
                                            <?php echo e($pedidoPagoCompleto ? '💳 Pagado' : '⏳ Pendiente'); ?>

                                        </span>
                                        <a href="#" onclick="event.stopPropagation(); printPedido(<?php echo e($pedido['id']); ?>); return false;" style="display: inline-flex; align-items: center; gap: 3px; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 500; text-decoration: none; background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb;">
                                            🖨️
                                        </a>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($pedido['cliente']['telefono'])): ?>
                                            <?php $waTelKanban = preg_replace('/[^0-9]/', '', $pedido['cliente']['telefono']); ?>
                                            <a href="https://wa.me/<?php echo e($waTelKanban); ?>?text=<?php echo e(urlencode('Hola! Te escribimos de Diego\'s Pizza por tu pedido #' . $pedido['numero_pedido'])); ?>" target="_blank" onclick="event.stopPropagation()" style="display: inline-flex; align-items: center; gap: 3px; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 500; text-decoration: none; background: #dcfce7; color: #25D366; border: 1px solid #bbf7d0;">
                                                <svg viewBox="0 0 24 24" width="14" height="14" fill="#25D366" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                            </a>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 4px;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!in_array($pedido['estado'], ['finalizado', 'cancelado'])): ?>
                                        <a href="/admin/pedidos/<?php echo e($pedido['id']); ?>/edit" onclick="event.stopPropagation()" style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; font-size: 12px; text-decoration: none; background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb;" title="Editar pedido">✏️</a>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['wire:click.stop' => 'abrirModalPago('.e($pedido['id']).')','size' => 'xs','color' => 'gray']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click.stop' => 'abrirModalPago('.e($pedido['id']).')','size' => 'xs','color' => 'gray']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                            💳
                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($col['siguiente'] === 'en_proceso'): ?>
                                            <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['wire:click.stop' => 'cambiarEstado('.e($pedido['id']).', \'en_proceso\')','size' => 'xs','color' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click.stop' => 'cambiarEstado('.e($pedido['id']).', \'en_proceso\')','size' => 'xs','color' => 'success']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                                ✅ <?php echo e($col['accion']); ?>

                                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                                            <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['wire:click.stop' => 'rechazarPedido('.e($pedido['id']).')','size' => 'xs','color' => 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click.stop' => 'rechazarPedido('.e($pedido['id']).')','size' => 'xs','color' => 'danger']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                                ❌ Rechazar
                                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                                        <?php elseif($col['siguiente'] !== 'finalizado'): ?>
                                            <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['wire:click.stop' => 'cambiarEstado('.e($pedido['id']).', \''.e($col['siguiente']).'\')','size' => 'xs','color' => 'gray']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click.stop' => 'cambiarEstado('.e($pedido['id']).', \''.e($col['siguiente']).'\')','size' => 'xs','color' => 'gray']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                                <?php echo e($col['accion']); ?>

                                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                                        <?php elseif($col['siguiente'] === 'finalizado' && ($pedido['pago_completo'] ?? false)): ?>
                                            <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['wire:click.stop' => 'finalizarPedido('.e($pedido['id']).')','size' => 'xs','color' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click.stop' => 'finalizarPedido('.e($pedido['id']).')','size' => 'xs','color' => 'success']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                                ✅ Finalizar
                                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                                        <?php elseif($col['siguiente'] === 'finalizado'): ?>
                                            <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['wire:click.stop' => 'finalizarPedido('.e($pedido['id']).')','size' => 'xs','color' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click.stop' => 'finalizarPedido('.e($pedido['id']).')','size' => 'xs','color' => 'warning']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                                💳 Pagar
                                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div style="text-align: center; padding: 32px 0;">
                                <p style="font-size: 36px; margin: 0 0 8px 0;">📋</p>
                                <p style="color: #9ca3af; margin: 0;">Sin pedidos</p>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($modalPago): ?>
    <?php $restante = max(0, $totalConDescuento - $totalPagado); ?>
    <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 999;">
        <div style="background: white; border-radius: 16px; padding: 0; width: 650px; max-width: 95vw; max-height: 95vh; overflow-y: auto;">
            <div style="background: linear-gradient(to right, #f9fafb, white); padding: 20px 24px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h3 style="margin: 0; font-size: 20px; font-weight: 800; color: #111827;">Pedido #<?php echo e($pedidoNumero); ?></h3>
                    <div style="display: flex; align-items: center; gap: 8px; margin-top: 4px;">
                        <span style="font-size: 11px; padding: 2px 8px; border-radius: 9999px; font-weight: 700; background: #fef3c7; color: #92400e;"><?php echo e($pedidoEstado === 'pendiente_pago' ? 'Pendiente Pago' : $pedidoEstado); ?></span>
                        <span style="font-size: 11px; padding: 2px 8px; border-radius: 9999px; font-weight: 600; background: <?php echo e($pedidoOrigen === 'web' ? '#dbeafe' : '#f3e8ff'); ?>; color: <?php echo e($pedidoOrigen === 'web' ? '#1e40af' : '#6b21a8'); ?>;"><?php echo e(strtoupper($pedidoOrigen)); ?></span>
                        <span style="font-size: 12px; color: #6b7280;"><?php echo e($pedidoFecha); ?></span>
                    </div>
                </div>
                <button wire:click="cerrarModalPago" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #9ca3af; padding: 0 4px;">×</button>
            </div>
            <div style="padding: 16px 24px;">

            
            <div style="margin-bottom: 16px;">
                <p style="margin: 0 0 4px; font-size: 14px; font-weight: 700; color: #111827;">Cliente: <?php echo e($clienteNombre); ?></p>
                <p style="margin: 0 0 2px; font-size: 13px; color: #6b7280;">Teléfono: +57 <?php echo e($clienteTelefono); ?></p>
                <p style="margin: 0; font-size: 13px; color: #6b7280;"><?php echo e($clienteDireccion); ?></p>
            </div>

            
            <div style="margin-bottom: 16px;">
                <p style="margin: 0 0 8px; font-size: 13px; font-weight: 700; color: #374151;">Productos: <?php echo e(count($productosPedido)); ?></p>
                <div style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; max-height: 140px; overflow-y: auto;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $productosPedido; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div style="display: flex; justify-content: space-between; padding: 6px 12px; font-size: 13px; <?php echo e(!$loop->last ? 'border-bottom: 1px solid #f3f4f6;' : ''); ?>">
                        <span style="color: #374151;">
                            <strong><?php echo e($pp['cantidad']); ?></strong>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($pp['mitades'])): ?>
                                Pizza Mediana Mitad y Mitad <span style="color: #ea580c;">[<?php echo e(collect($pp['mitades'])->pluck('nombre')->implode(' / ')); ?>]</span>
                            <?php else: ?>
                                <?php echo e($pp['producto']['nombre']); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($pp['variant_tamanio'])): ?> <span style="color: #6b7280;">- <?php echo e($pp['variant_tamanio']); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>
                        <span style="font-weight: 600;">$<?php echo e(number_format($pp['subtotal'], 0, ',', '.')); ?></span>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>

            
            <div style="background: #f9fafb; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; font-size: 13px; color: #6b7280; margin-bottom: 4px;">
                    <span>Subtotal Productos (<?php echo e(count($productosPedido)); ?>)</span>
                    <span>$<?php echo e(number_format($pedidoSubtotal, 0, ',', '.')); ?></span>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($descuentoAplicado > 0): ?>
                <div style="display: flex; justify-content: space-between; font-size: 13px; color: #dc2626;">
                    <span>Descuento</span>
                    <span>-$<?php echo e(number_format($descuentoAplicado, 0, ',', '.')); ?></span>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div style="display: flex; justify-content: space-between; font-size: 13px; color: #6b7280;">
                    <span>Estado</span>
                    <span style="font-weight: 600; color: <?php echo e($restante > 0 ? '#f59e0b' : '#16a34a'); ?>;"><?php echo e($restante > 0 ? 'Pendiente' : 'Pagado'); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: 800; color: #111827; border-top: 1px solid #e5e7eb; padding-top: 8px; margin-top: 8px;">
                    <span>Total</span>
                    <span>$<?php echo e(number_format($totalConDescuento, 0, ',', '.')); ?></span>
                </div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pedidoEstado === 'cancelado'): ?>
            <div style="text-align: center; padding: 12px; background: #fef2f2; border-radius: 8px; color: #dc2626; font-weight: 600; margin-bottom: 16px;">
                ❌ Pedido cancelado
            </div>
            <?php elseif($restante > 0): ?>
            <div style="margin-bottom: 16px;">
                <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                    <select wire:model.live="pagoMetodo" style="border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 14px; background: white; flex: 1; min-width: 140px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = \App\Models\NegocioSetting::getActivePaymentMethods(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $valor => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($valor); ?>"><?php echo e($info['label']); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                    <div style="display: flex; align-items: center; border: 1px solid #d1d5db; border-radius: 8px; overflow: hidden; flex: 1; min-width: 120px;">
                        <span style="background: #f3f4f6; padding: 10px 10px; font-size: 14px; color: #6b7280; border-right: 1px solid #d1d5db;">$</span>
                        <input type="number" step="1" wire:model.live="pagoMonto" max="<?php echo e($restante); ?>" placeholder="0" style="width: 100%; border: none; padding: 10px 12px; font-size: 14px; outline: none;">
                    </div>
                    <input type="text" wire:model.live="pagoReferencia" placeholder="Ref (opcional)" style="border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 13px; flex: 1; min-width: 120px;">
                    <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['wire:click' => 'registrarPago','color' => 'success','style' => 'height: 42px; white-space: nowrap;']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'registrarPago','color' => 'success','style' => 'height: 42px; white-space: nowrap;']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        💳 Registrar pago
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                </div>
                
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('applyDiscount', auth()->user())): ?>
                <div style="margin-top: 8px; display: flex; gap: 8px; align-items: center;">
                    <span style="font-size: 12px; font-weight: 600; color: #6b7280;">🏷️ Descuento:</span>
                    <label style="display: flex; align-items: center; gap: 4px; font-size: 12px; cursor: pointer;">
                        <input type="radio" wire:model.live="descuentoTipo" value="fijo"> Fijo
                    </label>
                    <label style="display: flex; align-items: center; gap: 4px; font-size: 12px; cursor: pointer;">
                        <input type="radio" wire:model.live="descuentoTipo" value="porcentaje"> %
                    </label>
                    <input type="number" step="1" wire:model.live="descuentoValor" min="0" placeholder="0" style="border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 8px; font-size: 13px; width: 80px;">
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($pagosRegistrados) > 0): ?>
            <div style="margin-bottom: 16px;">
                <p style="margin: 0 0 8px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Historial de pagos</p>
                <div style="max-height: 120px; overflow-y: auto;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pagosRegistrados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pago): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 4px;">
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 13px;">
                            <span style="font-weight: 700; color: #16a34a;">$<?php echo e(number_format($pago['monto'], 0, ',', '.')); ?></span>
                            <span style="font-size: 11px; padding: 2px 8px; border-radius: 9999px; font-weight: 600;
                                <?php if($pago['metodo'] == 'efectivo'): ?> background: #dcfce7; color: #16a34a;
                                <?php elseif($pago['metodo'] == 'tarjeta'): ?> background: #dbeafe; color: #2563eb;
                                <?php else: ?> background: #f3e8ff; color: #9333ea; <?php endif; ?>">
                                <?php echo e($pago['metodo']); ?>

                            </span>
                            <span style="color: #9ca3af; font-size: 12px;"><?php echo e(\Carbon\Carbon::parse($pago['created_at'])->format('d/m/y H:i')); ?></span>
                        </div>
                        <button wire:click="eliminarPago(<?php echo e($pago['id']); ?>)" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 16px; padding: 0 4px;" title="Eliminar">×</button>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($restante > 0): ?>
                <div style="text-align: center; margin-top: 8px;">
                    <span style="font-size: 13px; color: #6b7280; font-weight: 500;">¿Agregar otro pago?</span>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php elseif($restante <= 0): ?>
            <div style="text-align: center; padding: 12px; background: #f0fdf4; border-radius: 8px; color: #16a34a; font-weight: 600; margin-bottom: 12px;">
                ✅ Pago completo
            </div>
            <div style="display: flex; gap: 8px; justify-content: center; margin-bottom: 16px;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pedidoEstado === 'pendiente_pago'): ?>
                    <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['wire:click' => 'aceptarDesdeModal','color' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'aceptarDesdeModal','color' => 'success']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        ✅ Aceptar Pedido
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                <?php elseif($pedidoEstado === 'entregado'): ?>
                    <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['wire:click' => 'finalizarDesdeModal','color' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'finalizarDesdeModal','color' => 'success']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        🎉 Finalizar Pedido
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!in_array($pedidoEstado, ['finalizado', 'cancelado'])): ?>
            <div style="border-top: 1px solid #e5e7eb; padding-top: 12px;">
                <p style="margin: 0 0 8px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">✏️ Editar Cliente</p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                    <input type="text" wire:model.live="clienteNombre" placeholder="Nombre" style="border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 8px; font-size: 13px;">
                    <input type="text" wire:model.live="clienteTelefono" placeholder="Teléfono" style="border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 8px; font-size: 13px;">
                    <input type="text" wire:model.live="clienteConjunto" placeholder="Conjunto" style="border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 8px; font-size: 13px;">
                    <div style="display: flex; gap: 8px;">
                        <input type="text" wire:model.live="clienteTorre" placeholder="Torre" style="flex: 1; border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 8px; font-size: 13px;">
                        <input type="text" wire:model.live="clienteApto" placeholder="Apto" style="flex: 1; border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 8px; font-size: 13px;">
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <script>
        var pdvAudio = null;
        var pdvAlarmaUrl = null;
        var pdvIdsAlertando = {};
        var pdvPlayPendiente = false;

        function pdvGenWav() {
            var sr = 44100, dur = 0.6, len = sr * dur;
            var buf = new ArrayBuffer(44 + len * 2);
            var d = new DataView(buf);
            function w(i, s) { for (var j = 0; j < s.length; j++) d.setUint8(i + j, s.charCodeAt(j)); }
            w(0, 'RIFF'); d.setUint32(4, 36 + len * 2, true);
            w(8, 'WAVE'); w(12, 'fmt ');
            d.setUint32(16, 16, true); d.setUint16(20, 1, true); d.setUint16(22, 1, true);
            d.setUint32(24, sr, true); d.setUint32(28, sr * 2, true);
            d.setUint16(32, 2, true); d.setUint16(34, 16, true);
            w(36, 'data'); d.setUint32(40, len * 2, true);
            for (var i = 0; i < len; i++) {
                var t = i / sr;
                var freq = (Math.floor(t / 0.3) % 2 === 0) ? 880 : 660;
                var val = (Math.floor(i * freq / sr) % 2 === 0) ? 0.95 : -0.95;
                d.setInt16(44 + i * 2, val < 0 ? val * 0x8000 : val * 0x7FFF, true);
            }
            return URL.createObjectURL(new Blob([buf], { type: 'audio/wav' }));
        }

        function pdvReproducir() {
            if (!pdvAudio) return;
            pdvAudio.play().then(function(){ pdvPlayPendiente = false; }).catch(function(){ pdvPlayPendiente = true; });
        }

        function pdvIniciarAlarma(pedido) {
            pdvIdsAlertando[pedido.id] = true;
            if (!pdvAudio) {
                pdvAudio = new Audio();
                if (!pdvAlarmaUrl) pdvAlarmaUrl = pdvGenWav();
                pdvAudio.src = pdvAlarmaUrl;
                pdvAudio.loop = true;
                pdvAudio.volume = 1.0;
                pdvAudio.preload = 'auto';
            }
            pdvReproducir();
        }

        function pdvDetenerAlarma(pedidoId) {
            delete pdvIdsAlertando[pedidoId];
            if (Object.keys(pdvIdsAlertando).length === 0 && pdvAudio) {
                pdvAudio.pause();
                pdvAudio.currentTime = 0;
                pdvPlayPendiente = false;
            }
        }

        document.addEventListener('click', function() {
            if (pdvPlayPendiente && Object.keys(pdvIdsAlertando).length > 0) {
                pdvReproducir();
            }
        });

        document.addEventListener('visibilitychange', function() {
            if (!document.hidden && Object.keys(pdvIdsAlertando).length > 0) {
                pdvReproducir();
            }
        });

        function pdvToast(p) {
            var c = document.getElementById('pdv-toast-container');
            if (!c) { c = document.createElement('div'); c.id = 'pdv-toast-container'; c.style.cssText='position:fixed;top:20px;right:20px;z-index:99999;display:flex;flex-direction:column;gap:8px;'; document.body.appendChild(c); }
            var n = document.createElement('div');
            n.style.cssText = 'background:#1e293b;color:white;border-radius:12px;padding:16px 20px;box-shadow:0 10px 40px rgba(0,0,0,0.3);max-width:380px;font-family:-apple-system,sans-serif;display:flex;align-items:center;gap:12px;cursor:pointer;';
            n.innerHTML = '<div style="background:#22c55e;border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">🍕</div><div><div style="font-size:14px;font-weight:700;">Nuevo Pedido #'+(p.numero_pedido||p.id)+'</div><div style="font-size:12px;color:#94a3b8;">'+(p.cliente?.nombre||'')+'</div></div>';
            n.onclick = function(){ n.remove(); };
            c.appendChild(n);
            setTimeout(function(){ if(n.parentNode) n.remove(); }, 10000);
        }
        function pdvSystemNotif(p) {
            if (!('Notification' in window) || Notification.permission !== 'granted') return;
            try {
                var n = new Notification('🍕 Pedido #'+(p.numero_pedido||p.id), { body: (p.cliente?.nombre||'')+' — $'+Number(p.total).toLocaleString('es-CO'), tag: 'pdv-'+p.id, requireInteraction: true });
                setTimeout(function(){ n.close(); }, 10000);
                n.onclick = function(){ window.focus(); this.close(); };
            } catch(e) {}
        }
        function pdvFlash(p) {
            var orig = document.title, count = 0, t = setInterval(function(){
                document.title = (count%2===0) ? '🆕 Pedido #'+(p.numero_pedido||p.id)+' — '+orig : orig;
                count++;
                if (count >= 8) { clearInterval(t); document.title = orig; }
            }, 800);
            document.addEventListener('visibilitychange', function vis(){
                if (!document.hidden) { clearInterval(t); document.title = orig; document.removeEventListener('visibilitychange', vis); }
            });
        }

        var pdvUltimosIds = [];
        function pdvBuscarNuevos() {
            fetch('/api/pedidos/pendientes').then(function(r){ return r.json(); }).then(function(data){
                var pedidos = data.pedidos || [];
                var ids = pedidos.map(function(p){ return p.id; });
                Object.keys(pdvIdsAlertando).forEach(function(id) {
                    if (ids.indexOf(parseInt(id)) === -1) {
                        pdvDetenerAlarma(parseInt(id));
                    }
                });
                if (pdvUltimosIds.length > 0) {
                    var nuevos = pedidos.filter(function(p){ return pdvUltimosIds.indexOf(p.id) === -1; });
                    nuevos.forEach(function(p){
                        pdvIniciarAlarma(p);
                        pdvToast(p);
                        pdvSystemNotif(p);
                        pdvFlash(p);
                        try { navigator.vibrate && navigator.vibrate([200,100,200]); } catch(e){}
                        var w = window.open('/admin/ticket/'+p.id, '_blank', 'width=400,height=600,menubar=no,toolbar=no,location=no');
                        if (w) w.focus();
                    });
                }
                pdvUltimosIds = ids;
            }).catch(function(){});
        }

        if ('Notification' in window && Notification.permission === 'default') {
            setTimeout(function(){ Notification.requestPermission(); }, 3000);
        }

        setTimeout(pdvBuscarNuevos, 2000);
        setInterval(pdvBuscarNuevos, 5000);

        function printPedido(id) {
            var w = window.open('/admin/ticket/' + id, '_blank', 'width=400,height=600,menubar=no,toolbar=no,location=no');
            if (w) w.focus();
        }

        window.probarNotificacion = function() {
            var p = { id: 999999, numero_pedido: 'TEST', cliente: { nombre: 'Prueba' }, total: 50000, origen: 'web' };
            pdvIniciarAlarma(p);
            pdvToast(p);
            pdvSystemNotif(p);
            pdvFlash(p);
            try { navigator.vibrate && navigator.vibrate([200,100,200]); } catch(e){}
            setTimeout(function() { pdvDetenerAlarma(999999); }, 10000);
        };
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?><?php /**PATH D:\diegospizzaApp\diegospizza\resources\views/filament/pages/comandas.blade.php ENDPATH**/ ?>