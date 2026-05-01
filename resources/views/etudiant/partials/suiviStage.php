<?php

use App\Models\Etudiant;
use App\Models\Stage;

$etudiant = Etudiant::where('id_utilisateur', session('user_id'))->first();
$candidatures = $etudiant 
    ? Stage::with('offre.entreprise')->where('id_etudiant', $etudiant->id_etudiant)->get()
    : collect();
?>

<?php if ($candidatures->count() > 0): ?>

<div class="card suivi-card">
    <h1>Mes candidatures</h1>
    <?php foreach ($candidatures as $cand): ?>
        <div style="background:#f9fbfc; border-left:4px solid #0062AD; padding:15px; border-radius:8px; margin-bottom:12px;">
            <p><strong><?= htmlspecialchars($cand->offre->titre ?? '') ?></strong>
               — <?= htmlspecialchars($cand->offre->entreprise->nom_entreprise ?? '') ?></p>
            <p style="color:#666; font-size:0.9rem;">Statut : <strong><?= htmlspecialchars($cand->statut) ?></strong></p>

            <?php if ($cand->statut === 'dates proposées'): ?>
                <p style="color:#e67e22; margin: 8px 0;">
                    📅 L'entreprise propose du <strong><?= date('d/m/Y', strtotime($cand->date_debut_proposee)) ?></strong>
                    au <strong><?= date('d/m/Y', strtotime($cand->date_fin_proposee)) ?></strong>
                </p>
                <div style="display:flex; gap:8px; margin-top:8px;">
                    <form method="POST" action="/stage/accepter-dates/<?= $cand->id_stage ?>">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <button class="bouton">✅ Accepter les dates</button>
                    </form>
                    <form method="POST" action="/stage/refuser-dates/<?= $cand->id_stage ?>">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <button class="bouton" style="background:#e74c3c;">❌ Refuser les dates</button>
                    </form>
                </div>

            <?php elseif ($cand->statut === 'en attente convention'): ?>
                <p style="color:#27ae60; margin: 8px 0;">
                    ✅ Dates acceptées ! Déposez votre convention de stage.
                </p>
                <form method="POST" action="/stage/convention/<?= $cand->id_stage ?>" enctype="multipart/form-data" style="display:flex; gap:8px; align-items:center; margin-top:8px;">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    <input type="file" name="convention" accept=".pdf" required>
                    <button class="bouton">📄 Déposer la convention</button>
                </form>

            <?php elseif ($cand->statut === 'convention soumise'): ?>
                <p style="color:#0062AD; margin: 8px 0;">
                    ⏳ Convention déposée — en attente de signature par l'entreprise.
                </p>

            <?php elseif ($cand->statut === 'en attente validation admin'): ?>
                <p style="color:#e67e22; margin: 8px 0;">
                    ⏳ Convention signée — en attente de validation par l'administrateur.
                </p>

            <?php elseif ($cand->statut === 'accepté'): ?>
                <p style="color:#27ae60; margin: 8px 0;">
                    🎉 Stage accepté ! Du <?= date('d/m/Y', strtotime($cand->date_debut)) ?>
                    au <?= date('d/m/Y', strtotime($cand->date_fin)) ?>
                </p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<style> 
.suivi-card {
    grid-area: suivi;
}
</style>
<?php endif; ?>