<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginsHistoryTable extends Migration
{
    public function up()
    {
        Schema::create('logins_history', function (Blueprint $table) {
            $table->integer('LoginID')->primary();
            $table->string('Number', 10);
            $table->text('Xml');
            $table->integer('SubscriberID');
            $table->tinyInteger('Success');
            $table->timestamp('TimeStamp')->default(DB::raw('CURRENT_TIMESTAMP'));
            // Timestamps gérés par la colonne TimeStamp
        });
    }

    public function down()
    {
        Schema::dropIfExists('logins_history');
    }
}

