<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id(); // Clé primaire
            $table->string('title', 255); // Titre du projet
            $table->json('contact_ulb'); // JSON pour les contacts ULB
            $table->json('contact_ext'); // JSON pour les contacts externes
            $table->json('deadlines'); // JSON pour les dates limites
            $table->longText('admission_requirements')->nullable(); // Conditions d'admission
            $table->longText('funding')->nullable(); // Détails du financement
            $table->longText('apply_instructions')->nullable(); // Instructions pour postuler
            $table->foreignId('poster_id')->constrained('users')->onDelete('cascade'); // Clé étrangère vers users
            $table->integer('visit_count')->default(0); // Nombre de visites par défaut 0
            $table->foreignId('last_update_user_id')->constrained('users')->onDelete('cascade'); // Clé étrangère vers users
            $table->smallInteger('status')->default(1)->comment("-1 = Archive, 0 = Inactif, 1 = Actif, 2 = ?"); // Statut
            $table->boolean('is_big')->default(false); // Indicateur pour grands projets
            $table->text('long_description'); // Description longue
            $table->string('short_description', 500); // Description courte
            $table->string('origin_url')->nullable(); // URL d'origine
            $table->boolean('is_in_next_email')->default(true); // Inclusion dans le prochain email
            $table->foreignId('organisation_id')->nullable()->constrained()->onDelete('cascade'); // Clé étrangère vers organisations
            $table->string('Organisation', 255)->nullable(); // Organisation
            $table->string('OrganisationReference', 255)->nullable(); // Référence de l'organisation
            $table->boolean('InfoULB')->nullable(); // Information ULB
            $table->boolean('SeanceFin')->nullable(); // Séance fin
            $table->string('Pays', 255)->nullable(); // Pays
            $table->timestamps(); // Colonnes created_at et updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
