<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContinentsTable extends Migration
{
    public function up()
    {
        Schema::create('continents', function (Blueprint $table) {
            $table->char('code', 2)->primary()->comment('Code du continent'); // Le champ code est une clé primaire
            $table->string('name', 255)->nullable(); // Nom du continent
        });
    }

    public function down()
    {
        Schema::dropIfExists('continents'); // Correction du nom de la table dans dropIfExists
    }
}
