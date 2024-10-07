<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('info_sessions_scientific_domains', function (Blueprint $table) {
            $table->id(); // Clé primaire
            $table->foreignId('info_session_id')->constrained('info_sessions')->onDelete('cascade'); // Clé étrangère vers info_session
            $table->foreignId('scientific_domain_id')->constrained('scientific_domains')->onDelete('cascade'); // Clé étrangère vers countries

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('info_sessions_scientific_domains');
    }
};
