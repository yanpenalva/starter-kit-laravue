<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\{Artisan, DB, Log};

describe('CleanActivityLogCommand', function () {
    beforeEach(function () {
        DB::table('activity_log')->truncate();
    });

    it('clears all records from activity_log table', function () {
        DB::table('activity_log')->insert([
            [
                'log_name' => 'default',
                'description' => 'Log 1',
                'subject_type' => 'Test',
                'subject_id' => 1,
                'causer_type' => null,
                'causer_id' => null,
                'properties' => '{}',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'log_name' => 'default',
                'description' => 'Log 2',
                'subject_type' => 'Test',
                'subject_id' => 2,
                'causer_type' => null,
                'causer_id' => null,
                'properties' => '{}',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        expect(DB::table('activity_log')->count())->toBe(2);

        Log::shouldReceive('channel')
            ->once()
            ->with('activity_rotation')
            ->andReturnSelf();
        Log::shouldReceive('info')
            ->once()
            ->with('2 registros removidos de activity_log.');

        Artisan::call('clean:logs');

        expect(DB::table('activity_log')->count())->toBe(0);
    });

    it('does nothing if there are no records', function () {
        expect(DB::table('activity_log')->count())->toBe(0);

        Log::shouldReceive('channel')
            ->once()
            ->with('activity_rotation')
            ->andReturnSelf();
        Log::shouldReceive('info')
            ->once()
            ->with(Mockery::on(function (string $msg) {
                return str_starts_with($msg, 'Nenhum registro encontrado para limpeza. Data de execução: ');
            }));

        Artisan::call('clean:logs');

        expect(DB::table('activity_log')->count())->toBe(0);
    });
})->group('command');
