<?php

/**
 * Connexion à la base de données via variables d'environnement.
 * Ce fichier doit être placé idéalement hors du répertoire public.
 */

// 1. Chargement du fichier .env 
$envFile = __DIR__ . '/.env';

if (file_exists($envFile) && is_readable($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        
        // Ignorer les commentaires et les lignes vides
        if ($line === '' || strpos($line, '#') === 0) continue;

        // Séparer Clé et Valeur au premier '=' rencontré
        if (strpos($line, '=') !== false) {
            list($k, $v) = explode('=', $line, 2);
            $k = trim($k);
            $v = trim($v);

            // Nettoyage des guillemets (simples ou doubles)
            $v = trim($v, "\"' \t\n\r\0\x0B");

            // Injection sécurisée
            if (!empty($k)) {
                putenv("$k=$v");
                $_ENV[$k] = $v;
                $_SERVER[$k] = $v;
            }
        }
    }
}

$host   = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? null);
$port   = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? '5432'); // Port par défaut PostgreSQL
$dbname = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? null);
$user   = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? null);
$pass   = getenv('DB_PASS') ?: ($_ENV['DB_PASS'] ?? '');

// 3. Vérification stricte des variables obligatoires
if (!$host || !$dbname || !$user) {
    error_log("ERREUR CRITIQUE : Configuration base de données incomplète.");
    http_response_code(500);
    die("Erreur de configuration serveur.");
}

try {
    // 4. DSN et Connexion PDO (PostgreSQL)
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;options='--client_encoding=UTF8'";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lance des exceptions en cas d'erreur
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retourne des tableaux associatifs
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Utilise les vraies requêtes préparées (Sécurité ++)
        PDO::ATTR_TIMEOUT            => 5,                      // Timeout de connexion en secondes
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);

} catch (PDOException $e) {
    // On log l'erreur réelle pour le dev, mais on affiche un message neutre pour l'utilisateur
    error_log("PDO Connection Error: " . $e->getMessage());
    http_response_code(500);
    exit("Une erreur est survenue lors de la connexion à la base de données.");
}