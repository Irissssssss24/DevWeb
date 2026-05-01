<?php
?>
<div class="card documents-card">
    <div class="card-header">
        <h2>Rapport &amp; Documents</h2>
    </div>

    <?php if ($stage): ?>
        <!-- Indicateur du stage concerné par les dépôts -->
        <div class="doc-stage-contexte">
            <span class="doc-stage-label">Stage concerné :</span>
            <span class="doc-stage-titre"><?= htmlspecialchars($offre->titre ?? 'Stage') ?></span>
            <span class="doc-stage-statut badge-statut-<?= $stage->statut === 'en_cours' ? 'en-cours' : 'valide' ?>">
                <?= $stage->statut === 'en_cours' ? 'En cours' : 'Validé' ?>
            </span>
        </div>
    <?php endif; ?>

    <!-- Liste des documents existants -->
    <div class="doc-list">
        <?php if ($documents->isEmpty()): ?>
            <p class="empty-state">Aucun document déposé pour l'instant.</p>
        <?php else: ?>
            <?php foreach ($documents as $doc):
                $meta = $typesLabels[$doc->type] ?? ['label' => ucfirst($doc->type)];
            ?>
                <div class="doc-item">
                    <div class="doc-item-left">
                        <div>
                            <div class="doc-name"><?= htmlspecialchars($meta['label']) ?></div>
                            <div class="doc-type"><?= htmlspecialchars(basename($doc->fichier ?? '')) ?></div>
                        </div>
                    </div>
                    <?php if ($doc->fichier): ?>
                        <a href="/download-<?= $doc->type ?>"
                           class="btn-small btn-dl" download>
                            Télécharger
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Formulaire de dépôt d'un nouveau document -->
    <?php if ($stage): ?>
    <form action="/upload-document" method="POST" enctype="multipart/form-data" class="upload-form">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="id_stage" value="<?= $stage->id_stage ?>">

        <label class="upload-zone" for="fichier-upload" id="upload-label">
            <input type="file" name="fichier" id="fichier-upload"
                   onchange="majLabelUpload(this)">
            <div> Choisir un fichier</div>
        </label>

        <div class="upload-footer">
            <select name="type">
                <option value="rapport">Rapport de stage</option>
                <option value="convention">Convention de stage</option>
                <option value="evaluation">Fiche d'évaluation</option>
                <option value="resume">Résumé de stage</option>
            </select>
            <button type="submit" class="btn-small btn-dl">Déposer</button>
        </div>
    </form>
    <?php else: ?>
        <p class="empty-state">Sélectionnez un stage via le filtre pour déposer des documents.</p>
    <?php endif; ?>
</div>

<style>
    /* Indicateur stage contexte */
    .doc-stage-contexte {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        padding: 8px 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 14px;
        font-size: 0.82rem;
    }

    .doc-stage-label {
        color: #64748b;
        font-weight: 500;
    }

    .doc-stage-titre {
        font-weight: 600;
        color: #1e293b;
    }

    .badge-statut-en-cours {
        display: inline-block;
        padding: 2px 9px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        background: #e3f2fd;
        color: #1565c0;
    }

    .badge-statut-valide {
        display: inline-block;
        padding: 2px 9px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        background: #e8f5e9;
        color: #2e7d32;
    }

    .doc-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 14px;
    }

    .doc-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 14px;
        background: #f8fafc;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        transition: border-color 0.2s;
    }
    .doc-item:hover { border-color: #0062AD; }

    .doc-item-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .doc-name {
        font-size: 0.88rem;
        font-weight: 500;
        color: #1e293b;
    }

    .doc-type {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 2px;
    }

    /* Upload */
    .upload-zone {
        display: block;
        border: 2px dashed #cbd5e1;
        border-radius: 10px;
        padding: 18px;
        text-align: center;
        color: #94a3b8;
        font-size: 0.85rem;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        margin-top: 12px;
    }
    .upload-zone:hover {
        border-color: #0062AD;
        background: #f0f7ff;
        color: #0062AD;
    }
    .upload-zone input[type="file"] { display: none; }

    .upload-footer {
        display: flex;
        gap: 10px;
        margin-top: 10px;
        align-items: center;
    }
    .upload-footer select {
        flex: 1;
        padding: 8px 10px;
        border: 1px solid #e2e8f0;
        border-radius: 7px;
        font-size: 0.85rem;
        color: #475569;
    }

    .documents-card {
        grid-area: documents;
    }
</style>

<script>
function majLabelUpload(input) {
    const label = document.getElementById('upload-label');
    const div = label.querySelector('div');
    if (input.files.length > 0) {
        div.textContent = input.files[0].name;
    }
}
</script>
