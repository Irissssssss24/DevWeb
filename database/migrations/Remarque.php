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
        // create_remarque_table
        Schema::create('remarque', function (Blueprint $table) {
            $table->increments('id_remarque');
            $table->text('contenu')->nullable();
            $table->timestamp('date')->nullable();
            $table->unsignedInteger('id_stage')->nullable();
            $table->unsignedInteger('id_utilisateur')->nullable();
            $table->foreign('id_stage')
                  ->references('id_stage')->on('stage')
                  ->onDelete('cascade');
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
        Schema::dropIfExists('remarque');
    }
};
