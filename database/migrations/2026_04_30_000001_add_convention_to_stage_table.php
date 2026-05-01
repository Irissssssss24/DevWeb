<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stage', function (Blueprint $table) {
            if (!Schema::hasColumn('stage', 'convention_validee')) {
                // null = pas encore examinée, true = validée, false = refusée
                $table->boolean('convention_validee')->nullable()->after('date_fin');
            }

            if (!Schema::hasColumn('stage', 'remarque_convention')) {
                $table->text('remarque_convention')->nullable()->after('convention_validee');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stage', function (Blueprint $table) {
            if (Schema::hasColumn('stage', 'remarque_convention')) {
                $table->dropColumn('remarque_convention');
            }

            if (Schema::hasColumn('stage', 'convention_validee')) {
                $table->dropColumn('convention_validee');
            }
        });
    }
};
