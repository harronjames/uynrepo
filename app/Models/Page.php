<?php

namespace App\Models;

use App\Models\Concerns\HasSeoMeta;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasSeoMeta;

    protected $table = 'pages';

    protected $guarded = false;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
