<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribersTable extends Migration
{
    public function up()
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id('SubscriberID');
            $table->string('Number', 20);
            $table->string('StudentNumber', 10);
            $table->string('FirstName', 50);
            $table->string('LastName', 50);
            $table->string('Email', 50);
            $table->integer('UserID')->index();
            $table->tinyInteger('LangID')->default(1);
            $table->timestamp('TimeStamp')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->tinyInteger('receveMail')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscribers');
    }
}

