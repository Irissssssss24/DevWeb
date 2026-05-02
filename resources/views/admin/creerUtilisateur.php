<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Créer un utilisateur — MYstage</title>
    <link rel="stylesheet" href="/css/Adminstyle.css">
    <link rel="stylesheet" href="/css/Profilstyle.css">
    <link rel="stylesheet" href="/css/utilisateurs.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
    function afficherChamps() {
        var role = document.getElementById('role').value;
        document.getElementById('champs-etudiant').style.display     = role === 'etudiant'    ? 'block' : 'none';
        document.getElementById('champs-entreprise').style.display   = role === 'entreprise'  ? 'block' : 'none';
        document.getElementById('champs-tuteur').style.display       = role === 'tuteur'      ? 'block' : 'none';
    }
    </script>
</head>
<body>
<?php
$pageCourante = 'administrateur';
include resource_path('views/layouts/barre_nav.php');
?>

<div class="page-content">

    <a href="/administrateur/utilisateurs" class="btn-retour">← Retour à la liste</a>

    <div class="page-header">
        <h1>Créer un utilisateur</h1>
    </div>

    <?php if ($errors ?? null): ?>
    <div class="erreur">
        <strong>Erreurs :</strong>
        <ul>
            <?php foreach ($errors->all() as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST" action="/administrateur/utilisateurs/creer">
            <?php echo csrf_field() ?? '' ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="nom">Nom <span class="obligatoire">*</span></label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars(old('nom', '')) ?>" required>
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom <span class="obligatoire">*</span></label>
                    <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars(old('prenom', '')) ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email <span class="obligatoire">*</span></label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars(old('email', '')) ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="mot_de_passe">Mot de passe <span class="obligatoire">*</span></label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                </div>
                <div class="form-group">
                    <label for="mot_de_passe_confirmation">Confirmer le mot de passe <span class="obligatoire">*</span></label>
                    <input type="password" id="mot_de_passe_confirmation" name="mot_de_passe_confirmation" required>
                </div>
            </div>

            <div class="form-group">
                <label for="role">Rôle <span class="obligatoire">*</span></label>
                <select id="role" name="role" onchange="afficherChamps()" required>
                    <option value="">— Choisir un rôle —</option>
                    <option value="etudiant"       <?= old('role') === 'etudiant'       ? 'selected' : '' ?>>Étudiant</option>
                    <option value="entreprise"     <?= old('role') === 'entreprise'     ? 'selected' : '' ?>>Entreprise</option>
                    <option value="tuteur"         <?= old('role') === 'tuteur'         ? 'selected' : '' ?>>Tuteur</option>
                    <option value="jury"           <?= old('role') === 'jury'           ? 'selected' : '' ?>>Jury</option>
                    <option value="administrateur" <?= old('role') === 'administrateur' ? 'selected' : '' ?>>Administrateur</option>
                </select>
            </div>

            <!-- Champs étudiant -->
            <div id="champs-etudiant" class="champs-specifiques" style="display:none;">
                <h3>Informations étudiant</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="filiere">Filière</label>
                        <input type="text" id="filiere" name="filiere" value="<?= htmlspecialchars(old('filiere', '')) ?>" placeholder="Ex: Informatique">
                    </div>
                    <div class="form-group">
                        <label for="niveau">Niveau</label>
                        <select id="niveau" name="niveau">
                            <option value="">— Choisir —</option>
                            <?php foreach (['L1','L2','L3','M1','M2'] as $n): ?>
                                <option value="<?= $n ?>" <?= old('niveau') === $n ? 'selected' : '' ?>><?= $n ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Champs entreprise -->
            <div id="champs-entreprise" class="champs-specifiques" style="display:none;">
                <h3>Informations entreprise</h3>
                <div class="form-group">
                    <label for="nom_entreprise">Nom de l'entreprise</label>
                    <input type="text" id="nom_entreprise" name="nom_entreprise" value="<?= htmlspecialchars(old('nom_entreprise', '')) ?>">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="secteur">Secteur d'activité</label>
                        <input type="text" id="secteur" name="secteur" value="<?= htmlspecialchars(old('secteur', '')) ?>">
                    </div>
                    <div class="form-group">
                        <label for="siret">SIRET</label>
                        <input type="text" id="siret" name="siret" value="<?= htmlspecialchars(old('siret', '')) ?>" maxlength="14" placeholder="14 chiffres">
                    </div>
                </div>
                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <input type="text" id="adresse" name="adresse" value="<?= htmlspecialchars(old('adresse', '')) ?>">
                </div>
            </div>

            <!-- Champs tuteur -->
            <div id="champs-tuteur" class="champs-specifiques" style="display:none;">
                <h3>Informations tuteur</h3>
                <div class="form-group">
                    <label for="specialite">Spécialité</label>
                    <input type="text" id="specialite" name="specialite" value="<?= htmlspecialchars(old('specialite', '')) ?>">
                </div>
            </div>

            <hr class="separateur">
            <button type="submit" class="btn-submit">Créer l'utilisateur</button>
        </form>
    </div>
</div>

<script>
// Re-afficher les champs si erreur de validation (retour de formulaire)
document.addEventListener('DOMContentLoaded', function() { afficherChamps(); });
</script>
</body>
</html>
