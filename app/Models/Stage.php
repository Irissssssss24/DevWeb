<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    protected $table = 'stage';
    protected $primaryKey = 'id_stage';
    protected $fillable = [
<<<<<<< HEAD
        'id_etudiant',
        'id_offre',
        'id_tuteur',
        'statut',
        'date_debut',
        'date_fin',
        'lettre_motivation',
        'convention_validee',
        'remarque_convention',
    ];
    protected $casts = [
        'date_debut'        => 'datetime',
        'date_fin'          => 'datetime',
        'convention_validee'=> 'boolean',
    ];

    public function etudiant()
    {
=======
        'id_etudiant', 'id_offre', 'id_tuteur',
        'statut', 'date_debut', 'date_fin',
        'date_debut_proposee', 'date_fin_proposee',
        'lettre_motivation', 'convention', 'convention_signee'
    ];

    protected $casts = [
        'date_debut'          => 'datetime',
        'date_fin'            => 'datetime',
        'date_debut_proposee' => 'datetime',
        'date_fin_proposee'   => 'datetime',
    ];
    
    public function etudiant() {
>>>>>>> 6252fdd6682604db39244122d27a3921600cd6ff
        return $this->belongsTo(Etudiant::class, 'id_etudiant', 'id_etudiant');
    }

    public function offre()
    {
        return $this->belongsTo(OffreStage::class, 'id_offre', 'id_offre');
    }

<<<<<<< HEAD
    public function documents()
    {
        return $this->hasMany(Document::class, 'id_stage', 'id_stage');
    }

    public function remarques()
    {
        return $this->hasMany(Remarque::class, 'id_stage', 'id_stage')->orderBy('date', 'desc');
    }
=======
    
    
>>>>>>> 6252fdd6682604db39244122d27a3921600cd6ff
}


