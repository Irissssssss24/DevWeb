<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stage', function (Blueprint $table) {
            if (!Schema::hasColumn('stage', 'date_debut_proposee')) {
                $table->timestamp('date_debut_proposee')->nullable()->after('date_fin');
            }

            if (!Schema::hasColumn('stage', 'date_fin_proposee')) {
                $table->timestamp('date_fin_proposee')->nullable()->after('date_debut_proposee');
            }

            if (!Schema::hasColumn('stage', 'convention')) {
                $table->text('convention')->nullable()->after('lettre_motivation');
            }

            if (!Schema::hasColumn('stage', 'convention_signee')) {
                $table->text('convention_signee')->nullable()->after('convention');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stage', function (Blueprint $table) {
            foreach (['date_debut_proposee', 'date_fin_proposee', 'convention', 'convention_signee'] as $column) {
                if (Schema::hasColumn('stage', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
