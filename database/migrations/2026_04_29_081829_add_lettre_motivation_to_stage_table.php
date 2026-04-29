<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLettreMotivationToStageTable extends Migration
{
    public function up(): void
    {
        Schema::table('stage', function (Blueprint $table) {
            $table->text('lettre_motivation')->nullable()->after('statut');
        });
    }

    public function down(): void
    {
        Schema::table('stage', function (Blueprint $table) {
            $table->dropColumn('lettre_motivation');
        });
    }
}