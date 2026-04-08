<?php
session_start();

// Vérification de la méthode de requête
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: Portail_Connexion.php');
    exit();
}

//si les champs sont vides, on redirige vers la page de connexion avec un message d'erreur
if (empty($_POST['email']) || empty($_POST['password'])) {
    header('Location: Portail_Connexion.php?error=champs_vides');
    exit();
}

// Connexion à la base de données
require_once __DIR__ . '/config.php';

// Récupération de l'utilisateur à partir de la base de données
$email = trim($_POST['email']);
$mdp   = $_POST['password'];

//stmt est la variable qui récupére les informations de l'utilisateur 
//pdo est la variable de connexion à la base de données
//prepare est une méthode permettant l'execution de requêtes SQL sécurisées
// NOTE : Suppression de 'first_login' car la colonne n'existe pas dans le SQL fourni
$stmt = $pdo->prepare(
    'SELECT id_utilisateur, nom, prenom, email, mot_de_passe, role
     FROM utilisateur WHERE email = :email LIMIT 1'
);

//execute est une méthode qui exécute la requête préparée en remplaçant les paramètres par les valeurs fournies
$stmt->execute(['email' => $email]);

//fetch est une méthode qui récupère la ligne suivante dans la base de données
$user = $stmt->fetch();

// Vérification du mot de passe - utilisation de password_verify pour hashes
if ($user && password_verify($mdp, $user['mot_de_passe'])) {

    // Prévenir la session fixation : régénérer l'ID de session
    session_regenerate_id(true);

    //on stocke les informations de l'utilisateur dans la session afin d'éviter plusieurs connexions à la base de données
    $_SESSION['user_id'] = $user['id_utilisateur'];
    $_SESSION['nom']     = $user['nom'];
    $_SESSION['prenom']  = $user['prenom'];
    $_SESSION['email']   = $user['email'];
    $_SESSION['role']    = $user['role'];

    // Redirection selon le rôle
    $redirections = [
        'etudiant'   => 'etudiant.php',
        'entreprise' => 'entreprise.php',
        'tuteur'     => 'tuteur.php',
        'jury'     => 'jury.php',
        'admin'      => 'admin.php',
    ];

    // on redirige vers la page d'accueil correspondant au rôle de l'utilisateur, ou vers la page de connexion en cas de rôle inconnu
    $dest = $redirections[$user['role']] ?? 'Portail_Connexion.php';
    header('Location: ' . $dest);
    exit();
}

// si la connexion échoue
header('Location: Portail_Connexion.php?error=identifiants_invalides');
exit();
?>