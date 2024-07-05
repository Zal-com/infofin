<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateVisitsRateMailTable extends Migration
{
    public function up()
    {
        Schema::create('visits_rate_mail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('project_id');
            $table->dateTime('date_consult')->default(DB::raw('CURRENT_TIMESTAMP'));

            //Relations
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    public function down()
    {
        Schema::dropIfExists('visits_rate_mails');
    }
}

