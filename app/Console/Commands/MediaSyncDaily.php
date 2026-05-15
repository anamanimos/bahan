<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MediaSyncService;

class MediaSyncDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:sync-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi seluruh media lokal ke cloud (R2/MinIO) secara otomatis';

    /**
     * Execute the console command.
     */
    public function handle(MediaSyncService $syncService)
    {
        $this->info('Memulai sinkronisasi media harian...');
        
        $files = $syncService->getLocalFiles();
        $total = count($files);
        
        if ($total === 0) {
            $this->info('Tidak ada file untuk disinkronkan.');
            return 0;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($files as $file) {
            $syncService->syncFile($file);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Sinkronisasi {$total} file selesai.");
        
        return 0;
    }
}
