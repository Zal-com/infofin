<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFrequentationMailTable extends Migration
{
    public function up()
    {
        Schema::create('visits_rate_mail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id');
            $table->foreignId('subscriber_id');
            $table->dateTime('date_consult')->nullable();
            // Pas de timestamps

            $table->foreign('subscriber_id')->references('id')->on('users');
            $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    public function down()
    {
        Schema::dropIfExists('frequentation_mail');
    }
}
