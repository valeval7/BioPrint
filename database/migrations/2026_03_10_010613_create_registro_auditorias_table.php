<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('registro_auditorias', function (Blueprint $table) {
      $table->id();

      $table->unsignedBigInteger('usuario_id')
        ->nullable();

      $table->enum('tipo_evento', [
        'auth_exitosa',
        'auth_fallida',
        'trabajo_liberado',
        'trabajo_cancelado',
        'inicio_sesion',
        'cierre_sesion',
        'cambio_acl',
      ])->comment('Clasificación del evento');

      $table->unsignedBigInteger('trabajo_id')
        ->nullable()
        ->comment('FK → trabajo_impresions.id');

      $table->string('direccion_ip', 45)->nullable();

      $table->text('detalle')
        ->nullable()
        ->comment('JSON con contexto adicional');

      $table->timestamp('creado_en')->useCurrent();

      $table->foreign('usuario_id')
        ->references('id')
        ->on('users')
        ->onUpdate('cascade')
        ->onDelete('set null');

      $table->foreign('trabajo_id')
        ->references('id')
        ->on('trabajo_impresions')
        ->onUpdate('cascade')
        ->onDelete('set null');

      $table->index('usuario_id',  'idx_auditoria_usuario');
      $table->index('tipo_evento', 'idx_auditoria_tipo');
      $table->index('trabajo_id',  'idx_auditoria_trabajo');
      $table->index('creado_en',   'idx_auditoria_fecha');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('registro_auditorias');
  }
};
