<?php
?>
<div class="card carnet-card">
    <div class="card-header">
        <h2>Carnet de bord</h2>
    </div>

    <!-- Affichage des entrées existantes dans l'ordre chronologique-->
    <?php if ($suivis->isEmpty()): ?>
        <p class="empty-state">Aucune entrée dans le carnet pour l'instant.</p>
    <?php else: ?>
        <div class="suivi-list">
            <?php foreach ($suivis as $i => $suivi): ?>
                <div class="suivi-entry">
                    <div class="suivi-dot"><?= $i + 1 ?></div>
                    <div class="suivi-body">
                        <div class="suivi-date">
                            <?= $suivi->date ? $suivi->date->format('d M Y') : '' ?>
                        </div>
                        <div class="suivi-texte">
                            <?= htmlspecialchars($suivi->avancement) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Formulaire d'ajout d'une entrée -->
    <?php if ($stage): ?>
        <div class="form-ajout">
            <form action="/etudiant/ajouter-suivi" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id_stage" value="<?= $stage->id_stage ?>">
                <textarea
                    name="avancement"
                    placeholder="Décrivez votre avancement cette semaine…"
                    required
                ></textarea>
                <div class="form-ajout-footer">
                    <button type="submit" class="btn-primary">
                        Ajouter
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<style>
    .suivi-list {
        display: flex;
        flex-direction: column;
        max-height: 320px;
        overflow-y: auto;
        padding-right: 4px;
        margin-bottom: 4px;
    }
    .suivi-list::-webkit-scrollbar { width: 4px; }
    .suivi-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

    .suivi-entry {
        display: flex;
        gap: 14px;
        padding-bottom: 18px;
        position: relative;
    }
    /* Ligne verticale de la timeline */
    .suivi-entry:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 14px;
        top: 30px;
        bottom: 0;
        width: 2px;
        background: #e2e8f0;
    }

    .suivi-dot {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #0062AD;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
        flex-shrink: 0;
        z-index: 1;
    }

    .suivi-body {
        flex: 1;
        background: #f8fafc;
        border-radius: 8px;
        padding: 10px 14px;
    }

    .suivi-date {
        font-size: 0.72rem;
        font-weight: 600;
        color: #0062AD;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 4px;
    }

    .suivi-texte {
        font-size: 0.85rem;
        color: #475569;
        line-height: 1.5;
    }

    /* Formulaire d'ajout */
    .form-ajout {
        margin-top: 16px;
        border-top: 2px solid #f0f4f8;
        padding-top: 16px;
    }

    .form-ajout textarea {
        width: 100%;
        min-height: 80px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 0.85rem;
        font-family: inherit;
        color: #1e293b;
        resize: vertical;
        transition: border-color 0.2s;
    }
    .form-ajout textarea:focus {
        outline: none;
        border-color: #0062AD;
    }

    .form-ajout-footer {
        display: flex;
        justify-content: flex-end;
        margin-top: 8px;
    }

    .carnet-card {
        grid-area: carnet;
        align-self:start;
    }
</style>
