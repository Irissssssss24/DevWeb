<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Utilisateur extends Model
{
    protected $table = 'utilisateur';
    protected $primaryKey = 'id_utilisateur';
    protected $fillable = ['nom', 'prenom', 'email', 'mot_de_passe'];
    protected $hidden = ['mot_de_passe'];

    public function role() {
        return $this->hasOne(Role::class, 'id_utilisateur', 'id_utilisateur');
    }
    public function etudiant() {
        return $this->hasOne(Etudiant::class, 'id_utilisateur', 'id_utilisateur');
    }
    public function entreprise() {
        return $this->hasOne(Entreprise::class, 'id_utilisateur', 'id_utilisateur');
    }
    public function tuteur() {
        return $this->hasOne(Tuteur::class, 'id_utilisateur', 'id_utilisateur');
    }
    public function jury() {
        return $this->hasOne(Jury::class, 'id_utilisateur', 'id_utilisateur');
    }
    public function remarques() {
        return $this->hasMany(Remarque::class, 'id_utilisateur', 'id_utilisateur');
    }
    public function authentification() {
        return $this->hasOne(Authentification::class, 'id_utilisateur', 'id_utilisateur');
    }
}