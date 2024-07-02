<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContinentsTable extends Migration
{
    public function up()
    {
        Schema::create('continents', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
        });
    }

    public function down()
    {
        Schema::dropIfExists('continent');
    }
}
