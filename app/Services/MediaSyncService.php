<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MediaSyncService
{
    protected $primaryDisk;
    protected $secondaryDisk;
    protected $localDisk = 'public';

    public function __construct()
    {
        $defaultDisk = config('filesystems.default');
        
        // Only use R2 as primary if configured
        if (env('R2_ACCESS_KEY_ID') && env('R2_SECRET_ACCESS_KEY')) {
            $this->primaryDisk = $defaultDisk === 'local' ? 'r2' : $defaultDisk;
        } else {
            $this->primaryDisk = $defaultDisk === 'local' ? null : $defaultDisk;
        }

        // Only use MinIO as secondary if configured
        if (env('MINIO_ACCESS_KEY_ID') && env('MINIO_SECRET_ACCESS_KEY')) {
            $this->secondaryDisk = env('SECONDARY_DISK', 'minio');
        } else {
            $this->secondaryDisk = null;
        }
    }

    /**
     * Get the correct URL for a media file (prefers local, fallbacks to cloud).
     */
    public static function url($path)
    {
        if (!$path) return null;

        // Check local first (fast)
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        // If local missing, try Cloud (R2)
        $instance = new self();
        if ($instance->primaryDisk) {
            return Storage::disk($instance->primaryDisk)->url($path);
        }

        return Storage::disk('public')->url($path);
    }

    /**
     * Upload a file directly to cloud disks.
     */
    public function directUpload($file, $folder = 'invoices')
    {
        $filename = $file->hashName();
        $path = "{$folder}/{$filename}";
        $content = file_get_contents($file->getRealPath());
        $mime = $file->getClientMimeType();

        $uploaded = false;

        // Try Primary (R2)
        if ($this->primaryDisk) {
            try {
                Storage::disk($this->primaryDisk)->put($path, $content, [
                    'visibility' => 'public',
                    'ContentType' => $mime
                ]);
                $uploaded = true;
            } catch (\Exception $e) {
                Log::error("Direct Upload to R2 Failed: " . $e->getMessage());
            }
        }

        // Try Secondary (MinIO)
        if ($this->secondaryDisk) {
            try {
                Storage::disk($this->secondaryDisk)->put($path, $content, [
                    'visibility' => 'public',
                    'ContentType' => $mime
                ]);
                if (!$this->primaryDisk) $uploaded = true;
            } catch (\Exception $e) {
                Log::error("Direct Upload to MinIO Failed: " . $e->getMessage());
            }
        }

        // Fallback to local if cloud fails
        if (!$uploaded) {
            return $file->store($folder, 'public');
        }

        return $path;
    }

    /**
     * Get all media files used in the database.
     */
    public function getLocalFiles()
    {
        $receiptFiles = \App\Models\GoodsReceipt::whereNotNull('invoice_photo_path')
            ->pluck('invoice_photo_path')
            ->toArray();
        
        $productFiles = \App\Models\Product::whereNotNull('image_path')
            ->pluck('image_path')
            ->toArray();

        $files = array_unique(array_merge($receiptFiles, $productFiles));
        return array_values(array_filter($files));
    }

    /**
     * Sync a single file from local to cloud disks and delete local.
     */
    public function syncFile($path)
    {
        $results = [
            'path' => $path,
            'primary' => false,
            'secondary' => false,
            'deleted_local' => false,
            'error' => null,
            'url_primary' => null,
            'url_secondary' => null,
        ];

        try {
            if (!Storage::disk($this->localDisk)->exists($path)) {
                // If already on cloud, just return status
                $status = $this->getFileStatus($path);
                if ($status['primary'] || $status['secondary']) {
                    return array_merge($results, $status);
                }
                throw new \Exception("File not found on local storage: {$path}");
            }

            $content = Storage::disk($this->localDisk)->get($path);
            $mime = Storage::disk($this->localDisk)->mimeType($path);

            // Sync to Primary (R2)
            if ($this->primaryDisk) {
                Storage::disk($this->primaryDisk)->put($path, $content, [
                    'visibility' => 'public',
                    'ContentType' => $mime
                ]);
                
                // Double verify
                if (Storage::disk($this->primaryDisk)->exists($path)) {
                    $results['primary'] = true;
                    $results['url_primary'] = Storage::disk($this->primaryDisk)->url($path);
                } else {
                    throw new \Exception("Upload to R2 successful but file not found on verification.");
                }
            }

            // Sync to Secondary (MinIO)
            if ($this->secondaryDisk) {
                Storage::disk($this->secondaryDisk)->put($path, $content, [
                    'visibility' => 'public',
                    'ContentType' => $mime
                ]);
                
                // Double verify
                if (Storage::disk($this->secondaryDisk)->exists($path)) {
                    $results['secondary'] = true;
                    $results['url_secondary'] = Storage::disk($this->secondaryDisk)->url($path);
                }
            }

        } catch (\Exception $e) {
            Log::error("MediaSync Error for {$path}: " . $e->getMessage());
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Safely delete local file only if it exists on primary cloud.
     */
    public function deleteLocal($path)
    {
        if (!$this->primaryDisk) return false;

        if (Storage::disk($this->primaryDisk)->exists($path)) {
            if (Storage::disk($this->localDisk)->exists($path)) {
                return Storage::disk($this->localDisk)->delete($path);
            }
        }
        
        return false;
    }

    /**
     * Get status of a file on all disks.
     */
    public function getFileStatus($path)
    {
        $status = [
            'path' => $path,
            'local' => false,
            'primary' => false,
            'secondary' => false,
            'url_local' => null,
            'url_primary' => null,
            'url_secondary' => null,
        ];

        try {
            $status['local'] = Storage::disk($this->localDisk)->exists($path);
            if ($status['local']) {
                $status['url_local'] = Storage::disk($this->localDisk)->url($path);
            }
        } catch (\Exception $e) {}

        try {
            if ($this->primaryDisk) {
                $status['primary'] = Storage::disk($this->primaryDisk)->exists($path);
                if ($status['primary']) {
                    $status['url_primary'] = Storage::disk($this->primaryDisk)->url($path);
                }
            }
        } catch (\Exception $e) {}

        try {
            if ($this->secondaryDisk) {
                $status['secondary'] = Storage::disk($this->secondaryDisk)->exists($path);
                if ($status['secondary']) {
                    $status['url_secondary'] = Storage::disk($this->secondaryDisk)->url($path);
                }
            }
        } catch (\Exception $e) {}

        return $status;
    }
}
