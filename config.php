<?php
    // page permettant la connexion à la base de données, à inclure dans les autres pages
    $host   = 'localhost';
    $port   = '5432';
    $dbname = 'projetStage';
    $user   = 'postgres';
    $pass   = 'votre_mdp'; 
    
    try {
        // DSN (Data Source Name) pour PostgreSQL
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        // Création d'une instance PDO pour la connexion à la base de données
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
       
    //en cas d'erreur de connexion à la base de données, on affiche un message d'erreur et on arrête l'exécution du script
    } catch (PDOException $e) {
        die("Erreur de connexion PostgreSQL : " . $e->getMessage());
    }
?>
 