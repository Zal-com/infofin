<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganisationsTable extends Migration
{
    public function up()
    {
        Schema::create('Organisations', function (Blueprint $table) {
            $table->text('Nom'); // Assuming 'Nom' is the primary key or unique identifier
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('Organisations');
    }
}

