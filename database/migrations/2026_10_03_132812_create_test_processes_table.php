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
      Schema::create('test_processes', function (Blueprint $table) {
        $table->id();                                 // ID autoincrementable de la tabla
        $table->integer('pid')->unique();             // Identificador único del proceso en el Kernel de Linux
        $table->string('command');                   // Comando ejecutado (ej: "sleep 600")
        $table->timestamps();                         // Campos created_at y updated_at (fecha/hora de lanzamiento)
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_processes');
    }
};
