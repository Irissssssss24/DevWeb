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
        // create_offre_stage_table
        Schema::create('offre_stage', function (Blueprint $table) {
            $table->increments('id_offre');
            $table->string('titre', 150)->nullable();
            $table->text('description')->nullable();
            $table->text('competences')->nullable();
            $table->string('duree', 150)->nullable();
            $table->text('missions')->nullable();
            $table->unsignedInteger('id_entreprise')->nullable();
            $table->foreign('id_entreprise')
                  ->references('id_entreprise')->on('entreprise')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offre_stage');
    }
};
