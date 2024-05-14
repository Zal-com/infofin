<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemandeAvisTable extends Migration
{
    public function up()
    {
        Schema::create('demandeAvis', function (Blueprint $table) {
            $table->id('id');
            $table->integer('ProjectID');
            $table->integer('UserID');
            $table->char('Concerne', 1);
            $table->char('complet', 1);
            $table->char('IntroductionDossier', 1);
            $table->char('Helpfull', 1);
            $table->char('Q5', 1);
            $table->char('Q6', 1);
            $table->char('Q7', 1);
            $table->char('Q8', 1);
            $table->char('Q9', 1);
            $table->char('Q10', 1);
            $table->timestamp('date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('Commentaire', 1500);
            // Timestamps gérés par la colonne date
        });
    }

    public function down()
    {
        Schema::dropIfExists('demandeAvis');
    }
}

