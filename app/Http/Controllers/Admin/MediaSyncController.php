<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MediaSyncService;
use Illuminate\Http\Request;

class MediaSyncController extends Controller
{
    protected $syncService;

    public function __construct(MediaSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function index(Request $request)
    {
        $files = $this->syncService->getLocalFiles();

        if ($request->has('json')) {
            return response()->json(['files' => array_values($files)]);
        }

        $mediaList = [];
        foreach ($files as $file) {
            $mediaList[] = $this->syncService->getFileStatus($file);
        }

        return view('pages.admin.media-sync.index', compact('mediaList'));
    }

    public function sync(Request $request)
    {
        $path = $request->path;
        if (!$path) {
            return response()->json(['status' => 'error', 'message' => 'Path is required'], 400);
        }

        $result = $this->syncService->syncFile($path);

        if ($result['error']) {
            return response()->json(['status' => 'error', 'message' => $result['error']], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => "File {$path} berhasil disinkronkan.",
            'data' => $result
        ]);
    }

    public function syncAll()
    {
        $files = $this->syncService->getLocalFiles();

        foreach ($files as $file) {
            \App\Jobs\SyncMediaJob::dispatch($file);
        }

        return response()->json([
            'status' => 'success',
            'message' => count($files) . " file sedang disinkronkan di latar belakang."
        ]);
    }

    public function deleteLocal(Request $request)
    {
        $path = $request->path;
        $result = $this->syncService->deleteLocal($path);

        if ($result) {
            return response()->json([
                'status' => 'success',
                'message' => "File lokal {$path} berhasil dihapus."
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => "Gagal menghapus file lokal. Pastikan file sudah tersinkron ke cloud."
        ], 400);
    }

    public function deleteLocalAll()
    {
        $files = $this->syncService->getLocalFiles();
        $count = 0;

        foreach ($files as $file) {
            if ($this->syncService->deleteLocal($file)) {
                $count++;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => "{$count} file lokal berhasil dibersihkan."
        ]);
    }
}
