<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->bigIncrements('id'); // Clé primaire avec auto-increment
            $table->char('code', 2)->unique()->comment('Code à deux lettres du pays (ISO 3166-1 alpha-2)'); // Code alpha-2
            $table->string('name', 64)->comment('Nom du pays en français'); // Nom du pays
            $table->string('full_name', 128)->comment('Nom complet du pays en français'); // Nom complet
            $table->char('iso3', 3)->comment('Code à trois lettres du pays (ISO 3166-1 alpha-3)'); // Code ISO alpha-3
            $table->smallInteger('numero')->unsigned()->comment('Numéro à trois chiffres du pays (ISO 3166-1 numérique)'); // Numéro ISO
            $table->char('continent_code', 2)->comment('Code du continent'); // Référence au code du continent
            $table->integer('show_order')->default(900)->comment("Ordre d'affichage"); // Ordre d'affichage

            // Définir les relations
            $table->foreign('continent_code')->references('code')->on('continents')->onUpdate('cascade'); // Clé étrangère vers continents avec mise à jour en cascade
        });
    }

    public function down()
    {
        Schema::dropIfExists('countries'); // Correction du nom de la table
    }
}
