<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
    protected $table = 'entreprise';
    protected $primaryKey = 'id_entreprise';
    protected $fillable = ['id_utilisateur', 'nom_entreprise', 'adresse', 'secteur'];

    public function utilisateur() {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur', 'id_utilisateur');
    }

    public function offres() {
        return $this->hasMany(OffreStage::class, 'id_entreprise', 'id_entreprise');
    }
}