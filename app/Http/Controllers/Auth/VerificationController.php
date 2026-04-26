<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use App\Models\Role;
use App\Models\Etudiant;
use App\Models\Entreprise;
use App\Models\Tuteur;
use App\Models\Jury;
use App\Models\Administrateur;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    // Affiche le formulaire de vérification
    public function index()
    {
        ob_start();
        include resource_path('views/auth/verification.php');
        return ob_get_clean();
    }

    // Vérifie le code entré par l'utilisateur
    public function verify(Request $request)
    {
        // Vérification des champs
        if (empty($request->input('email')) || empty($request->input('code'))) {
            return redirect('/verification')->with('error', 'Email et code requis.');
        }

        $email = trim($request->input('email'));
        $code = trim($request->input('code'));

        // Chercher l'utilisateur par email
        $user = Utilisateur::where('email', $email)->first();

        if (!$user) {
            return redirect('/verification')->with('error', 'Email non trouvé.');
        }

        // Vérifier si l'email est déjà vérifié
        if ($user->email_verified_at) {
            return redirect('/connexion')->with('error', 'Cet email est déjà vérifié.');
        }

        // Vérifier si le code est correct et non expiré (15 minutes)
        if ($user->verification_code !== $code) {
            return redirect('/verification')->with('error', 'Code incorrect.');
        }

        if (now()->diffInMinutes($user->verification_code_expires_at) > 15) {
            return redirect('/verification')->with('error', 'Le code a expiré. Réessayez.');
        }

        // Marquer l'email comme vérifié
        $user->update([
            'email_verified_at' => now(),
            'verification_code' => null,
            'verification_code_expires_at' => null,
        ]);

        // Créer le rôle et l'entité spécifique
        $roleData = [
            'id_utilisateur' => $user->id_utilisateur,
            'administrateur' => 0,
            'etudiant' => 0,
            'entreprise' => 0,
            'tuteur' => 0,
            'jury' => 0,
        ];

        // Récupérer le rôle depuis la session ou les données temporaires
        // Ici on suppose que le rôle est stocké dans la table utilisateur
        // À adapter selon votre structure
        $role = session('temp_role');

        if (!$role) {
            return redirect('/inscription')->with('error', 'Erreur lors de la vérification.');
        }

        $roleData[$role] = 1;
        Role::create($roleData);

        // Créer l'entité spécifique
        switch ($role) {
            case 'etudiant':
                Etudiant::create(['id_utilisateur' => $user->id_utilisateur]);
                break;
            case 'entreprise':
                Entreprise::create(['id_utilisateur' => $user->id_utilisateur]);
                break;
            case 'tuteur':
                Tuteur::create(['id_utilisateur' => $user->id_utilisateur]);
                break;
            case 'jury':
                Jury::create(['id_utilisateur' => $user->id_utilisateur]);
                break;
            case 'administrateur':
                Administrateur::create(['id_utilisateur' => $user->id_utilisateur]);
                break;
        }

        return redirect('/connexion')->with('success', 'Email vérifié ! Vous pouvez maintenant vous connecter.');
    }
}
