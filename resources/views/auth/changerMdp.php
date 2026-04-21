<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Changer mon mot de passe — ProjetStage</title>
</head>
<body>

<?php

//$_SERVER est une super globale en PHP qui contient des informations sur le serveur web
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $nouveau = $_POST['nouveau']   ?? '';
    $confirm = $_POST['confirmer'] ?? '';

    //si le mot de passe est trop court ou si les deux mots de passe ne correspondent pas, on affiche un message d'erreur
    if (strlen($nouveau) < 8) {
        $error = 'Le mot de passe doit contenir au moins 8 caractères.';
    } elseif ($nouveau !== $confirm) {
        $error = 'Les deux mots de passe ne correspondent pas.';
    } else {
        // Connexion à la base de données
        require_once __DIR__ . '/config.php';
        // Hashage du mot de passe
        $hash = password_hash($nouveau, PASSWORD_DEFAULT);

        //stockage du nouveau mot de passe dans la base de données
        $stmt = $pdo->prepare(
            'UPDATE utilisateur SET mot_de_passe = :mdp
             WHERE id_utilisateur = :id'
        );
        //on execute la requete en remplaçant les paramètres par les valeurs fournies dans la session
        $stmt->execute(['mdp' => $hash, 'id' => $_SESSION['user_id']]);

        // Redirection à la page correspondant au rôle
        $redirections = [
            'etudiant'   => 'etudiant.php',
            'entreprise' => 'entreprise.php',
            'tuteur'     => 'tuteur.php',
            'jury'       => 'jury.php',
            'admin'      => 'admin.php',
        ];
        //si le rôle de l'utilisateur est connu, on redirige vers la page d'accueil correspondant au rôle de l'utilisateur, sinon on redirige vers la page de connexion
        $dest = $redirections[$_SESSION['role']] ?? 'Portail_Connexion.php';
        header('Location: ' . $dest);
        exit();
    }
}
?>

    <h1>Changer mon mot de passe</h1>
    <p>Choisissez un nouveau mot de passe (8 caractères minimum).</p>

    <form method="POST">

        <label for="nouveau">Nouveau mot de passe :</label><br>
        <input
            type="password"
            id="nouveau"
            name="nouveau"
            required
            minlength="8"
            autocomplete="new-password"
        ><br><br>

        <label for="confirmer">Confirmer le mot de passe :</label><br>
        <input
            type="password"
            id="confirmer"
            name="confirmer"
            required
            minlength="8"
            autocomplete="new-password"
        ><br><br>

        <button type="submit">Valider</button>

    </form>

</body>
</html>