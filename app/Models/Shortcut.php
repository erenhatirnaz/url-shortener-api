<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shortcut extends Model
{
    use HasFactory;

    protected $fillable = [
        'shortcut',
        'url',
    ];

    protected $hidden = [
        'user_id', 'user'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
