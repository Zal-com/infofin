<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaysTable extends Migration
{
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->integer('codePays')->comment('Code du pays');
            $table->string('nomPaysUK', 60)->nullable()->comment('Nom du pays en anglais');
            $table->string('nomPays', 60)->nullable()->comment('Nom du pays en français');
            $table->string('alpha2', 6)->nullable()->comment('Code alpha2 (ISO 3166-1)');
            $table->string('alpha3', 6)->nullable()->comment('Code alpha3 (ISO 3166-1)');
            $table->string('numIso', 6)->nullable()->comment('Code ISO (ISO 3166-1)');
            $table->string('stvnatn_code', 12)->nullable()->comment('Code nationalite');
            // Add other fields as necessary
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('pays');
    }
}
