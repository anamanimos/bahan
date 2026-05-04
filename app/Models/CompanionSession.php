<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CompanionSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'context_type',
        'context_id',
        'photo_path',
        'photo_uploaded_at',
        'last_seen_at',
        'expires_at',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'photo_uploaded_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns this session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this session is still valid (not expired).
     */
    public function isValid(): bool
    {
        return $this->expires_at->isFuture();
    }

    /**
     * Check if a photo has been uploaded.
     */
    public function hasPhoto(): bool
    {
        return !is_null($this->photo_path);
    }

    /**
     * Generate a unique token.
     */
    public static function generateToken(): string
    {
        do {
            $token = Str::random(48);
        } while (static::where('token', $token)->exists());

        return $token;
    }

    /**
     * Scope to only valid (non-expired) sessions.
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now());
    }
}
