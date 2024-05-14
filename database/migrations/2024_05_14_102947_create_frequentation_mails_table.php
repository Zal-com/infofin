<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFrequentationMailTable extends Migration
{
    public function up()
    {
        Schema::create('frequentation_mail', function (Blueprint $table) {
            $table->id('id');
            $table->integer('idProject')->nullable();
            $table->integer('idSubscriber')->nullable();
            $table->dateTime('dateConsultation')->nullable();
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('frequentation_mail');
    }
}
