<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginBan extends Model
{
    protected $table = 'login_bans';

    protected $guarded = false;

    protected $casts = [
        'banned_at' => 'datetime',
    ];

    public function isBanned(): bool
    {
        return $this->banned_at !== null;
    }
}
