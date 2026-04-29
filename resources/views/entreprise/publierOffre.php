<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Publier une offre — MYstage</title>
    <link rel="stylesheet" href="/css/Adminstyle.css">
    <link rel="stylesheet" href="/css/offreStyle.css">
    <link href="https://fonts.googleapis.com/css?family=Inter" rel="stylesheet">
</head>
<body>
<?php
$pageCourante = 'entreprise';
include resource_path('views/layouts/barre_nav.php');
?>

<div class="contenu-offre">
    <h2>Publier une offre de stage</h2>


    <form method="POST" action="/creer-offre">
        <input type="hidden" name="_token" value="<?= csrf_token() ?>">

        <div class="groupe">
            <label for="titre">Titre de l'offre *</label>
            <input type="text" id="titre" name="titre" required 
                   placeholder="ex: Développeur web fullstack"
                   value="<?= old('titre') ?>">
        </div>

        <div class="groupe">
            <label for="description">Description *</label>
            <textarea id="description" name="description" required 
                      rows="5" placeholder="Décrivez le stage..."><?= old('description') ?></textarea>
        </div>

        <div class="groupe">
            <label for="missions">Missions *</label>
            <textarea id="missions" name="missions" required 
                      rows="4" placeholder="Listez les missions du stagiaire..."><?= old('missions') ?></textarea>
        </div>

        <div class="groupe">
            <label for="competences">Compétences requises</label>
            <input type="text" id="competences" name="competences"
                   placeholder="ex: PHP, Laravel, PostgreSQL"
                   value="<?= old('competences') ?>">
            <small>Séparez les compétences par des virgules</small>
        </div>

        <div class="groupe">
            <label for="duree">Durée *</label>
            <input type="text" id="duree" name="duree" required
                   placeholder="ex: 3 mois, 6 semaines..."
                   value="<?= old('duree') ?>">
        </div>

        <div class="boutons-form">
            <a href="/entreprise" class="btn-annuler">Annuler</a>
            <button type="submit">Publier l'offre</button>
        </div>
    </form>
</div>
</body>
</html>