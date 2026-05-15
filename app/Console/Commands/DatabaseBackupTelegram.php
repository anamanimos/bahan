<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DatabaseBackupTelegram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup-telegram';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database ke Telegram Bot secara otomatis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if (!$token || !$chatId) {
            $this->error('TELEGRAM_BOT_TOKEN atau TELEGRAM_CHAT_ID belum dikonfigurasi di .env');
            return 1;
        }

        $this->info('Memulai backup database...');

        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');
        $dbHost = env('DB_HOST', '127.0.0.1');
        
        $filename = "backup_" . $dbName . "_" . date('Y-m-d_H-i-s') . ".sql";
        $filePath = storage_path("app/" . $filename);

        // Deteksi Path mysqldump berdasarkan OS
        if (PHP_OS_FAMILY === 'Windows') {
            $mysqldumpPath = 'D:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe'; 
        } else {
            $mysqldumpPath = 'mysqldump'; // Di Ubuntu biasanya sudah ada di PATH
        }

        $errorLogPath = storage_path("app/backup_error.log");

        // Gunakan escapeshellarg untuk keamanan di kedua OS
        $command = sprintf(
            '%s --user=%s --password=%s --host=%s %s > %s 2> %s',
            PHP_OS_FAMILY === 'Windows' ? '"' . $mysqldumpPath . '"' : escapeshellarg($mysqldumpPath),
            escapeshellarg($dbUser),
            escapeshellarg($dbPass),
            escapeshellarg($dbHost),
            escapeshellarg($dbName),
            escapeshellarg($filePath),
            escapeshellarg($errorLogPath)
        );

        $output = [];
        $returnVar = null;
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $errorMsg = file_exists($errorLogPath) ? file_get_contents($errorLogPath) : 'Unknown Error';
            if (file_exists($errorLogPath)) unlink($errorLogPath);
            if (file_exists($filePath)) unlink($filePath); // Hapus file kosong/rusak

            $this->error('Gagal melakukan dump database: ' . $errorMsg);
            Log::error('DB Backup Failed: ' . $errorMsg);
            return 1;
        }

        if (file_exists($errorLogPath)) unlink($errorLogPath);

        $finalPath = $filePath;
        $finalFilename = $filename;

        $this->info('Mengirim backup ke Telegram...');

        try {
            $response = Http::attach(
                'document', 
                file_get_contents($finalPath), 
                $finalFilename
            )->post("https://api.telegram.org/bot{$token}/sendDocument", [
                'chat_id' => $chatId,
                'caption' => "✅ Backup Database SQL\n📅 Tanggal: " . date('d M Y H:i') . "\n📂 Database: " . $dbName
            ]);

            if ($response->successful()) {
                $this->info('Backup berhasil terkirim!');
                // Jangan hapus instan agar muncul di tabel UI
            } else {
                $this->error('Gagal mengirim ke Telegram: ' . $response->body());
                Log::error('Telegram Backup Failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
            Log::error('Telegram Backup Exception: ' . $e->getMessage());
        }

        // Pembersihan Otomatis: Hapus backup yang lebih lama dari 7 hari di server
        $allFiles = \Storage::disk('local')->files();
        foreach ($allFiles as $file) {
            if (str_starts_with($file, 'backup_') && (str_ends_with($file, '.sql') || str_ends_with($file, '.zip'))) {
                $lastModified = \Storage::disk('local')->lastModified($file);
                if (time() - $lastModified > (86400 * 7)) { // 7 hari
                    \Storage::disk('local')->delete($file);
                }
            }
        }

        return 0;
    }
}
