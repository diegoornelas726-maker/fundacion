<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apoyos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiario_id')->constrained('beneficiarios')->onDelete('cascade');
            $table->string('tipo_apoyo');
            $table->text('descripcion')->nullable();
            $table->date('fecha_apoyo');
            $table->decimal('monto', 10, 2)->nullable();
            $table->enum('estado', ['Entregado', 'Pendiente', 'Cancelado'])->default('Entregado');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apoyos');
    }
};