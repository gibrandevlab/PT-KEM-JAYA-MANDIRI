<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccessOverride extends Model
{
    protected $fillable = ['user_id', 'feature', 'action', 'allowed'];

    protected function casts(): array
    {
        return ['allowed' => 'boolean'];
    }
}
