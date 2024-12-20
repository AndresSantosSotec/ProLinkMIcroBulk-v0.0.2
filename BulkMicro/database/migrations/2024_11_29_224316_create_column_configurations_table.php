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


        // Crear tabla column_configurations
        Schema::create('column_configurations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_tipo'); // Referencia al ID de nomenclatura (nom)
            $table->string('column_name', 100); // Nombre de la columna en la BD
            $table->string('excel_column_name', 100); // Nombre asignado en el Excel
            $table->integer('column_number'); // Número de la columna en el Excel
            $table->timestamps();

            // Foreign key: Relación con la tabla nom
            $table->foreign('id_tipo')->references('id_tipo')->on('nom')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
};
