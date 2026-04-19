<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'role';
    protected $primaryKey = 'id_utilisateur';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'id_utilisateur', 'administrateur', 'etudiant',
        'entreprise', 'tuteur', 'jury'
    ];

    public function utilisateur() {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur', 'id_utilisateur');
    }
}