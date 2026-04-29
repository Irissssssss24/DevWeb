<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Page de <?php echo htmlspecialchars(session('nom') . " " . session('prenom')); ?></title> 
        <link rel="stylesheet" href="/css/Adminstyle.css">
        <link href="https://fonts.googleapis.com/css?family=Inter" rel="stylesheet">
    </head>

<body>
<?php
// On inclut la barre de navigation
$pageCourante = 'tuteur';
include resource_path('views/layouts/barre_nav.php');

$RapportDeStage = "#"; 
?>
    
    <p>Voici les fonctionnalités disponibles pour vous :</p>



</body>
</html>