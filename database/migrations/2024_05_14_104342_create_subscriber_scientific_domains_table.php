<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribersScientificDomainsTable extends Migration
{
    public function up()
    {
        Schema::create('users_scientific_domains', function (Blueprint $table) {
            $table->id('id');
            $table->integer('user_id')->references("id")->on("users");
            $table->integer('scientific_domain_id')->references('id')->on("scientific_domain");
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('users_scientific_domains');
    }
}
