<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // create_entreprise_table
        Schema::create('entreprise', function (Blueprint $table) {
            $table->increments('id_entreprise');
            $table->uuid('id_utilisateur')->nullable();
            $table->string('nom_entreprise', 150)->nullable();
            $table->text('adresse')->nullable();
            $table->string('secteur', 100)->nullable();
            $table->string('siret', 100)->nullable();
            $table->foreign('id_utilisateur')
                  ->references('id_utilisateur')->on('utilisateur')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entreprise');
    }
};
