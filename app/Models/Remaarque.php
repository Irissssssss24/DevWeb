<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Remarque extends Model
{
    protected $table = 'remarque';
    protected $primaryKey = 'id_remarque';
    protected $fillable = ['contenu', 'date', 'id_stage', 'id_utilisateur'];
    protected $casts = ['date' => 'datetime'];
}