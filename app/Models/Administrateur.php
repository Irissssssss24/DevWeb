<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Administrateur extends Model
{
    protected $table = 'administrateur';
    protected $primaryKey = 'id_administrateur';
    protected $fillable = ['id_utilisateur'];
}