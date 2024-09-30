<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersInfoTypesTable extends Migration
{
    public function up()
    {
        Schema::create('users_info_types', function (Blueprint $table) {
            $table->id(); // Clé primaire
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Clé étrangère vers users avec cascade
            $table->foreignId('info_type_id')->constrained('info_types')->onDelete('cascade'); // Clé étrangère vers info_types avec cascade
        });
    }

    public function down()
    {
        Schema::dropIfExists('users_info_types'); // Correction du nom de la table
    }
}
