<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'landlord';

    public function up(): void
    {
        Schema::connection('landlord')->create('tenant_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
            $table->unsignedInteger('monto')->default(0);
            $table->string('metodo')->default('transferencia');
            $table->string('referencia')->nullable();
            $table->string('comprobante')->nullable();
            $table->string('estado')->default('pendiente'); // pendiente, aprobado, rechazado
            $table->string('periodo')->nullable(); // mes facturado (ej: 2026-09)
            $table->text('notas')->nullable();
            $table->timestamp('pagado_en')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('tenant_payments');
    }
};
