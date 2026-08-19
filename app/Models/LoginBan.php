<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class LoginBan extends Model
{
    protected $table = 'login_bans';

    protected $fillable = [
        'ip_address',
        'attempts',
        'banned_at',
        'locked_until',
    ];

    protected $casts = [
        'banned_at'    => 'datetime',
        'locked_until' => 'datetime',
        'attempts'     => 'integer',
    ];

    public function isPermanentlyBanned(): bool
    {
        return $this->banned_at !== null;
    }

    public function isBanned(): bool
    {
        return $this->isPermanentlyBanned() || $this->isTemporarilyLocked();
    }

    public function isTemporarilyLocked(): bool
    {
        return $this->locked_until instanceof Carbon && $this->locked_until->isFuture();
    }

    public function retryAfterSeconds(): int
    {
        if (! $this->locked_until instanceof Carbon) {
            return 0;
        }

        return max(0, $this->locked_until->getTimestamp() - time());
    }

    public function resetIfLockExpired(): void
    {
        if ($this->locked_until instanceof Carbon && $this->locked_until->isPast()) {
            $this->locked_until = null;
            $this->attempts = 0;
            $this->save();
        }
    }
}
