<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OffreStage extends Model
{
    protected $table = 'offre_stage';
    protected $primaryKey = 'id_offre';
    protected $fillable = ['titre', 'description', 'competences', 'duree', 'missions', 'id_entreprise'];
}