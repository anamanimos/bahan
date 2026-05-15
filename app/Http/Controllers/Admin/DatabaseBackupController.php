<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class DatabaseBackupController extends Controller
{
    public function index()
    {
        // Ambil daftar file backup yang mungkin ada di storage/app
        $files = collect(Storage::disk('local')->files())
            ->filter(fn($file) => str_ends_with($file, '.zip') || str_ends_with($file, '.sql'))
            ->map(function($file) {
                return [
                    'name' => $file,
                    'size' => round(Storage::disk('local')->size($file) / 1024 / 1024, 2) . ' MB',
                    'date' => date('Y-m-d H:i:s', Storage::disk('local')->lastModified($file)),
                ];
            })
            ->sortByDesc('date');

        return view('pages.admin.backup.index', compact('files'));
    }

    public function runBackup(Request $request)
    {
        try {
            // Panggil command yang sudah kita buat sebelumnya
            $exitCode = Artisan::call('db:backup-telegram');
            
            if ($exitCode === 0) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Backup database berhasil dikirim ke Telegram.'
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat backup. Cek log untuk detailnya.'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function download($filename)
    {
        if (Storage::disk('local')->exists($filename)) {
            return Storage::disk('local')->download($filename);
        }
        
        return abort(404);
    }

    public function delete($filename)
    {
        if (Storage::disk('local')->exists($filename)) {
            Storage::disk('local')->delete($filename);
            return back()->with('success', 'File backup berhasil dihapus.');
        }
        
        return back()->with('error', 'File tidak ditemukan.');
    }
}
