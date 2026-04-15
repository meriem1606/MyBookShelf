<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Rediriger si non connécté 
if (!isset($_SESSION['user_id'])) {
    header('Location: /bibliotheque/connexion.php');
    exit;
}

// Supprimer un livre de la liste (Contrôleur)
if (isset($_POST['supprimer'])) {
    $id_liste = intval($_POST['id_liste']);
    $stmt = $pdo->prepare("DELETE FROM liste_lecture WHERE id = ? AND id_utilisateur = ?");
    $stmt->execute([$id_liste, $_SESSION['user_id']]);
    header('Location: /bibliotheque/ma_liste.php');
    exit;
}

// Changer le statut (Contrôleur)
if (isset($_POST['changer_statut'])) {
    $id_liste = intval($_POST['id_liste']);
    $statut = $_POST['statut'];
    $stmt = $pdo->prepare("UPDATE liste_lecture SET statut = ? WHERE id = ? AND id_utilisateur = ?");
    $stmt->execute([$statut, $id_liste, $_SESSION['user_id']]);
    header('Location: /bibliotheque/ma_liste.php');
    exit;
}

// Récupérer la liste de lecture
$stmt = $pdo->prepare("
    SELECT liste_lecture.*, livres.titre, livres.auteur, livres.genre
    FROM liste_lecture
    JOIN livres ON liste_lecture.id_livre = livres.id
    WHERE liste_lecture.id_utilisateur = ?
    ORDER BY liste_lecture.date_ajout DESC
");
$stmt->execute([$_SESSION['user_id']]);
$liste = $stmt->fetchAll();

// Statistiques par statut (Utilisation de tableaux) 
$a_lire = array_filter($liste, fn($l) => $l['statut'] === 'à lire');
$en_cours = array_filter($liste, fn($l) => $l['statut'] === 'en cours');
$lus = array_filter($liste, fn($l) => $l['statut'] === 'lu');
?>

<main>
    <div class="section-header">
        <h1>❀ Ma liste de lecture</h1>
        <span class="result-count"><?= count($liste) ?> livre(s)</span>
    </div>

    <div class="stats-grid">
        <div class="stat-item border-peach">
            <div class="stat-value color-peach"><?= count($a_lire) ?></div>
            <div class="stat-label">À lire</div>
        </div>
        <div class="stat-item border-blue">
            <div class="stat-value color-blue"><?= count($en_cours) ?></div>
            <div class="stat-label">En cours</div>
        </div>
        <div class="stat-item border-green">
            <div class="stat-value color-green"><?= count($lus) ?></div>
            <div class="stat-label">Lus</div>
        </div>
    </div>

    <?php if (empty($liste)): ?>
        <div class="no-results-list">
            <p class="empty-msg">Votre liste est vide </p>
            <p>Ajoutez des livres depuis leur fiche !</p>
            <a href="/bibliotheque/livres.php" class="btn btn-primary">Explorer les livres</a>
        </div>
    <?php else: ?>
        <?php foreach ($liste as $item): ?>
            <div class="liste-item">
                <div class="item-info">
                    <h3>
                        <a href="/bibliotheque/fiche_livre.php?id=<?= $item['id_livre'] ?>">
                            <?= htmlspecialchars($item['titre']) ?>
                        </a>
                    </h3>
                    <p class="item-auteur"><?= htmlspecialchars($item['auteur']) ?></p>
                </div>

                <div class="liste-actions">
                    <form method="POST" class="form-inline">
                        <input type="hidden" name="id_liste" value="<?= $item['id'] ?>">
                        <select name="statut" class="select-small">
                            <option value="à lire" <?= $item['statut'] === 'à lire' ? 'selected' : '' ?>>À lire</option>
                            <option value="en cours" <?= $item['statut'] === 'en cours' ? 'selected' : '' ?>>En cours</option>
                            <option value="lu" <?= $item['statut'] === 'lu' ? 'selected' : '' ?>>Lu</option>
                        </select>
                        <button type="submit" name="changer_statut" value="1" class="btn btn-secondary btn-xs">
                            Mettre à jour
                        </button>
                    </form>

                    <form method="POST">
                        <input type="hidden" name="id_liste" value="<?= $item['id'] ?>">
                        <button type="submit" name="supprimer" value="1" class="btn btn-danger btn-xs"
                            onclick="return confirm('Retirer ce livre de votre liste ?')">
                            ✕ Retirer
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<?php require_once 'includes/footer.php'; ?>