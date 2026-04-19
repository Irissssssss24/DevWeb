<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    protected $table = 'stage';
    protected $primaryKey = 'id_stage';
    protected $fillable = ['id_etudiant', 'id_offre', 'id_tuteur', 'statut', 'date_debut', 'date_fin'];
    protected $casts = ['date_debut' => 'datetime', 'date_fin' => 'datetime'];
}