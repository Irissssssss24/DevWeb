<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etudiant extends Model
{
    protected $table = 'etudiant';
    protected $primaryKey = 'id_etudiant';
    protected $fillable = ['id_utilisateur', 'filiere', 'niveau', 'cv'];
    public function utilisateur() {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur', 'id_utilisateur');
    }
}