<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->json('contact_ulb');
            $table->json('contact_ext');
            $table->integer('periodicity');
            $table->longText('admission_requirements');
            $table->longText('funding');
            $table->longText('apply_instructions');
            $table->foreignId('poster_id');
            $table->boolean('is_view_for_mail');
            //$table->dateTime('date_lessor');
            $table->boolean('info_lessor');
            $table->integer('visit_count')->default(0);
            $table->foreignId('last_update_user_id');
            $table->foreignId('country_id');
            $table->foreignId('continent_id');
            $table->smallInteger('status')->default(1);
            $table->boolean('is_big')->default(false);
            $table->text('long_description');
            $table->string('short_description', 500);
            $table->boolean('is_draft')->default(false);
            $table->string('origin_url')->nullable();
            $table->boolean('is_in_next_email')->default(true);
            $table->timestamps();

            //Relations
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->foreign('poster_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('last_update_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('continent_id')->references('id')->on('continents')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }
}

