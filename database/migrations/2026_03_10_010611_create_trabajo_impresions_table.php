<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('trabajo_impresions', function (Blueprint $table) {
      $table->id();

      $table->unsignedBigInteger('usuario_id')
        ->comment('FK → users.id');

      $table->string('nombre_trabajo', 255);

      $table->unsignedInteger('cups_trabajo_id')
        ->unique()
        ->comment('ID asignado por CUPS');

      $table->string('ruta_archivo_cifrado', 400);

      $table->enum('modo_impresion', ['bn', 'color']);

      $table->enum('estado', ['pendiente', 'liberado', 'cancelado', 'error'])
        ->default('pendiente');

      $table->unsignedSmallInteger('paginas')->nullable();

      $table->timestamp('creado_en')->useCurrent();
      $table->timestamp('liberado_en')->nullable();
      $table->timestamp('expira_en')->nullable();

      $table->foreign('usuario_id')
        ->references('id')
        ->on('users')
        ->onUpdate('cascade')
        ->onDelete('restrict');

      $table->index('usuario_id',  'idx_trabajos_usuario');
      $table->index('estado',      'idx_trabajos_estado');
      $table->index('creado_en',   'idx_trabajos_creado');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('trabajo_impresions');
  }
};
