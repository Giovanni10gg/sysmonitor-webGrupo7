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
        Schema::create('simulaciones', function (Blueprint $table) {
    $table->id();

    $table->string('algoritmo');

    $table->decimal('promedio_espera', 8, 2);

    $table->decimal('promedio_retorno', 8, 2);

    $table->timestamps();
   });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulaciones');
    }
};
