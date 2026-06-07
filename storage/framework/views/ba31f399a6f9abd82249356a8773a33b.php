<div>
    
    <div class="bg-gradient-to-br from-red-700 via-red-600 to-orange-500 text-white">
        <div class="max-w-4xl mx-auto px-4 py-6">
            <a href="<?php echo e(route('menu')); ?>" wire:navigate class="inline-flex items-center text-red-100 hover:text-white mb-4 transition">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver al menú
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold">Finalizar Pedido</h1>
            <p class="text-red-100 text-sm mt-1">Confirma tus datos y realiza tu pedido</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 py-6 sm:py-8">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pedidoCreado): ?>
            <div class="text-center py-12 max-w-md mx-auto">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">¡Pedido Confirmado!</h1>
                <p class="text-gray-500 mb-2">Tu pedido <strong class="text-red-600">#<?php echo e($pedidoId); ?></strong> ha sido recibido</p>
                <div class="bg-gray-50 rounded-xl p-4 mb-4 text-sm space-y-1">
                    <p class="text-gray-600">Total: <strong class="text-gray-800">$<?php echo e(number_format($total, 0, ',', '.')); ?></strong></p>
                    <p class="text-gray-600">Pago: <strong class="text-gray-800"><?php echo e(ucfirst($metodoPago)); ?></strong></p>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($metodoPago === 'transferencia'): ?>
                    <?php $s = \App\Models\NegocioSetting::getSettings(); ?>
                    <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 12px; padding: 16px; margin-bottom: 16px; text-align: left;">
                        <p style="margin: 0 0 10px; font-size: 14px; font-weight: 700; color: #0369a1;">🏦 Datos para transferencia</p>
                        <div style="font-size: 13px; color: #0c4a6e; display: flex; flex-direction: column; gap: 6px;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($s->llave): ?>
                                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e0f2fe;">
                                    <span style="font-weight: 600;">Llave (Bancolombia)</span>
                                    <span><?php echo e($s->llave); ?></span>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($s->nequi): ?>
                                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e0f2fe;">
                                    <span style="font-weight: 600;">Nequi</span>
                                    <span><?php echo e($s->nequi); ?></span>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($s->daviplata): ?>
                                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e0f2fe;">
                                    <span style="font-weight: 600;">Daviplata</span>
                                    <span><?php echo e($s->daviplata); ?></span>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <p style="margin: 10px 0 0; font-size: 12px; color: #0369a1; text-align: center;">📸 Envía el comprobante de pago por WhatsApp</p>
                    </div>
                    <a href="<?php echo e($whatsappComprobanteUrl); ?>" target="_blank"
                        class="inline-flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white px-8 py-3.5 rounded-xl font-bold transition-all active:scale-95 shadow-sm w-full mb-3">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Enviar comprobante
                    </a>
                <?php else: ?>
                    <a href="<?php echo e($whatsappUrl); ?>" target="_blank"
                        class="inline-flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white px-8 py-3.5 rounded-xl font-bold transition-all active:scale-95 shadow-sm w-full mb-3">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Enviar mensaje al Restaurante
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <a href="<?php echo e(route('menu')); ?>" wire:navigate class="inline-block bg-gradient-to-r from-red-600 to-red-500 text-white px-8 py-3 rounded-xl font-bold hover:from-red-700 hover:to-red-600 transition-all active:scale-95 shadow-sm w-full">
                    Seguir ordenando
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 sm:gap-6 lg:gap-8">
                
                <div class="lg:col-span-3 space-y-4 sm:space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
                        <h2 class="text-base sm:text-lg font-bold text-gray-800 mb-4">📋 Tus datos</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono *</label>
                                <input type="tel" wire:model.live="telefono" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 <?php $__errorArgs = ['telefono'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 bg-red-50 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="555-1234-5678" autofocus>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['telefono'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                                <input type="text" wire:model.live="nombre" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 bg-red-50 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Tu nombre">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clienteInfo): ?>
                            <div class="bg-orange-50 border border-orange-100 rounded-xl p-4 text-sm space-y-3">
                                <div class="flex gap-4 items-center flex-wrap">
                                    <span class="px-2 py-0.5 rounded-lg text-xs font-bold"
                                        style="background: <?php echo e($clienteInfo['clasificacion'] === 'elite' ? '#fef3c7' : ($clienteInfo['clasificacion'] === 'frecuente' ? '#ffedd5' : '#f3f4f6')); ?>; color: <?php echo e($clienteInfo['clasificacion'] === 'elite' ? '#92400e' : ($clienteInfo['clasificacion'] === 'frecuente' ? '#9a3412' : '#6b7280')); ?>;">
                                        <?php echo e($clienteInfo['clasificacion_label']); ?>

                                    </span>
                                    <span class="text-gray-600">📦 <strong><?php echo e($clienteInfo['total_pedidos']); ?></strong> pedidos</span>
                                    <span class="text-gray-600">⭐ <strong><?php echo e($clienteInfo['puntos']); ?></strong> puntos</span>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($clienteInfo['recompensas'])): ?>
                                    <div style="border-top: 1px dashed #fed7aa; padding-top: 8px;">
                                        <p style="font-size: 12px; color: #9a3412; font-weight: 600; margin-bottom: 6px;">🏆 Elige tu recompensa:</p>
                                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 6px 10px; border-radius: 8px; font-size: 13px; <?php echo e($recompensaSeleccionadaIndex === null ? 'background: #fef3c7; font-weight: 700;' : ''); ?>">
                                            <input type="radio" name="recompensa" wire:click="seleccionarRecompensa(null)" <?php echo e($recompensaSeleccionadaIndex === null ? 'checked' : ''); ?> style="accent-color: #ea580c;">
                                            <span>❌ No usar puntos</span>
                                        </label>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $clienteInfo['recompensas']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 6px 10px; border-radius: 8px; font-size: 13px; <?php echo e($recompensaSeleccionadaIndex === $i ? 'background: #fef3c7; font-weight: 700;' : ''); ?>">
                                                <input type="radio" name="recompensa" wire:click="seleccionarRecompensa(<?php echo e($i); ?>)" <?php echo e($recompensaSeleccionadaIndex === $i ? 'checked' : ''); ?> style="accent-color: #ea580c;">
                                                <span><?php echo e(number_format($r['puntos'])); ?> pts →</span>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r['tipo'] === 'porcentaje'): ?>
                                                    <span style="background: #fef3c7; padding: 1px 8px; border-radius: 9999px; font-weight: 700;"><?php echo e($r['valor']); ?>% OFF</span>
                                                <?php else: ?>
                                                    <span style="background: #fef3c7; padding: 1px 8px; border-radius: 9999px; font-weight: 700;">$<?php echo e(number_format($r['valor'], 0, ',', '.')); ?> OFF</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </label>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div style="border-top: 1px dashed #fed7aa; padding-top: 8px;">
                                        <p style="font-size: 12px; color: #9ca3af; margin: 0;">Sigue acumulando puntos para canjear recompensas 🎯</p>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Conjunto *</label>
                                    <input wire:model.live="conjunto" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 <?php $__errorArgs = ['conjunto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 bg-red-50 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Nombre del conjunto">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['conjunto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Torre</label>
                                    <input wire:model.live="torre" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="N°">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Apto</label>
                                    <input wire:model.live="apto" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="N°">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                                <textarea wire:model.live="notas" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="¿Alguna instrucción especial?"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
                        <h2 class="text-base sm:text-lg font-bold text-gray-800 mb-4">💳 Método de pago</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <?php $metodosPago = \App\Models\NegocioSetting::getActivePaymentMethods(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $metodosPago; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $valor => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <label class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 <?php echo e($metodoPago === $valor ? 'border-red-500 bg-red-50' : 'border-gray-100 hover:border-gray-200 bg-gray-50'); ?>">
                                    <input type="radio" wire:model.live="metodoPago" value="<?php echo e($valor); ?>" class="text-red-600 focus:ring-red-500 sr-only">
                                    <span class="text-xl"><?php echo e($info['icon']); ?></span>
                                    <span class="font-medium text-sm text-gray-800"><?php echo e($info['label']); ?></span>
                                </label>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['metodoPago'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-2"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 sticky top-4">
                        <h2 class="text-base sm:text-lg font-bold text-gray-800 mb-4">📦 Resumen</h2>
                        <div class="divide-y divide-gray-50">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="py-3 flex justify-between items-start">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800 truncate"><?php echo e($item['nombre']); ?></p>
                                        <p class="text-xs text-gray-400">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item['variant_tamanio'] ?? null): ?>
                                                <span class="inline-block bg-orange-100 rounded px-1.5 py-0.5 text-[10px] font-medium" style="color: #FF8D08;"><?php echo e($item['variant_tamanio']); ?></span> ·
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item['mitades'] ?? null): ?>
                                                <span class="text-xs" style="color: #ea580c;"><?php echo e(collect($item['mitades'])->pluck('nombre')->implode(' / ')); ?></span> ·
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php echo e($item['cantidad']); ?> x $<?php echo e(number_format($item['precio'], 0, ',', '.')); ?>

                                        </p>
                                    </div>
                                    <span class="text-sm font-bold text-gray-800 flex-shrink-0 ml-2">$<?php echo e(number_format($item['precio'] * $item['cantidad'], 0, ',', '.')); ?></span>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>

                        <div class="border-t border-gray-100 mt-3 pt-3 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Subtotal</span>
                                <span>$<?php echo e(number_format($subtotal, 0, ',', '.')); ?></span>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($descuentoPuntos > 0 && $recompensaAplicada): ?>
                                <div class="flex justify-between text-sm text-green-600">
                                    <span>Descuento puntos</span>
                                    <span>-$<?php echo e(number_format($descuentoPuntos, 0, ',', '.')); ?></span>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div class="flex justify-between text-base font-bold pt-2 border-t border-gray-100">
                                <span>Total</span>
                                <span class="text-red-600">$<?php echo e(number_format($total, 0, ',', '.')); ?></span>
                            </div>
                        </div>

                        <button wire:click="procesarPedido" wire:loading.attr="disabled"
                            class="w-full mt-5 bg-gradient-to-r from-red-600 to-red-500 text-white py-3 rounded-xl font-bold hover:from-red-700 hover:to-red-600 transition-all duration-200 disabled:opacity-50 active:scale-[0.98] shadow-sm">
                            <span wire:loading.remove>Confirmar pedido</span>
                            <span wire:loading>Procesando...</span>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH D:\diegospizzaApp\diegospizza\resources\views/livewire/checkout.blade.php ENDPATH**/ ?>