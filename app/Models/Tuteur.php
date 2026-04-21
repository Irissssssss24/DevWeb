<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Tuteur extends Model
{
    protected $table = 'tuteur';
    protected $primaryKey = 'id_tuteur';
    protected $fillable = ['id_utilisateur', 'specialite'];
}