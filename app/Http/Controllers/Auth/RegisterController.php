<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Inscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function index()
    {
        ob_start();
        include resource_path('views/auth/nvUtil.php');
        return ob_get_clean();
    }

    public function register(Request $request)
    {
        $data = [
            'nom'          => $request->nom,
            'prenom'       => $request->prenom,
            'email'        => $request->email,
            'mot_de_passe' => Hash::make($request->mot_de_passe),
            'role'         => $request->role,
        ];

        // Champs spécifiques
        switch ($request->role) {
            case 'etudiant':
                $data['filiere'] = $request->filiere;
                $data['niveau']  = $request->niveau;
                break;

            case 'entreprise':
                $data['nom_entreprise'] = $request->nom_entreprise;
                $data['adresse']        = $request->adresse;
                $data['secteur']        = $request->secteur;
                $data['siret']          = $request->siret;
                break;

            case 'tuteur':
                $data['specialite'] = $request->specialite;
                break;
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'nom'          => 'required|string|max:255',
            'prenom'       => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:utilisateur,email',
            'mot_de_passe' => 'required|string|min:8|confirmed',
            'role'         => 'required|in:etudiant,entreprise,tuteur,jury,administrateur',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Stocker en session
        session()->put('register_pending', $data);

        // Code vérification
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        session()->put('register_code', $code);
        session()->put('register_code_expiration', now()->addMinutes(15));

        Mail::raw("Votre code : $code (valable 15 min)", function ($message) use ($request) {
            $message->to($request->email)->subject('Vérification email');
        });

        return redirect('/inscription/verification');
    }

    public function showVerifyForm()
    {
        if (!session()->has('register_pending')) {
            return redirect('/inscription')->with('error', 'Session expirée.');
        }

        ob_start();
        include resource_path('views/auth/verify-register.php');
        return ob_get_clean();
    }

    public function verifyAndCreate(Request $request)
    {
        $code    = trim($request->input('code'));
        $stored  = session('register_code');
        $expiry  = session('register_code_expiration');
        $pending = session('register_pending');

        if (!$pending || !$stored) {
            return redirect('/inscription')->with('error', 'Session expirée.');
        }

        if (now()->gt($expiry)) {
            session()->forget(['register_pending', 'register_code', 'register_code_expiration']);
            return redirect('/inscription')->with('error', 'Code expiré.');
        }

        if ($code !== $stored) {
            return redirect('/inscription/verification')->with('error', 'Code incorrect.');
        }

        Inscription::create([
            'data'   => $pending, 
            'statut' => 'en_attente',
        ]);

        session()->forget(['register_pending', 'register_code', 'register_code_expiration']);

        return redirect('/connexion')
            ->with('success', 'Demande envoyée. En attente de validation admin.');
    }


}