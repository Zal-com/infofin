<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id('ProjectID');
            $table->string('Name');
            $table->string('Organisation');
            $table->string('OrganisationReference');
            $table->date('Deadline');
            $table->tinyInteger('Continuous');
            $table->string('Justificatif', 50)->nullable();
            $table->date('Deadline2')->nullable();
            $table->tinyInteger('Continuous2');
            $table->string('Justificatif2', 50)->nullable();
            $table->text('ShortDescription');
            $table->text('LongDescription');
            $table->string('ContactULBName');
            $table->string('ContactULBAddress');
            $table->string('ContactULBEmail', 70)->nullable();
            $table->string('ContactULBPhone', 50);
            $table->string('ContactULBWebPage');
            $table->string('ContactExtName');
            $table->string('ContactExtAddress');
            $table->string('ContactExtEmail', 50);
            $table->string('ContactExtPhone', 50);
            $table->string('ContactExtWebPage');
            $table->tinyInteger('Periodicity')->default(0);
            $table->text('AdmissionRequirements');
            $table->text('Financement');
            $table->text('PourPostuler');
            $table->tinyInteger('Active')->default(0);
            $table->tinyInteger('LangID')->default(1);
            $table->dateTime('CreateTimeStamp');
            $table->timestamp('TimeStamp')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('UserID');
            $table->integer('LastUpdateUserID');
            $table->timestamps();  // Adds created_at and updated_at columns
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }
}

