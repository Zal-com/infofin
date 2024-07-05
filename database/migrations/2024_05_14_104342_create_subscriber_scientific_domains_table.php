<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriberScientificDomainsTable extends Migration
{
    public function up()
    {
        Schema::create('users_scientific_domains', function (Blueprint $table) {
           $table->id();
           $table->foreignId('user_id');
           $table->foreignId('scientific_domain_id');

           //Relations
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('scientific_domain_id')->references('id')->on('scientific_domains');
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscribers_scientific_domains');
    }
}
