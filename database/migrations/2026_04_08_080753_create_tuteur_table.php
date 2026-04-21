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
        // create_tuteur_table
        Schema::create('tuteur', function (Blueprint $table) {
            $table->increments('id_tuteur');
            $table->unsignedInteger('id_utilisateur')->nullable();
            $table->string('specialite', 100)->nullable();
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
        Schema::dropIfExists('tuteur');
    }
};
