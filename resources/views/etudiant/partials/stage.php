<?php
?>
<div class="card card-full stage-card">
    <div class="card-header">
        <h2>Mon stage </h2>
    </div>

    <?php if ($stage && $offre): ?>

        <div class="stage-grid">
            <div class="stage-info-item">
                <div class="label">Entreprise</div>
                <div class="value">
                    <?= htmlspecialchars($offre->entreprise->nom_entreprise ?? '–') ?>
                </div>
            </div>
            <div class="stage-info-item">
                <div class="label">Poste</div>
                <div class="value"><?= htmlspecialchars($offre->titre) ?></div>
            </div>
            <div class="stage-info-item">
                <div class="label">Statut</div>
                <div class="value">
                    <span class="badge-statut badge-<?= htmlspecialchars($stage->statut ?? 'en_attente') ?>">
                        <?= match($stage->statut) {
                            'en_cours'   => 'En cours',
                            'termine'    => 'Terminé',
                            'en_attente' => 'En attente',
                            default      => htmlspecialchars($stage->statut ?? '–')
                        } ?>
                    </span>
                </div>
            </div>
            <div class="stage-info-item">
                <div class="label">Date de début</div>
                <div class="value">
                    <?= $stage->date_debut ? $stage->date_debut->format('d/m/Y') : '–' ?>
                </div>
            </div>
            <div class="stage-info-item">
                <div class="label">Date de fin</div>
                <div class="value">
                    <?= $stage->date_fin ? $stage->date_fin->format('d/m/Y') : '–' ?>
                </div>
            </div>
            <div class="stage-info-item">
                <div class="label">Durée</div>
                <div class="value"><?= htmlspecialchars($offre->duree ?? '–') ?></div>
            </div>
        </div>

        <div class="progression-wrap">
            <div class="progression-label">
                <span>Progression du stage</span>
                <span><?= $progression ?>%</span>
            </div>
            <div class="barre">
                <div class="barre-fill" style="width: <?= $progression ?>%"></div>
            </div>
        </div>

    <?php else: ?>
        <p class="empty-state">Aucun stage en cours pour le moment.</p>
    <?php endif; ?>
</div>

<style>
    .stage-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 14px;
    }

    .stage-card {
        grid-area: stage;
    }

    .stage-info-item {
        background: #f8fafc;
        border-radius: 8px;
        padding: 12px 16px;
    }

    .stage-info-item .label {
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
        margin-bottom: 4px;
    }

    .stage-info-item .value {
        font-size: 0.92rem;
        font-weight: 500;
        color: #1e293b;
    }

    .badge-statut {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
    }
    .badge-en_cours   { background: #dcfce7; color: #16a34a; }
    .badge-termine    { background: #e0e7ff; color: #4338ca; }
    .badge-en_attente { background: #fef9c3; color: #ca8a04; }

    .progression-wrap { margin-top: 18px; }

    .progression-label {
        display: flex;
        justify-content: space-between;
        font-size: 0.8rem;
        color: #64748b;
        margin-bottom: 6px;
    }

    .barre {
        height: 8px;
        background: #e2e8f0;
        border-radius: 99px;
        overflow: hidden;
    }

    .barre-fill {
        height: 100%;
        background: linear-gradient(90deg, #0062AD, #38bdf8);
        border-radius: 99px;
        transition: width 0.6s ease;
    }

    @media (max-width: 600px) {
        .stage-grid { grid-template-columns: 1fr 1fr; }
    }
</style>
