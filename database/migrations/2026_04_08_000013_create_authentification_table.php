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
        // create_authentification_table
        Schema::create('authentification', function (Blueprint $table) {
            $table->increments('id_auth');
            $table->uuid('id_utilisateur')->nullable();
            $table->string('code_2fa', 10)->nullable();
            $table->timestamp('date_expiration')->nullable();
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
        Schema::dropIfExists('authentification');
    }
};
