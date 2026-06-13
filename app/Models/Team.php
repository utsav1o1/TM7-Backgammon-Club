<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'captain_nickname', 'co_captain_nickname', 'members'];

    protected $casts = [
        'members' => 'array',
    ];
}
