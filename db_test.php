<?php
// db_test.php — petit script pour tester la connexion à la base via config.php
// Placez ce fichier dans le dossier web et ouvrez-le depuis votre navigateur
// ou lancez-le en CLI: php db_test.php

require_once __DIR__ . '/config.php';

try {
    // simple requête pour valider la connexion
    $stmt = $pdo->query('SELECT 1');
    $res = $stmt->fetch();
    echo "Connexion à la base réussie.\n";
    // affiche info minimale
    echo "PDO chargé et prêt.\n";
} catch (PDOException $e) {
    echo "Erreur de connexion : " . htmlspecialchars($e->getMessage());
}

?>
