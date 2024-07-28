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
        Schema::create('registros', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('habitacion_id');
            $table->unsignedBigInteger('personal_id');
            $table->string('fecha_inicio');
            $table->string('fecha_fin');
            $table->string('hora_inicio');
            $table->string('hora_fin');
            $table->string('observacion')->nullable();
            $table->float('adelanto')->nullable();
            $table->string('check_in');
            $table->string('check_out')->nullable();
            $table->integer('total_horas')->default(0);
            $table->boolean('hora_extendido')->default(false);
            $table->enum('estado', ['activa','terminada'])->default('activa');
            $table->float('total_pago')->default(0);
            $table->timestamps();
            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->foreign('habitacion_id')->references('id')->on('habitacions');
            $table->foreign('personal_id')->references('id')->on('personals');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros');
    }
};
