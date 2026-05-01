<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
        {
            Schema::table('stage', function (Blueprint $table) {
                $table->timestamp('date_debut_proposee')->nullable()->after('date_fin');
                $table->timestamp('date_fin_proposee')->nullable()->after('date_debut_proposee');
                $table->text('convention')->nullable()->after('lettre_motivation');
                $table->text('convention_signee')->nullable()->after('convention');
            });
        }

        public function down(): void
        {
            Schema::table('stage', function (Blueprint $table) {
                $table->dropColumn(['date_debut_proposee', 'date_fin_proposee', 'convention', 'convention_signee']);
            });
        }
};
