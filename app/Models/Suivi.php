<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Suivi extends Model
{
    protected $table = 'suivi';
    protected $primaryKey = 'id_suivi';
    protected $fillable = ['avancement', 'date', 'id_stage'];
    protected $casts = ['date' => 'datetime'];
}