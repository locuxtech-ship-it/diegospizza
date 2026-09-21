<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'landlord';

    public function up(): void
    {
        Schema::connection('landlord')->create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('nombre');
            $table->unsignedInteger('precio_mensual')->default(0);
            $table->unsignedInteger('max_usuarios')->default(1);
            $table->boolean('activo')->default(true);
            $table->string('descripcion')->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });

        Schema::connection('landlord')->create('plan_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->string('feature_key');
            $table->boolean('enabled')->default(true);
            $table->json('limits')->nullable();
            $table->unique(['plan_id', 'feature_key']);
        });

        Schema::connection('landlord')->table('tenants', function (Blueprint $table) {
            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
            $table->string('plan_status')->default('activo');
            $table->date('plan_expira')->nullable();
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->table('tenants', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn(['plan_id', 'plan_status', 'plan_expira']);
        });
        Schema::connection('landlord')->dropIfExists('plan_features');
        Schema::connection('landlord')->dropIfExists('plans');
    }
};
