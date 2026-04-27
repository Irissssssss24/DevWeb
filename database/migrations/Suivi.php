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
        // create_suivi_table
        Schema::create('suivi', function (Blueprint $table) {
            $table->increments('id_suivi');
            $table->text('avancement')->nullable();
            $table->timestamp('date')->nullable();
            $table->unsignedInteger('id_stage')->nullable();
            $table->foreign('id_stage')
                  ->references('id_stage')->on('stage')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suivi');
    }
};
