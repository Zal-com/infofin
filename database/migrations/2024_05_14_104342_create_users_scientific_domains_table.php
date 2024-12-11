<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersScientificDomainsTable extends Migration
{
    public function up()
    {
        Schema::create('users_scientific_domains', function (Blueprint $table) {
            $table->id(); // Clé primaire
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Clé étrangère vers users avec cascade
            $table->foreignId('scientific_domain_id')->constrained('scientific_domains')->onDelete('cascade'); // Clé étrangère vers scientific_domains avec cascade
        });
    }

    public function down()
    {
        Schema::dropIfExists('users_scientific_domains'); // Correction du nom de la table
    }
}
