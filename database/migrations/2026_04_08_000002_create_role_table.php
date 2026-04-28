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
        // create_role_table
        Schema::create('role', function (Blueprint $table) {
            $table->uuid('id_utilisateur')->primary();
            $table->integer('administrateur')->default(0);
            $table->integer('etudiant')->default(0);
            $table->integer('entreprise')->default(0);
            $table->integer('tuteur')->default(0);
            $table->integer('jury')->default(0);
            $table->foreign('id_utilisateur')
                  ->references('id_utilisateur')->on('utilisateur')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role');
    }
};
