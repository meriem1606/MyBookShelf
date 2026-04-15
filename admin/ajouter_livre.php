<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

// --- PARTIE CONTRÔLEUR (Logique) ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /bibliotheque/index.php');
    exit;
}

$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collecte des données via $_POST 
    $titre = trim($_POST['titre']);
    $auteur = trim($_POST['auteur']);
    $genre = trim($_POST['genre']);
    $description = trim($_POST['description']);
    $annee = intval($_POST['annee']);
    $image = trim($_POST['image']); 

    // Validation simple 
    if (empty($titre) || empty($auteur)) {
        $message = ['type' => 'error', 'texte' => 'Le titre et l\'auteur sont obligatoires.'];
    } else {
        // --- PARTIE MODÈLE (Requête SQL) --- 
        $stmt = $pdo->prepare("INSERT INTO livres (titre, auteur, genre, description, annee, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$titre, $auteur, $genre, $description, $annee, $image]);
        
        header('Location: /bibliotheque/admin/index.php');
        exit;
    }
}
?>

<main>
    <div class="form-container form-admin">
        <h2>➕ Ajouter un livre</h2>

        <?php if ($message): ?>
            <div class="message <?= $message['type'] ?>">
                <?= htmlspecialchars($message['texte']) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Titre *</label>
                <input type="text" name="titre" placeholder="Titre du livre" required>
            </div>
            <div class="form-group">
                <label>Auteur *</label>
                <input type="text" name="auteur" placeholder="Nom de l'auteur" required>
            </div>
            <div class="form-group">
                <label>Genre</label>
                <input type="text" name="genre" placeholder="Roman, Fantasy, Dystopie...">
            </div>
            <div class="form-group">
                <label>Année de publication</label>
                <input type="number" name="annee" placeholder="Ex: 2024" min="0" max="2100">
            </div>
            <div class="form-group">
                <label>URL de l'image (Open Library)</label>
                <input type="url" name="image" placeholder="https://covers.openlibrary.org/...">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" placeholder="Résumé du livre..."></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Ajouter le livre</button>
                <a href="/bibliotheque/admin/index.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>