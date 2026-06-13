<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BracketSnapshot extends Model
{
    protected $fillable = [
        'snapshot_label',
        'tournament_type',
        'snapshot_data',
        'deleted_by',
    ];

    protected $casts = [
        'snapshot_data' => 'array',
    ];
}
