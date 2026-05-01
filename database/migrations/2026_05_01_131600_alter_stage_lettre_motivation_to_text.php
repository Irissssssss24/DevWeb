<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('stage', 'lettre_motivation')) {
            DB::statement('ALTER TABLE stage ALTER COLUMN lettre_motivation TYPE TEXT');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('stage', 'lettre_motivation')) {
            DB::statement('ALTER TABLE stage ALTER COLUMN lettre_motivation TYPE VARCHAR(50)');
        }
    }
};
