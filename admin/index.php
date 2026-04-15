<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

// Réservé aux admin uniquement 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /bibliotheque/index.php');
    exit;
}

// Stats
$nb_livres = $pdo->query("SELECT COUNT(*) FROM livres")->fetchColumn();
$nb_users = $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
$nb_avis = $pdo->query("SELECT COUNT(*) FROM avis")->fetchColumn();

// Liste des livres 
$livres = $pdo->query("SELECT * FROM livres ORDER BY date_ajout DESC")->fetchAll();
?>

<main>
    <div class="section-header">
        <h1>🔐 Espace Administrateur</h1>
        <a href="/bibliotheque/admin/ajouter_livre.php" class="btn btn-primary">+ Ajouter un livre</a>
    </div>

    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-number"><?= $nb_livres ?></div>
            <div class="stat-label">Livres</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $nb_users ?></div>
            <div class="stat-label">Utilisateurs</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $nb_avis ?></div>
            <div class="stat-label">Avis</div>
        </div>
    </div>

    <h2 class="admin-table-title">Gérer les livres</h2>
    
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Couverture</th>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Genre</th>
                    <th>Année</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($livres as $livre): ?>
                    <tr>
                        <td>
                            <?php if (!empty($livre['image'])): ?>
                                <img 
                                    src="<?= htmlspecialchars($livre['image']) ?>" 
                                    alt="<?= htmlspecialchars($livre['titre']) ?>"
                                    class="admin-thumb"
                                    onerror="this.style.display='none'"
                                >
                            <?php else: ?>
                                <span class="admin-emoji-placeholder">📖</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= htmlspecialchars($livre['titre']) ?></strong></td>
                        <td class="admin-auteur-cell"><?= htmlspecialchars($livre['auteur']) ?></td>
                        <td><?= htmlspecialchars($livre['genre']) ?></td>
                        <td><?= htmlspecialchars($livre['annee']) ?></td>
                        <td>
                            <div class="admin-actions">
                                <a href="/bibliotheque/admin/modifier_livre.php?id=<?= $livre['id'] ?>" 
                                   class="btn btn-secondary btn-table">
                                    ✏️ Modifier
                                </a>
                                <a href="/bibliotheque/admin/supprimer_livre.php?id=<?= $livre['id'] ?>" 
                                   class="btn btn-danger btn-table btn-delete"
                                   data-titre="<?= htmlspecialchars($livre['titre']) ?>">
                                    🗑️ Supprimer
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>