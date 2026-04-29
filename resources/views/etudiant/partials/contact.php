<?php
/**
 * Partial : _contact.php
 * Variables attendues : $tuteurUser, $entrepriseUser, $offre
 */
?>
<div class="card card-full">
    <div class="card-header">
        <div class="icon">✉️</div>
        <h2>Contacter mon tuteur &amp; mon entreprise</h2>
    </div>

    <div class="contact-list">

        <?php
        // Tableau de configuration pour éviter la répétition du HTML
        $contacts = [];

        if ($tuteurUser) {
            $contacts[] = [
                'id'      => 'msg-tuteur',
                'avatar'  => strtoupper(substr($tuteurUser->prenom, 0, 1) . substr($tuteurUser->nom, 0, 1)),
                'classe'  => 'avatar-tuteur',
                'nom'     => $tuteurUser->prenom . ' ' . $tuteurUser->nom,
                'role'    => 'Tuteur pédagogique',
                'email'   => $tuteurUser->email,
                'placeholder' => 'Écrivez votre message au tuteur…',
            ];
        }

        if ($entrepriseUser) {
            $contacts[] = [
                'id'      => 'msg-entreprise',
                'avatar'  => strtoupper(substr($entrepriseUser->prenom ?? 'E', 0, 1) . substr($entrepriseUser->nom ?? '', 0, 1)),
                'classe'  => 'avatar-entreprise',
                'nom'     => $offre->entreprise->nom_entreprise ?? ($entrepriseUser->prenom . ' ' . $entrepriseUser->nom),
                'role'    => 'Maître de stage / Entreprise',
                'email'   => $entrepriseUser->email,
                'placeholder' => "Écrivez votre message à l'entreprise…",
            ];
        }
        ?>

        <?php if (empty($contacts)): ?>
            <p class="empty-state">Aucun contact disponible pour le moment.</p>
        <?php endif; ?>

        <?php foreach ($contacts as $contact): ?>
            <div class="contact-wrap">

                <!-- Carte du contact -->
                <div class="contact-card">
                    <div class="contact-avatar <?= $contact['classe'] ?>">
                        <?= $contact['avatar'] ?>
                    </div>
                    <div class="contact-info">
                        <div class="contact-nom"><?= htmlspecialchars($contact['nom']) ?></div>
                        <div class="contact-role"><?= htmlspecialchars($contact['role']) ?></div>
                        <a href="mailto:<?= htmlspecialchars($contact['email']) ?>" class="contact-email">
                            <?= htmlspecialchars($contact['email']) ?>
                        </a>
                    </div>
                    <button class="btn-contact" onclick="toggleMessage('<?= $contact['id'] ?>')">
                        ✉ Envoyer un message
                    </button>
                </div>

                <!-- Formulaire de message (masqué par défaut) -->
                <div class="message-form" id="<?= $contact['id'] ?>">
                    <form action="/etudiant/contacter" method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="destinataire"     value="<?= htmlspecialchars($contact['email']) ?>">
                        <input type="hidden" name="nom_destinataire" value="<?= htmlspecialchars($contact['nom']) ?>">
                        <textarea
                            name="message"
                            placeholder="<?= htmlspecialchars($contact['placeholder']) ?>"
                            required
                        ></textarea>
                        <div class="message-form-footer">
                            <button type="button" class="btn-annuler"
                                    onclick="toggleMessage('<?= $contact['id'] ?>')">
                                Annuler
                            </button>
                            <button type="submit" class="btn-primary">
                                📨 Envoyer
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        <?php endforeach; ?>

    </div>
</div>

<style>
    .contact-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .contact-card {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px;
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }

    .contact-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: 700;
        color: white;
        flex-shrink: 0;
    }
    .avatar-tuteur     { background: linear-gradient(135deg, #0062AD, #38bdf8); }
    .avatar-entreprise { background: linear-gradient(135deg, #7c3aed, #a78bfa); }

    .contact-info { flex: 1; }

    .contact-nom {
        font-size: 0.9rem;
        font-weight: 600;
        color: #1e293b;
    }

    .contact-role {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 1px;
    }

    .contact-email {
        font-size: 0.78rem;
        color: #0062AD;
        margin-top: 3px;
        text-decoration: none;
    }
    .contact-email:hover { text-decoration: underline; }

    .btn-contact {
        background: #0062AD;
        color: white;
        border: none;
        padding: 7px 14px;
        border-radius: 7px;
        font-size: 0.78rem;
        font-weight: 500;
        cursor: pointer;
        white-space: nowrap;
        transition: background 0.2s;
    }
    .btn-contact:hover { background: #004f8a; }

    /* Formulaire de message */
    .message-form {
        display: none;
        margin-top: 10px;
        padding: 14px;
        background: #f0f7ff;
        border-radius: 8px;
        border: 1px solid #bfdbfe;
    }
    .message-form.open { display: block; }

    .message-form textarea {
        width: 100%;
        min-height: 80px;
        border: 1px solid #bfdbfe;
        border-radius: 7px;
        padding: 10px 12px;
        font-size: 0.85rem;
        font-family: inherit;
        background: white;
        resize: vertical;
    }
    .message-form textarea:focus {
        outline: none;
        border-color: #0062AD;
    }

    .message-form-footer {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 8px;
    }

    .btn-annuler {
        background: white;
        color: #64748b;
        border: 1px solid #e2e8f0;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.8rem;
        cursor: pointer;
    }
</style>

<script>
function toggleMessage(id) {
    document.getElementById(id).classList.toggle('open');
}
</script>
