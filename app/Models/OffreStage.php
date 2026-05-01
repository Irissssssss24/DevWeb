<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OffreStage extends Model
{
    protected $table = 'offre_stage';
    protected $primaryKey = 'id_offre';
    protected $fillable = ['titre', 'description', 'competences', 'duree', 'missions', 'id_entreprise'];

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'id_entreprise', 'id_entreprise');
    }

    public function stages()
    {
        return $this->hasMany(Stage::class, 'id_offre', 'id_offre');
    }
}
