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
        Schema::create('proyecto', function (Blueprint $table) {
            $table->id('Id');
            $table->unsignedBigInteger('Id_equipo');
            $table->unsignedBigInteger('Id_Calificacion')->nullable();
            $table->string('Categoria', 255);
            $table->unsignedBigInteger('Id_evento');
            $table->unsignedBigInteger('Id_asesor')->nullable();
            $table->unsignedBigInteger('Id_avance')->nullable();
            $table->unsignedBigInteger('Id_repositorio')->nullable();
            $table->string('nombre', 255);

            $table->foreign('Id_equipo')->references('Id')->on('equipo')->onDelete('cascade');
            $table->foreign('Id_evento')->references('Id')->on('evento')->onDelete('cascade');
            $table->foreign('Id_Calificacion')->references('Id')->on('calificacion')->onDelete('set null');
            $table->foreign('Id_asesor')->references('Id')->on('asesor')->onDelete('set null');
            $table->foreign('Id_avance')->references('Id')->on('avance')->onDelete('set null');
            $table->foreign('Id_repositorio')->references('Id')->on('repositorio')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyecto');
    }
};
