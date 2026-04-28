<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Utilisateur extends Model
{
    protected $table = 'utilisateur';
    protected $primaryKey = 'id_utilisateur';
    public $incrementing = false;        // pas auto-incrémenté
    protected $keyType = 'string';       // c'est une string pas un int
    protected $fillable = ['nom', 'prenom', 'email', 'mot_de_passe'];
    protected $hidden = ['mot_de_passe'];

    // Génère automatiquement un UUID à la création
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id_utilisateur)) {
                $model->id_utilisateur = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

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