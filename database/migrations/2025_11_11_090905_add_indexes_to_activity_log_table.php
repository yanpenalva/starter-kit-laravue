<?php
declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class AddIndexesToActivityLogTable extends Migration
{
    public function up(): void
    {
        Schema::connection(config('activitylog.database_connection'))
            ->table(config('activitylog.table_name'), function (Blueprint $table): void {

                $table->index('created_at');
                $table->index('updated_at');

                $table->index(['subject_type', 'subject_id']);
                $table->index(['causer_type', 'causer_id']);
            });
    }

    public function down(): void
    {
        Schema::connection(config('activitylog.database_connection'))
            ->table(config('activitylog.table_name'), function (Blueprint $table): void {
                $table->dropIndex(['created_at']);
                $table->dropIndex(['updated_at']);
                $table->dropIndex(['subject_type', 'subject_id']);
                $table->dropIndex(['causer_type', 'causer_id']);
                $table->dropIndex(['event']);
                $table->dropIndex(['batch_uuid']);
            });
    }
}
