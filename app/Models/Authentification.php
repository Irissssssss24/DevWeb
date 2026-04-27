<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Authentification extends Model
{
    protected $table = 'authentification';
    protected $primaryKey = 'id_auth';
    protected $fillable = ['id_utilisateur', 'code_2fa', 'date_expiration'];
    protected $casts = ['date_expiration' => 'datetime'];

    /**
     * Relation vers l'utilisateur
     */
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur', 'id_utilisateur');
    }
}