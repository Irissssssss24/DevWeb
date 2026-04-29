<?php
$errorMessage = session()->get('error', '');
$successMessage = session()->get('success', '');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification 2FA — ProjetStage</title>
    <link rel="stylesheet" href="/css/stylePortail.css">
</head>
<body>

    <div class="conteneur-connexion">
        <h1>ProjetStage — Vérification 2FA</h1>

        <?php if ($errorMessage): ?>
            <div class="alerte alerte-erreur">
                <strong>Erreur:</strong> <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <?php if ($successMessage): ?>
            <div class="alerte alerte-succes">
                <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>

        <div style="background-color: #f9f9f9; border-left: 4px solid #0056b3; padding: 15px; border-radius: 5px; margin-bottom: 20px; font-size: 0.9rem; color: #555; line-height: 1.6;">
            <strong>ℹ️ Information:</strong> Un code à 6 chiffres a été envoyé à votre adresse email. Ce code expire dans 15 minutes.
        </div>

        <form method="POST" action="/changer-mdp/verification">
            <?php echo csrf_field(); ?>

            <div class="groupe-formulaire">
                <label for="code">Code de vérification</label>
                <input 
                    type="text" 
                    id="code" 
                    name="code" 
                    class="champ-saisie"
                    placeholder="000000" 
                    maxlength="6" 
                    pattern="[0-9]{6}"
                    inputmode="numeric"
                    autofocus
                    required
                    style="text-align: center; letter-spacing: 8px; font-weight: bold; font-size: 1.2rem;"
                >
                <div style="font-size: 0.85rem; color: #999; margin-top: 5px;">Entrez les 6 chiffres reçus par email</div>
            </div>

            <button type="submit" class="bouton-connexion">Vérifier</button>
        </form>

        <div class="liens-bas">
            <a href="/cancel-2fa" class="lien-secondaire">Annuler et se reconnecter</a>
        </div>

        <div style="text-align: center; color: #999; font-size: 0.85rem; margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd;">
            ⏱️ Code valide pendant 15 minutes
        </div>
    </div>

    <script>
        // Permettre seulement les chiffres
        document.getElementById('code').addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });

        // Soumettre automatiquement quand 6 chiffres sont entrés
        document.getElementById('code').addEventListener('input', function(e) {
            if (this.value.length === 6) {
                // Optional: auto-submit (commentez si vous ne voulez pas)
                // this.form.submit();
            }
        });
    </script>
</body>
</html>
