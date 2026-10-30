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
        Schema::create('logs', function (Blueprint $table) {
        $table->id();                                           // ID único del registro de bitácora
        $table->unsignedBigInteger('user_id')->nullable();     // ID del usuario que ejecutó la acción (nullable por si no ha iniciado sesión)
        $table->string('action');                              // Descripción de la acción (ej: "SIGKILL", "Lanzar proceso")
        $table->integer('target_process_pid')->nullable();     // PID afectado
        $table->text('result');                                // Resultado (ej: "Éxito", "Permiso denegado")
        $table->timestamps();                                   // Fecha y hora exacta del evento
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
