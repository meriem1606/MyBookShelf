<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>📜 MyBookShelf</title>
    <link rel="stylesheet" href="/bibliotheque/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="/bibliotheque/js/script.js" defer></script> </head>
<body>
<nav>
    <div class="nav-logo">📜 MyBookShelf</div>
    
    <div class="menu-toggle" id="mobile-menu">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </div>

    <ul class="nav-links">
        <li><a href="/bibliotheque/index.php">Accueil</a></li>
        <li><a href="/bibliotheque/livres.php">Livres</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="/bibliotheque/ma_liste.php">Ma liste</a></li>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li><a href="/bibliotheque/admin/index.php">Admin</a></li>
            <?php endif; ?>
            <li><a href="/bibliotheque/deconnexion.php" class="user-link">Déconnexion (<?= htmlspecialchars($_SESSION['nom'] ?? 'Utilisateur') ?>)</a></li>
        <?php else: ?>
            <li><a href="/bibliotheque/connexion.php">Connexion</a></li>
            <li><a href="/bibliotheque/inscription.php" class="btn-nav">Inscription</a></li>
        <?php endif; ?>
    </ul>
</nav>
