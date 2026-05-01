<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('offre_stage', 'duree')) {
            DB::statement('ALTER TABLE offre_stage ALTER COLUMN duree TYPE VARCHAR(150)');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('offre_stage', 'duree')) {
            DB::statement('ALTER TABLE offre_stage ALTER COLUMN duree TYPE VARCHAR(50)');
        }
    }
};
