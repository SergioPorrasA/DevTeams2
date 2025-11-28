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
        Schema::create('rol_permiso', function (Blueprint $table) {
            $table->unsignedBigInteger('Id_rol');
            $table->unsignedBigInteger('Id_permiso');

            $table->primary(['Id_rol', 'Id_permiso']);
            $table->foreign('Id_rol')->references('Id')->on('rol')->onDelete('cascade');
            $table->foreign('Id_permiso')->references('Id')->on('permiso')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rol_permiso');
    }
};
