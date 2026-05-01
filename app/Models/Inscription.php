<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscription extends Model
{
    protected $table = 'inscriptions';

    protected $fillable = [
        'data',
        'statut',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}