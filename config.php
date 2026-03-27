<?php
    // page permettant la connexion à la base de données, à inclure dans les autres pages
    $host   = 'localhost';
    $port   = '5432';
    $dbname = 'projetstage'; // Mis en minuscules pour correspondre au SQL
    //A CHANGER EN FONCTION DE VOTRE CONFIGURATION POSTGRESQL
    $user   = 'iris';        //  utilisateur PostgreSQL
    $pass   = 'iris';        //  mot de passe
    
    try {
        // DSN (Data Source Name) pour PostgreSQL
        // Ajout de options='--client_encoding=UTF8' pour gérer les accents correctement
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;options='--client_encoding=UTF8'";
        
        // Création d'une instance PDO pour la connexion à la base de données
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
       
    // en cas d'erreur de connexion, on affiche un message clair
    } catch (PDOException $e) {
        // On utilise htmlspecialchars pour sécuriser l'affichage de l'erreur si besoin
        die("Erreur de connexion PostgreSQL : " . $e->getMessage());
    }
?>