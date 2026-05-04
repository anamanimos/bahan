<?php

namespace App\Http\Controllers;

use App\Models\CompanionSession;
use Illuminate\Http\Request;

class CompanionController extends Controller
{
    /**
     * Check if user has an existing active companion session with a connected phone.
     * Called from PC on page load to auto-detect connected phone.
     */
    public function checkSession()
    {
        $session = CompanionSession::where('user_id', auth()->id())
            ->valid()
            ->latest('created_at')
            ->first();

        if (!$session) {
            return response()->json([
                'success' => true,
                'has_session' => false,
            ]);
        }

        // Consider HP "connected" if last_seen_at is within 2 minutes
        $isPhoneConnected = $session->last_seen_at 
            && $session->last_seen_at->diffInSeconds(now()) < 120;

        return response()->json([
            'success' => true,
            'has_session' => true,
            'token' => $session->token,
            'url' => url('/cam/' . $session->token),
            'phone_connected' => $isPhoneConnected,
            'has_photo' => $session->hasPhoto(),
            'photo_url' => $session->photo_path
                ? asset('storage/' . $session->photo_path)
                : null,
            'last_seen_at' => $session->last_seen_at?->toISOString(),
        ]);
    }

    /**
     * Create or reuse a companion session and return the token.
     * Called from PC when user clicks "Ambil dari HP".
     * Reuses existing valid session if available.
     */
    public function createSession(Request $request)
    {
        // Check for existing valid session first
        $session = CompanionSession::where('user_id', auth()->id())
            ->valid()
            ->latest('created_at')
            ->first();

        if ($session) {
            // Reuse existing session — just clear the photo for new usage
            $session->update([
                'photo_path' => null,
                'photo_uploaded_at' => null,
                'context_type' => $request->input('context_type', 'goods_receipt'),
                'context_id' => $request->input('context_id'),
            ]);
        } else {
            // Create new session
            $session = CompanionSession::create([
                'user_id' => auth()->id(),
                'token' => CompanionSession::generateToken(),
                'context_type' => $request->input('context_type', 'goods_receipt'),
                'context_id' => $request->input('context_id'),
                'expires_at' => now()->addHours(8), // Valid for a work day
            ]);
        }

        return response()->json([
            'success' => true,
            'token' => $session->token,
            'url' => url('/cam/' . $session->token),
            'expires_at' => $session->expires_at->toISOString(),
        ]);
    }

    /**
     * Show the camera page for the phone.
     * This page is standalone (no app layout) and accessed via QR code.
     */
    public function show($token)
    {
        $session = CompanionSession::where('token', $token)
            ->valid()
            ->first();

        if (!$session) {
            return response()->view('companion.expired', [], 410);
        }

        // Update last seen
        $session->update(['last_seen_at' => now()]);

        return view('companion.camera', [
            'token' => $token,
            'session' => $session,
        ]);
    }

    /**
     * Check if a photo has been uploaded (polled from PC).
     */
    public function checkPhoto($token)
    {
        $session = CompanionSession::where('token', $token)
            ->valid()
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired'
            ], 410);
        }

        // Check phone connection status
        $isPhoneConnected = $session->last_seen_at 
            && $session->last_seen_at->diffInSeconds(now()) < 120;

        return response()->json([
            'success' => true,
            'has_photo' => $session->hasPhoto(),
            'photo_url' => $session->photo_path
                ? asset('storage/' . $session->photo_path)
                : null,
            'photo_uploaded_at' => $session->photo_uploaded_at?->toISOString(),
            'phone_connected' => $isPhoneConnected,
            'last_seen_at' => $session->last_seen_at?->toISOString(),
        ]);
    }

    /**
     * Heartbeat endpoint for phone to report it's still connected.
     */
    public function heartbeat($token)
    {
        $session = CompanionSession::where('token', $token)
            ->valid()
            ->first();

        if (!$session) {
            return response()->json(['success' => false], 410);
        }

        $session->update(['last_seen_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Clear the photo from a companion session (for new nota usage).
     * Called from PC when preparing for a new photo.
     */
    public function clearPhoto($token)
    {
        $session = CompanionSession::where('token', $token)
            ->where('user_id', auth()->id())
            ->valid()
            ->first();

        if (!$session) {
            return response()->json(['success' => false], 410);
        }

        // Delete old photo file
        if ($session->photo_path) {
            $oldPath = storage_path('app/public/' . $session->photo_path);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $session->update([
            'photo_path' => null,
            'photo_uploaded_at' => null,
        ]);

        return response()->json(['success' => true]);
    }
}
