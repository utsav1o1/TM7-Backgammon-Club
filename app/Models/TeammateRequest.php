<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeammateRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'players_needed',
        'message',
        'status',
    ];
}
