<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

$id = intval($_GET['id'] ?? 0);

if (!$id) {
    header('Location: /bibliotheque/livres.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM livres WHERE id = ?");
$stmt->execute([$id]);
$livre = $stmt->fetch();

if (!$livre) {
    header('Location: /bibliotheque/livres.php');
    exit;
}

$stmt = $pdo->prepare("SELECT AVG(note) as moyenne, COUNT(*) as total FROM avis WHERE id_livre = ?");
$stmt->execute([$id]);
$stats = $stmt->fetch();

$stmt = $pdo->prepare("
    SELECT avis.*, utilisateurs.nom 
    FROM avis 
    JOIN utilisateurs ON avis.id_utilisateur = utilisateurs.id 
    WHERE avis.id_livre = ? 
    ORDER BY avis.date_avis DESC
");
$stmt->execute([$id]);
$avis_liste = $stmt->fetchAll();

$reponses = [];
foreach ($avis_liste as $avis) {
    $stmt = $pdo->prepare("
        SELECT reponses_avis.*, utilisateurs.nom 
        FROM reponses_avis 
        JOIN utilisateurs ON reponses_avis.id_utilisateur = utilisateurs.id 
        WHERE reponses_avis.id_avis = ? 
        ORDER BY reponses_avis.date_reponse ASC
    ");
    $stmt->execute([$avis['id']]);
    $reponses[$avis['id']] = $stmt->fetchAll();
}

$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    if (isset($_POST['repondre_avis'])) {
        $id_avis = intval($_POST['id_avis']);
        $commentaire = trim($_POST['reponse']);
        if (!empty($commentaire)) {
            $stmt = $pdo->prepare("INSERT INTO reponses_avis (id_avis, id_utilisateur, commentaire) VALUES (?, ?, ?)");
            $stmt->execute([$id_avis, $_SESSION['user_id'], $commentaire]);
            header("Location: /bibliotheque/fiche_livre.php?id=$id");
            exit;
        }
    }

    if (isset($_POST['publier_avis'])) {
        $note = intval($_POST['note']);
        $commentaire = trim($_POST['commentaire']);
        $spoiler = isset($_POST['spoiler']) ? 1 : 0;

        if ($note < 1 || $note > 5) {
            $message = ['type' => 'error', 'texte' => 'Veuillez choisir une note entre 1 et 5.'];
        } else {
            $stmt = $pdo->prepare("SELECT id FROM avis WHERE id_utilisateur = ? AND id_livre = ?");
            $stmt->execute([$_SESSION['user_id'], $id]);
            if ($stmt->fetch()) {
                $message = ['type' => 'error', 'texte' => 'Vous avez déjà laissé un avis pour ce livre.'];
            } else {
                $stmt = $pdo->prepare("INSERT INTO avis (id_utilisateur, id_livre, note, commentaire, spoiler) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $id, $note, $commentaire, $spoiler]);
                header("Location: /bibliotheque/fiche_livre.php?id=$id");
                exit;
            }
        }
    }
}

if (isset($_POST['ajouter_liste']) && isset($_SESSION['user_id'])) {
    $statut = $_POST['statut'];
    $stmt = $pdo->prepare("SELECT id FROM liste_lecture WHERE id_utilisateur = ? AND id_livre = ?");
    $stmt->execute([$_SESSION['user_id'], $id]);
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO liste_lecture (id_utilisateur, id_livre, statut) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $id, $statut]);
    }
    header("Location: /bibliotheque/fiche_livre.php?id=$id");
    exit;
}

$dans_liste = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT id FROM liste_lecture WHERE id_utilisateur = ? AND id_livre = ?");
    $stmt->execute([$_SESSION['user_id'], $id]);
    $dans_liste = $stmt->fetch() ? true : false;
}

function etoiles($note) {
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        $html .= $i <= $note ? '★' : '☆';
    }
    return $html;
}
?>

<main>
    <div class="fiche-livre">
        <div class="fiche-container">
            <?php if (!empty($livre['image'])): ?>
                <img class="fiche-image" src="<?= htmlspecialchars($livre['image']) ?>" alt="Couverture de <?= htmlspecialchars($livre['titre']) ?>" onerror="this.outerHTML='<div class=\'fiche-placeholder\'>?</div>'">
            <?php else: ?>
                <div class="fiche-placeholder">?</div>
            <?php endif; ?>

            <div class="fiche-infos">
                <h1><?= htmlspecialchars($livre['titre']) ?></h1>
                <div class="fiche-meta">
                    <span>✍️ <?= htmlspecialchars($livre['auteur']) ?></span>
                    <?php if ($livre['genre']): ?>
                        <span>📖 <?= htmlspecialchars($livre['genre']) ?></span>
                    <?php endif; ?>
                    <?php if ($livre['annee']): ?>
                        <span>📅 <?= htmlspecialchars($livre['annee']) ?></span>
                    <?php endif; ?>
                    <?php if ($stats['total'] > 0): ?>
                        <span class="etoiles-moyenne">
                            <?= etoiles(round($stats['moyenne'])) ?>
                            (<?= number_format($stats['moyenne'], 1) ?>/5 — <?= $stats['total'] ?> avis)
                        </span>
                    <?php endif; ?>
                </div>

                <?php if ($livre['description']): ?>
                    <p class="fiche-description">
                        <?= nl2br(htmlspecialchars($livre['description'])) ?>
                    </p>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (!$dans_liste): ?>
                        <form method="POST" class="form-liste">
                            <select name="statut" class="select-statut">
                                <option value="à lire">À lire</option>
                                <option value="en cours">En cours</option>
                                <option value="lu">Lu</option>
                            </select>
                            <button type="submit" name="ajouter_liste" value="1" class="btn btn-secondary">
                                + Ajouter à ma liste
                            </button>
                        </form>
                    <?php else: ?>
                        <p class="msg-succes-liste">
                            ✅ Ce livre est dans votre liste.
                            <a href="/bibliotheque/ma_liste.php" class="form-link">Voir ma liste</a>
                        </p>
                    <?php endif; ?>
                <?php endif; ?>

                <a href="/bibliotheque/livres.php" class="btn btn-secondary">← Retour aux livres</a>
            </div>
        </div>

        <hr class="fiche-separator">
        <h2 class="fiche-titre-avis">💬 Avis des lecteurs</h2>

        <?php if ($message): ?>
            <div class="message <?= $message['type'] ?>">
                <?= htmlspecialchars($message['texte']) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="form-avis-container">
                <h3>Laisser un avis</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Note</label>
                        <select name="note">
                            <option value="5">★★★★★ — Excellent</option>
                            <option value="4">★★★★☆ — Très bien</option>
                            <option value="3">★★★☆☆ — Bien</option>
                            <option value="2">★★☆☆☆ — Moyen</option>
                            <option value="1">★☆☆☆☆ — Déçu(e)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Commentaire (optionnel)</label>
                        <textarea name="commentaire" placeholder="Partagez votre avis..."></textarea>
                    </div>
                    <div class="spoiler-checkbox">
                        <input type="checkbox" name="spoiler" id="spoiler">
                        <label for="spoiler">⚠️ Mon avis contient des <strong>spoilers</strong></label>
                    </div>
                    <button type="submit" name="publier_avis" value="1" class="btn btn-primary">Publier mon avis</button>
                </form>
            </div>
        <?php else: ?>
            <p class="msg-connexion-avis">
                <a href="/bibliotheque/connexion.php" class="form-link">Connectez-vous</a> pour laisser un avis.
            </p>
        <?php endif; ?>

        <div class="avis-liste">
            <?php if (empty($avis_liste)): ?>
                <p class="no-results">Aucun avis pour le moment. Soyez le premier ! 🌸</p>
            <?php else: ?>
                <?php foreach ($avis_liste as $avis): ?>
                    <div class="avis-item">
                        <div class="avis-header">
                            <span class="avis-auteur"><?= htmlspecialchars($avis['nom']) ?></span>
                            <span class="etoiles"><?= etoiles($avis['note']) ?></span>
                        </div>

                        <?php if ($avis['spoiler']): ?>
                            <div class="spoiler-badge">⚠️ Contient des spoilers</div>
                            <div class="spoiler-content">
                                <div class="spoiler-mask" onclick="this.style.display='none'; this.nextElementSibling.style.display='block'">
                                    👁️ Cliquez pour révéler le spoiler
                                </div>
                                <div class="spoiler-hidden">
                                    <?php if ($avis['commentaire']): ?>
                                        <p><?= nl2br(htmlspecialchars($avis['commentaire'])) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php if ($avis['commentaire']): ?>
                                <p class="avis-texte"><?= nl2br(htmlspecialchars($avis['commentaire'])) ?></p>
                            <?php endif; ?>
                        <?php endif; ?>

                        <p class="avis-date"><?= date('d/m/Y', strtotime($avis['date_avis'])) ?></p>

                        <?php if (!empty($reponses[$avis['id']])): ?>
                            <div class="reponses-container">
                                <?php foreach ($reponses[$avis['id']] as $rep): ?>
                                    <div class="reponse-item">
                                        <div class="reponse-header">
                                            <span class="reponse-auteur">💬 <?= htmlspecialchars($rep['nom']) ?></span>
                                            <span class="reponse-date"><?= date('d/m/Y', strtotime($rep['date_reponse'])) ?></span>
                                        </div>
                                        <p class="reponse-texte"><?= nl2br(htmlspecialchars($rep['commentaire'])) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="reponse-action">
                                <button class="btn-toggle-reponse" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none'">
                                    💬 Répondre à cet avis
                                </button>
                                <div class="form-reponse">
                                    <form method="POST">
                                        <input type="hidden" name="id_avis" value="<?= $avis['id'] ?>">
                                        <textarea name="reponse" placeholder="Votre réponse..."></textarea>
                                        <button type="submit" name="repondre_avis" value="1" class="btn btn-primary btn-sm">Envoyer</button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>