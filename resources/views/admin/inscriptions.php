<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Validation des Inscriptions — MYstage</title>
    <link rel="stylesheet" href="/css/Adminstyle.css">
    <link rel="stylesheet" href="/css/candidaturesStyle.css">
    <link href="https://fonts.googleapis.com/css?family=Inter" rel="stylesheet">
</head>
<body>
<?php
$pageCourante = 'administrateur';
include resource_path('views/layouts/barre_nav.php');
?>

<div class="contenu-candidatures">
    <h2>Inscriptions en attente de validation</h2>

    <?php if (session('success')): ?>
        <div class="message-succes" style="color: green; margin-bottom: 20px;">
            <?= htmlspecialchars(session('success')) ?>
        </div>
    <?php endif; ?>
    <?php if (session('error')): ?>
        <div class="message-erreur" style="color: #e74c3c; margin-bottom: 20px;">
            <?= htmlspecialchars(session('error')) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($inscriptions) || count($inscriptions) === 0): ?>
        <p class="aucune-candidature">Aucune demande d'inscription en attente.</p>
    <?php else: ?>
        <?php foreach ($inscriptions as $inscription): ?>
            <?php
            // On récupère les données du JSON
            $data = $inscription->data;
            $nom = $data['nom'] ?? 'Inconnu';
            $prenom = $data['prenom'] ?? 'Utilisateur';
            $email = $data['email'] ?? 'Non renseigné';
            $role = $data['role'] ?? 'Non défini';
            ?>
            <div class="carte-candidature statut-en-attente">
                <div class="candidature-header">
                    <div class="candidat-info">
                        <div class="candidat-avatar">
                            <?= strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1)) ?>
                        </div>
                        <div>
                            <p class="candidat-nom"><?= htmlspecialchars($prenom . ' ' . $nom) ?></p>
                            <p class="candidat-email"><?= htmlspecialchars($email) ?></p>
                            <p class="candidat-filiere">
                                Rôle demandé : <strong><?= htmlspecialchars(ucfirst($role)) ?></strong>
                            </p>
                            <?php if($role === 'etudiant'): ?>
                                <p class="candidat-filiere">Filière : <?= htmlspecialchars($data['filiere'] ?? 'N/A') ?></p>
                            <?php elseif($role === 'entreprise'): ?>
                                <p class="candidat-filiere">Entreprise : <?= htmlspecialchars($data['nom_entreprise'] ?? 'N/A') ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="actions-candidature" style="margin-top: 15px; display: flex; gap: 10px;">
                    <!-- FORMULAIRE ACCEPTER -->
                    <form method="POST" action="/admin/inscription/accepter/<?= $inscription->id ?>">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="btn-accepter" style="background-color: #27ae60; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer;">
                            Valider l'inscription
                        </button>
                    </form>

                    <!-- FORMULAIRE REFUSER -->
                    <form method="POST" action="/admin/inscription/refuser/<?= $inscription->id ?>">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="btn-refuser" onclick="return confirm('Refuser cette inscription ?')" style="background-color: #e74c3c; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer;">
                            Refuser
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
