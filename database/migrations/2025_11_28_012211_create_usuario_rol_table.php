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
        Schema::create('usuario_rol', function (Blueprint $table) {
            $table->unsignedBigInteger('Id_usuario');
            $table->unsignedBigInteger('Id_Rol');

            $table->primary(['Id_usuario', 'Id_Rol']);
            $table->foreign('Id_usuario')->references('Id')->on('usuario')->onDelete('cascade');
            $table->foreign('Id_Rol')->references('Id')->on('rol')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario_rol');
    }
};
