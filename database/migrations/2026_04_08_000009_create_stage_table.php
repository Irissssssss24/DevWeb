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
        // create_stage_table
        Schema::create('stage', function (Blueprint $table) {
            $table->increments('id_stage');
            $table->unsignedInteger('id_etudiant')->nullable();
            $table->unsignedInteger('id_offre')->nullable();
            $table->unsignedInteger('id_tuteur')->nullable(); // nullable pour SET NULL
            $table->string('statut', 50)->nullable();
            $table->timestamp('date_debut')->nullable();
            $table->timestamp('date_fin')->nullable();
            $table->text('lettre_motivation')->nullable();
            $table->foreign('id_etudiant')
                  ->references('id_etudiant')->on('etudiant')
                  ->onDelete('cascade');
            $table->foreign('id_offre')
                  ->references('id_offre')->on('offre_stage')
                  ->onDelete('cascade');
            $table->foreign('id_tuteur')
                  ->references('id_tuteur')->on('tuteur')
                  ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stage');
    }
};
