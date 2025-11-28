<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('participante', function (Blueprint $table) {
            $table->id('Id');
            $table->unsignedBigInteger('Usuario_id');
            $table->string('No_Control', 20)->unique();
            $table->unsignedBigInteger('Carrera_id');
            $table->string('Nombre', 255);
            $table->string('Correo', 255)->unique();
            $table->string('telefono', 15);

            $table->foreign('Usuario_id')->references('Id')->on('usuario')->onDelete('cascade');
            $table->foreign('Carrera_id')->references('Id')->on('carrera')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participante');
    }
};
