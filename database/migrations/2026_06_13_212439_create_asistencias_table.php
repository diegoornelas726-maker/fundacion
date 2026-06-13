<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->foreignId('beneficiario_id')->nullable()->constrained('beneficiarios')->cascadeOnDelete();
            $table->string('nombre_visitante')->nullable();
            $table->boolean('presente')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['fecha', 'beneficiario_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
