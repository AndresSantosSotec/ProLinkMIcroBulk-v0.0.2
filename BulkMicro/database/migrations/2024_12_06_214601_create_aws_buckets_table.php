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
        Schema::create('aws_buckets', function (Blueprint $table) {
            $table->id();
            $table->string('bucket_path'); // Ruta completa del bucket
            $table->string('file_name')->nullable(); // Archivo asociado para migración automática
            $table->string('access_key'); // Llave de acceso
            $table->string('secret_key'); // Llave secreta
            $table->string('region')->nullable(); // Región del bucket (opcional)
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
        Schema::dropIfExists('aws_buckets');
    }
};
