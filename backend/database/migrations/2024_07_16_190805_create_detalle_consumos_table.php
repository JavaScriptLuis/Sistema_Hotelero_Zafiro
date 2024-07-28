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
        Schema::create('detalle_consumos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('consumo_id');
            $table->unsignedBigInteger('producto_id');
            $table->integer('cantidad');
            $table->float('precio');
            $table->enum('estado', ['pagado', 'pendiente'])->default('pendiente');
            $table->string('metodo_pago')->nullable();
            $table->timestamps();
            $table->foreign('consumo_id')->references('id')->on('consumos');
            $table->foreign('producto_id')->references('id')->on('productos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_consumos');
    }
};
