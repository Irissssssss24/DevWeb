<?php
//hash les mots de passe en clair dans la table `utilisateur` en utilisant password_hash

//on le lance qu'une fois
require_once __DIR__ . '/config.php';

try {
    $stmt = $pdo->query('SELECT id_utilisateur, email, mot_de_passe FROM utilisateur');
    $rows = $stmt->fetchAll();

    $updated = 0;
    $skipped = 0;

    foreach ($rows as $row) {
        $id = $row['id_utilisateur'];
        $pw = $row['mot_de_passe'];

        // Détecter si le mot de passe est déjà un hash (prefixes courants)
        if (is_string($pw) && (preg_match('/^\$2y\$/', $pw) || preg_match('/^\$2a\$/', $pw) || preg_match('/^\$argon2/', $pw))) {
            $skipped++;
            continue;
        }

        // Si champ vide, on skip
        if ($pw === null || $pw === '') {
            $skipped++;
            continue;
        }

        // Hashage
        $hash = password_hash($pw, PASSWORD_DEFAULT);

        $u = $pdo->prepare('UPDATE utilisateur SET mot_de_passe = :hash WHERE id_utilisateur = :id');
        $u->execute(['hash' => $hash, 'id' => $id]);
        $updated++;
        echo "Mise à jour utilisateur id={$id} ({$row['email']})\n";
    }

    echo "Terminé. Mises à jour: {$updated}, ignorés: {$skipped}\n";

    // Optionnel: créer un utilisateur test si aucun utilisateur n'existe
    if (count($rows) === 0) {
        $email = 'test@example.com';
        $hash = password_hash('Test12345', PASSWORD_DEFAULT);
        $q = $pdo->prepare('INSERT INTO utilisateur (nom, prenom, email, identifiant, mot_de_passe, role, first_login) VALUES (:nom, :prenom, :email, :ident, :hash, :role, :first)');
        $q->execute([
            'nom' => 'Test',
            'prenom' => 'User',
            'email' => $email,
            'ident' => 'testuser',
            'hash' => $hash,
            'role' => 'etudiant',
            'first' => false
        ]);
        echo "Utilisateur de test créé: {$email} / Mot de passe: Test12345\n";
    }

//si erreur on l'affiche
} catch (PDOException $e) {
    echo "Erreur PDO: " . $e->getMessage() . "\n";
    exit(1);
}

?>
