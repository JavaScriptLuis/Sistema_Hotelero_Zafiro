<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('habitacions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tipo_id');
            $table->unsignedBigInteger('sucursal_id');
            $table->unsignedBigInteger('piso_id');
            $table->string('numero');
            $table->string('descripcion');
            $table->string('estado_servicio')->default('disponible');
            $table->boolean('estado')->default(true);
            $table->enum('check_tipo', ['reservada', 'momento'])->default('momento');
            $table->enum('check_status', ['desocupado', 'ocupado', 'limpieza'])->default('desocupado');
            $table->timestamps();
            $table->foreign('sucursal_id')->references('id')->on('sucursals');
            $table->foreign('tipo_id')->references('id')->on('tipo_habitacions');
            $table->foreign('piso_id')->references('id')->on('pisos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void    
    {
        Schema::dropIfExists('habitacions');
    }
};
