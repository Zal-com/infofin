<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribersScientificDomainsTable extends Migration
{
    public function up()
    {
        Schema::create('subscribers_scientific_domains', function (Blueprint $table) {
            $table->id('ID');
            $table->integer('SubscriberID');
            $table->integer('ScientificDomainID');
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscribers_scientific_domains');
    }
}
