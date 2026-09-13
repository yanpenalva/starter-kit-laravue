<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\{DB, Schema};

return new class () extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        DB::statement('CREATE INDEX users_active_created_at_idx ON users (active, created_at DESC)');
        DB::statement('CREATE INDEX users_unverified_idx ON users (email_verified_at) WHERE email_verified_at IS NULL');
        DB::statement('CREATE INDEX users_inactive_idx ON users (active) WHERE active = false');

        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        DB::statement('CREATE INDEX users_name_trgm_idx ON users USING gin (name gin_trgm_ops)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS users_active_created_at_idx');
        DB::statement('DROP INDEX IF EXISTS users_unverified_idx');
        DB::statement('DROP INDEX IF EXISTS users_inactive_idx');
        DB::statement('DROP INDEX IF EXISTS users_name_trgm_idx');
    }
};
