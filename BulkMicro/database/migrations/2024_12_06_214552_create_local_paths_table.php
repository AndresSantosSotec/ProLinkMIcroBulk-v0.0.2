<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('local_paths', function (Blueprint $table) {
            $table->id();
            $table->string('path'); // Ruta de la carpeta local
            $table->string('file_name')->nullable(); // Archivo asociado para migración automática
            $table->string('description')->nullable(); // Descripción opcional
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('local_paths');
    }
};
