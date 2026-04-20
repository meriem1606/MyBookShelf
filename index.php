<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Sélection aléatoire pour le dynamisme 
$stmt = $pdo->query("SELECT * FROM livres ORDER BY RAND() LIMIT 4");
$livres = $stmt->fetchAll();
?>

<main>
    <div class="hero">
        <h1>✿ Bienvenue sur MyBookShelf</h1>
        <p>Découvrez, notez et organisez vos lectures en un seul endroit.</p>
        <a href="/bibliotheque/livres.php" class="btn btn-primary">Explorer les livres</a>
    </div>

    <div class="section-header">
        <h1>Sélection du moment</h1>
        <a href="/bibliotheque/livres.php" class="btn btn-secondary">Voir tout</a>
    </div>

    <div class="livres-grid">
        <?php foreach ($livres as $livre): ?>
            <div class="livre-card">
                <?php if (!empty($livre['image'])): ?>
                    <img 
                        class="home-cover"
                        src="<?= htmlspecialchars($livre['image']) ?>" 
                        alt="<?= htmlspecialchars($livre['titre']) ?>"
                        onerror="this.outerHTML='<div class=\'home-placeholder\'>?</div>'"
                    >
                <?php else: ?>
                    <div class="home-placeholder">?</div>
                <?php endif; ?>
                
                <h3><?= htmlspecialchars($livre['titre']) ?></h3>
                <p class="auteur"><?= htmlspecialchars($livre['auteur']) ?></p>
                <span class="genre"><?= htmlspecialchars($livre['genre']) ?></span>
                
                <a href="/bibliotheque/fiche_livre.php?id=<?= $livre['id'] ?>" class="btn btn-primary">
                    Voir la fiche
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
