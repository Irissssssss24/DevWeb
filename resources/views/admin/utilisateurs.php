<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Gestion des utilisateurs — MYstage</title>
    <link rel="stylesheet" href="/css/utilisateurs.css">
    <link rel="stylesheet" href="/css/Adminstyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<?php
$pageCourante = 'administrateur';
?>

<div class="page-content">

    <div class="page-header">
        <h2>Gestion des utilisateurs</h2>
        <a href="/administrateur/utilisateurs/creer" class="btn-ajouter">+ Ajouter un utilisateur</a>
    </div>

    <?php if (session('success')): ?>
        <div class="alerte alerte-succes"><?= htmlspecialchars(session('success')) ?></div>
    <?php endif; ?>
    <?php if (session('error')): ?>
        <div class="alerte alerte-erreur"><?= htmlspecialchars(session('error')) ?></div>
    <?php endif; ?>

    <!-- Filtres de recherche -->
    <form method="GET" action="/administrateur/utilisateurs">
        <div class="filtres">
            <div class="filtre-group">
                <label for="recherche">Rechercher</label>
                <input type="text" id="recherche" name="recherche"
                       placeholder="Nom, prénom ou email..."
                       value="<?= htmlspecialchars($recherche ?? '') ?>">
            </div>
            <div class="filtre-group">
                <label for="role">Filtrer par rôle</label>
                <select id="role" name="role">
                    <option value="">— Tous les rôles —</option>
                    <?php foreach (['etudiant','entreprise','tuteur','jury','administrateur'] as $r): ?>
                        <option value="<?= $r ?>" <?= ($filtreRole ?? '') === $r ? 'selected' : '' ?>>
                            <?= ucfirst($r) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn-filtrer">Rechercher</button>
            <?php if (($recherche ?? '') || ($filtreRole ?? '')): ?>
                <a href="/administrateur/utilisateurs" class="btn-reset">Effacer</a>
            <?php endif; ?>
        </div>
    </form>

    <div class="tableau-container">
        <?php if ($utilisateurs->isEmpty()): ?>
            <div class="vide">
                <p>Aucun utilisateur trouvé.</p>
            </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Rôles</th>
                    <th>Inscrit le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($utilisateurs as $u): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($u->nom) ?></strong></td>
                    <td><?= htmlspecialchars($u->prenom) ?></td>
                    <td><?= htmlspecialchars($u->email) ?></td>
                    <td>
                        <?php
                        $r = $u->role;
                        if ($r):
                            $rolesLabels = ['etudiant','entreprise','tuteur','jury','administrateur'];
                            foreach ($rolesLabels as $rl):
                                if ($r->$rl == 1):
                        ?>
                            <span class="badge-role badge-<?= $rl ?>"><?= ucfirst($rl) ?></span>
                        <?php
                                endif;
                            endforeach;
                        endif;
                        ?>
                    </td>
                    <td><?= $u->created_at ? $u->created_at->format('d/m/Y') : '—' ?></td>
                    <td>
                        <div class="actions-cell">
                            <a href="/administrateur/modifier/<?= $u->id_utilisateur ?>" class="btn-modifier">Modifier</a>
                            <?php if ($u->id_utilisateur !== session('user_id')): ?>
                            <form method="POST" action="/administrateur/supprimer/<?= $u->id_utilisateur ?>"
                                  onsubmit="return confirm('Supprimer <?= htmlspecialchars($u->prenom . ' ' . $u->nom) ?> ? Cette action est irréversible.');">
                                <?php echo csrf_field() ?? '' ?>
                                <button type="submit" class="btn-supprimer">Supprimer</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <a href="/administrateur" class="lien-secondaire">Retour </a>

</div>
</body>
</html>
