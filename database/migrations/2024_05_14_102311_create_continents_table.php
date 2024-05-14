<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContinentTable extends Migration
{
    public function up()
    {
        Schema::create('continent', function (Blueprint $table) {
            $table->id('id');
            $table->string('continent');
            // Pas de timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('continent');
    }
}
