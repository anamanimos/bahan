<?php

namespace App\Http\Controllers;

use App\Models\CompanionSession;
use Illuminate\Http\Request;

class CompanionUploadController extends Controller
{
    /**
     * Handle photo upload from the phone camera companion.
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'photo' => 'required|string', // Base64 encoded image
        ]);

        $session = CompanionSession::where('token', $request->token)
            ->valid()
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak valid atau sudah expired.',
            ], 410);
        }

        // Decode base64 image
        $imageData = $request->photo;

        // Remove data URL prefix if present
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
            $extension = $matches[1];
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
        } else {
            $extension = 'jpg';
        }

        $imageData = base64_decode($imageData);

        if ($imageData === false) {
            return response()->json([
                'success' => false,
                'message' => 'Format gambar tidak valid.',
            ], 422);
        }

        // Generate filename and save
        $filename = 'companion-photos/' . $session->token . '_' . time() . '.' . $extension;
        $storagePath = storage_path('app/public/' . $filename);

        // Ensure directory exists
        $dir = dirname($storagePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($storagePath, $imageData);

        // Delete old photo if exists
        if ($session->photo_path) {
            $oldPath = storage_path('app/public/' . $session->photo_path);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // Update session with photo info
        $session->update([
            'photo_path' => $filename,
            'photo_uploaded_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil diupload!',
            'photo_url' => asset('storage/' . $filename),
        ]);
    }
}
