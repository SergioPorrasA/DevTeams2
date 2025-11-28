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
        Schema::create('evento', function (Blueprint $table) {
            $table->id('Id');
            $table->string('Nombre', 255);
            $table->text('Descripcion');
            $table->unsignedBigInteger('Id_juez')->nullable();
            $table->date('Fecha_inicio');
            $table->date('Fecha_fin');

            $table->foreign('Id_juez')->references('Id')->on('juez')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evento');
    }
};
