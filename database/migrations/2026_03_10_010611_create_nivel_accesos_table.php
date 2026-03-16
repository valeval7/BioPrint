<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('nivel_accesos', function (Blueprint $table) {
      $table->tinyIncrements('id');
      $table->string('nombre', 60)->unique()->comment('Básico, Estándar, Premium');
      $table->enum('modo_impresion', ['bn', 'color']);
      $table->string('descripcion', 255)->nullable();
      $table->timestamp('creado_en')->useCurrent();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('nivel_accesos');
  }
};
