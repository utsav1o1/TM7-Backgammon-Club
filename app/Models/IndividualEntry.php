<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndividualEntry extends Model
{
    use HasFactory;

    protected $fillable = ['player_name', 'email', 'timezone'];
}
