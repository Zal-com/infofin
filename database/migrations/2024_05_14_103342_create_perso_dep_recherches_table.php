<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersoDepRechercheTable extends Migration
{
    public function up()
    {
        Schema::create('perso_DepRecherche', function (Blueprint $table) {
            $table->integer('id');
            $table->string('nom', 255)->nullable();
            $table->string('prenom', 255)->nullable();
            $table->string('mail', 255)->nullable();
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('perso_DepRecherche');
    }
}

