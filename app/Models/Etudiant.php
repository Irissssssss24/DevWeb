<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etudiant extends Model
{
    protected $table = 'etudiant';
    protected $primaryKey = 'id_etudiant';
    protected $fillable = ['id_utilisateur', 'filiere', 'niveau', 'cv'];
}