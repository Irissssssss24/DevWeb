<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Jury extends Model
{
    protected $table = 'jury';
    protected $primaryKey = 'id_jury';
    protected $fillable = ['id_utilisateur'];
}