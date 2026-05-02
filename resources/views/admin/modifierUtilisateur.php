<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Modifier l'utilisateur — MYstage</title>
    <link rel="stylesheet" href="/css/Adminstyle.css">
    <link rel="stylesheet" href="/css/Profilstyle.css">
    <link rel="stylesheet" href="/css/utilisateurs.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<?php
$pageCourante = 'administrateur';
include resource_path('views/layouts/barre_nav.php');

$role = $utilisateur->role;
?>

<div class="page-content">

    <a href="/administrateur/utilisateurs" class="btn-retour">← Retour à la liste</a>

    <div class="page-header">
        <h1>Modifier : <?= htmlspecialchars($utilisateur->prenom . ' ' . $utilisateur->nom) ?></h1>
    </div>

    <?php if (session('success')): ?>
        <div class="alerte alerte-succes"><?= htmlspecialchars(session('success')) ?></div>
    <?php endif; ?>
    <?php if (session('error')): ?>
        <div class="alerte alerte-erreur"><?= htmlspecialchars(session('error')) ?></div>
    <?php endif; ?>
    <?php if ($errors ?? null): ?>
    <div class="alerte alerte-erreur">
        <strong>Erreurs :</strong>
        <ul><?php foreach ($errors->all() as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
    </div>
    <?php endif; ?>

    <!-- Infos générales -->
    <div class="form-card">
        <h2>👤 Informations générales</h2>
        <form method="POST" action="/administrateur/utilisateurs/update/<?= $utilisateur->id_utilisateur ?>">
            <?php echo csrf_field() ?? '' ?>

            <div class="form-row">
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars(old('nom', $utilisateur->nom)) ?>" required>
                </div>
                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" name="prenom" value="<?= htmlspecialchars(old('prenom', $utilisateur->prenom)) ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars(old('email', $utilisateur->email)) ?>" required>
            </div>

            <hr class="separateur">
            <h2>Changer le mot de passe <small style="font-size:0.8rem;color:#888;">(optionnel)</small></h2>
            <div class="form-row">
                <div class="form-group">
                    <label>Nouveau mot de passe</label>
                    <input type="password" name="mot_de_passe">
                    <p class="info-mdp">Laisser vide pour ne pas modifier</p>
                </div>
                <div class="form-group">
                    <label>Confirmer le mot de passe</label>
                    <input type="password" name="mot_de_passe_confirmation">
                </div>
            </div>

            <?php if ($role): ?>
            <!-- Champs spécifiques selon le rôle -->
            <?php if ($role->etudiant): ?>
            <div class="section-specifique">
                <h3>Informations étudiant</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Filière</label>
                        <input type="text" name="filiere" value="<?= htmlspecialchars(old('filiere', $utilisateur->etudiant->filiere ?? '')) ?>">
                    </div>
                    <div class="form-group">
                        <label>Niveau</label>
                        <select name="niveau">
                            <option value="">— Choisir —</option>
                            <?php foreach (['P1','P2','ING1','ING2','ING3'] as $n): ?>
                                <option value="<?= $n ?>" <?= ($utilisateur->etudiant->niveau ?? '') === $n ? 'selected' : '' ?>><?= $n ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($role->entreprise): ?>
            <div class="section-specifique">
                <h3>Informations entreprise</h3>
                <div class="form-group">
                    <label>Nom de l'entreprise</label>
                    <input type="text" name="nom_entreprise" value="<?= htmlspecialchars(old('nom_entreprise', $utilisateur->entreprise->nom_entreprise ?? '')) ?>">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Secteur</label>
                        <input type="text" name="secteur" value="<?= htmlspecialchars(old('secteur', $utilisateur->entreprise->secteur ?? '')) ?>">
                    </div>
                    <div class="form-group">
                        <label>SIRET</label>
                        <input type="text" name="siret" value="<?= htmlspecialchars(old('siret', $utilisateur->entreprise->siret ?? '')) ?>" maxlength="14">
                    </div>
                </div>
                <div class="form-group">
                    <label>Adresse</label>
                    <input type="text" name="adresse" value="<?= htmlspecialchars(old('adresse', $utilisateur->entreprise->adresse ?? '')) ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($role->tuteur): ?>
            <div class="section-specifique">
                <h3>Informations tuteur</h3>
                <div class="form-group">
                    <label>Spécialité</label>
                    <input type="text" name="specialite" value="<?= htmlspecialchars(old('specialite', $utilisateur->tuteur->specialite ?? '')) ?>">
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>

            <hr class="separateur">
            <button type="submit" class="btn-submit">Enregistrer les modifications</button>
        </form>
    </div>

    <!-- Gestion des rôles -->
    <div class="form-card">
        <h2>Rôles</h2>
        <p style="color:#666;font-size:0.9rem;margin-bottom:15px;">
            Au moins un rôle est obligatoire.
        </p>
        <form method="POST" action="/administrateur/roles/<?= $utilisateur->id_utilisateur ?>">
            <?php echo csrf_field() ?? '' ?>
            <div class="roles-grid">
                <?php
                $rolesDisp = [
                    'etudiant'       => 'Étudiant',
                    'entreprise'     => 'Entreprise',
                    'tuteur'         => 'Tuteur',
                    'jury'           => 'Jury',
                    'administrateur' => 'Administrateur',
                ];
                foreach ($rolesDisp as $key => $label):
                    $checked = $role && $role->$key == 1 ? 'checked' : '';
                ?>
                <label class="role-checkbox">
                    <input type="checkbox" name="role_<?= $key ?>" value="1" <?= $checked ?>>
                    <span><?= $label ?></span>
                </label>
                <?php endforeach; ?>
            </div>
            <hr class="separateur">
            <button type="submit" class="btn-submit">Mettre à jour les rôles</button>
        </form>
    </div>

    <!-- Suppression -->
    <?php if ($utilisateur->id_utilisateur !== session('user_id')): ?>
    <div class="form-card" style="border-left: 5px solid #c0392b;">
        <h2 style="color:#c0392b;">Suppression</h2>
        <p style="color:#666;font-size:0.9rem;margin-bottom:15px;">
            La suppression d'un utilisateur est définitive. Toutes ses données associées seront supprimées.
        </p>
        <form method="POST" action="/administrateur/supprimer/<?= $utilisateur->id_utilisateur ?>"
              onsubmit="return confirm('Supprimer définitivement <?= htmlspecialchars($utilisateur->prenom . ' ' . $utilisateur->nom) ?> ?');">
            <?php echo csrf_field() ?? '' ?>
            <button type="submit" style="background:#c0392b;color:white;border:none;padding:12px 25px;border-radius:6px;font-size:0.97rem;font-weight:700;cursor:pointer;">
                Supprimer cet utilisateur
            </button>
        </form>
    </div>
    <?php endif; ?>

</div>
</body>
</html>
