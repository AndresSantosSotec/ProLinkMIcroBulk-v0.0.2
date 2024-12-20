<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('local_paths', function (Blueprint $table) {
            $table->string('config_type')->after('id')->comment('Tipo de configuración: asociados, captaciones, colocaciones');
        });

        Schema::table('aws_buckets', function (Blueprint $table) {
            $table->string('config_type')->after('id')->comment('Tipo de configuración: asociados, captaciones, colocaciones');
        });
    }

    public function down()
    {
        Schema::table('local_paths', function (Blueprint $table) {
            $table->dropColumn('config_type');
        });

        Schema::table('aws_buckets', function (Blueprint $table) {
            $table->dropColumn('config_type');
        });
    }
};

