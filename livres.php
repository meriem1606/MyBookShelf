<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

$recherche = trim($_GET['recherche'] ?? '');
$genre = $_GET['genre'] ?? '';

$sql = "SELECT * FROM livres WHERE 1=1";
$params = [];

if ($recherche !== '') {
    $sql .= " AND (titre LIKE ? OR auteur LIKE ?)";
    $params[] = "%$recherche%";
    $params[] = "%$recherche%";
}

if ($genre !== '') {
    $sql .= " AND genre = ?";
    $params[] = $genre;
}

$sql .= " ORDER BY date_ajout DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$livres = $stmt->fetchAll();

$genres = $pdo->query("SELECT DISTINCT genre FROM livres ORDER BY genre")->fetchAll();
?>

<main>
    <div class="section-header">
        <h1>❀ Tous les livres</h1>
        <span class="result-count"><?= count($livres) ?> livre(s) trouvé(s)</span>
    </div>

    <form method="GET" class="recherche-bar">
        <input 
            type="text" 
            name="recherche" 
            placeholder="🔍 Rechercher par titre ou auteur..." 
            value="<?= htmlspecialchars($recherche) ?>"
        >
        <select name="genre">
            <option value="">Tous les genres</option>
            <?php foreach ($genres as $g): ?>
                <option value="<?= htmlspecialchars($g['genre']) ?>" 
                    <?= $genre === $g['genre'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($g['genre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Filtrer</button>
        <?php if ($recherche || $genre): ?>
            <a href="/bibliotheque/livres.php" class="btn btn-secondary">Réinitialiser</a>
        <?php endif; ?>
    </form>

    <?php if (empty($livres)): ?>
        <div class="no-results">
            <p>Aucun livre trouvé 😔</p>
        </div>
    <?php else: ?>
        <div class="livres-grid">
            <?php foreach ($livres as $livre): ?>
                <div class="livre-card">
                    <?php if (!empty($livre['image'])): ?>
                        <img 
                            class="livre-cover" 
                            src="<?= htmlspecialchars($livre['image']) ?>" 
                            alt="<?= htmlspecialchars($livre['titre']) ?>"
                            onerror="this.src='https://placehold.co/200x180/fce8e0/c47a6a?text=📚'"
                        >
                    <?php else: ?>
                        <div class="livre-cover-placeholder">📚</div>
                    <?php endif; ?>
                    
                    <h3><?= htmlspecialchars($livre['titre']) ?></h3>
                    <p class="auteur"><?= htmlspecialchars($livre['auteur']) ?></p>
                    <span class="genre"><?= htmlspecialchars($livre['genre']) ?></span>
                    
                    <?php if ($livre['annee']): ?>
                        <p class="annee-livre"><?= htmlspecialchars($livre['annee']) ?></p>
                    <?php endif; ?>
                    
                    <a href="/bibliotheque/fiche_livre.php?id=<?= $livre['id'] ?>" class="btn btn-primary">
                        Voir la fiche
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php require_once 'includes/footer.php'; ?>