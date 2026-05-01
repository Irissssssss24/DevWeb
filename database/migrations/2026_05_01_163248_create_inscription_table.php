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
        // create_utilisateur_table
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->json('data'); // toutes les infos utilisateur
            $table->string('statut')->default('en_attente'); // en_attente, accepte, refuse
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscriptions');
    }
};
