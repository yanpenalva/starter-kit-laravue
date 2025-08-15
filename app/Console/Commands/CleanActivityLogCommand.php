<?php

declare(strict_types = 1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\{DB, Log};

final class CleanActivityLogCommand extends Command
{
    protected const LOG_CHANNEL = 'activity_rotation';
    protected const WITHOUT_RECORDS = 0;
    protected const TABLE_NAME = 'activity_log';
    protected const CHUNK_SIZE = 1000;
    protected const INITIAL_MESSAGE = 'Iniciando limpeza da tabela activity_log...';
    protected const NO_RECORDS_MESSAGE = 'Nenhum registro encontrado para limpeza. Data de execução: ';
    protected const CLEANUP_COMPLETE_MESSAGE = 'Limpeza concluída. Registros removidos: ';

    protected $signature = 'clean:logs';

    protected $description = 'Limpa registros antigos da tabela activity_log';

    public function handle(): int
    {
        $this->info(self::INITIAL_MESSAGE);

        $total = DB::table(self::TABLE_NAME)->count();

        if ($total === self::WITHOUT_RECORDS) {
            $this->info(self::NO_RECORDS_MESSAGE . now());
            Log::channel(self::LOG_CHANNEL)->info(self::NO_RECORDS_MESSAGE . now());

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        DB::table(self::TABLE_NAME)
            ->orderBy('id')
            ->chunkById(self::CHUNK_SIZE, function ($logs) use ($bar) {
                DB::table(self::TABLE_NAME)->whereIn('id', $logs->pluck('id'))->delete();
                $bar->advance(count($logs));
            });

        $bar->finish();
        $this->newLine();

        $this->info(self::CLEANUP_COMPLETE_MESSAGE . "{$total} registros removidos.");
        Log::channel(self::LOG_CHANNEL)->info("{$total} registros removidos de activity_log.");

        return self::SUCCESS;
    }
}
