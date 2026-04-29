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
        // create_etudiant_table
        Schema::create('etudiant', function (Blueprint $table) {
            $table->increments('id_etudiant');
            $table->uuid('id_utilisateur')->nullable();
            $table->string('filiere', 100)->nullable();
            $table->string('niveau', 50)->nullable();
            $table->text('cv')->nullable();
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
        Schema::dropIfExists('etudiant');
    }
};
