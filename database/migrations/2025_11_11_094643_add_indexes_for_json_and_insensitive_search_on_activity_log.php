<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\{DB, Schema};

final class AddIndexesForJsonAndInsensitiveSearchOnActivityLog extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE activity_log
            ALTER COLUMN properties
            TYPE jsonb
            USING properties::jsonb;
        ");

        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_activity_log_properties
            ON activity_log
            USING GIN (properties jsonb_path_ops);
        ");

        if (Schema::hasTable('users')) {
            DB::statement("
                CREATE INDEX IF NOT EXISTS idx_users_name_lower
                ON users (LOWER(name));
            ");
        }
    }

    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS idx_activity_log_properties;");
        DB::statement("DROP INDEX IF EXISTS idx_users_name_lower;");
    }
}
