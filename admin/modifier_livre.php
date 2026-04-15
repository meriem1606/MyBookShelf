<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

// Sécurité Admin : vérification du rôle dans la session 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /bibliotheque/index.php');
    exit;
}

// Récupération de l'ID via la méthode GET 
$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: /bibliotheque/admin/index.php');
    exit;
}

// Récupération des données actuelles du livre 
$stmt = $pdo->prepare("SELECT * FROM livres WHERE id = ?");
$stmt->execute([$id]);
$livre = $stmt->fetch();

if (!$livre) {
    header('Location: /bibliotheque/admin/index.php');
    exit;
}

$message = null;

// Traitement du formulaire lors de la soumission (Contrôleur) 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $auteur = trim($_POST['auteur']);
    $genre = trim($_POST['genre']);
    $description = trim($_POST['description']);
    $annee = intval($_POST['annee']);
    $image = trim($_POST['image']); // Ajout du traitement de l'image 

    if (empty($titre) || empty($auteur)) {
        $message = ['type' => 'error', 'texte' => 'Le titre et l\'auteur sont obligatoires.'];
    } else {
        // Requête préparée pour mettre à jour la base de données 
        $stmt = $pdo->prepare("UPDATE livres SET titre=?, auteur=?, genre=?, description=?, annee=?, image=? WHERE id=?");
        $stmt->execute([$titre, $auteur, $genre, $description, $annee, $image, $id]);
        
        header('Location: /bibliotheque/admin/index.php');
        exit;
    }
}
?>

<main>
    <div class="form-container form-admin">
        <h2>✏️ Modifier un livre</h2>

        <?php if ($message): ?>
            <div class="message <?= $message['type'] ?>">
                <?= htmlspecialchars($message['texte']) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Titre *</label>
                <input type="text" name="titre" value="<?= htmlspecialchars($livre['titre']) ?>" required>
            </div>
            <div class="form-group">
                <label>Auteur *</label>
                <input type="text" name="auteur" value="<?= htmlspecialchars($livre['auteur']) ?>" required>
            </div>
            <div class="form-group">
                <label>Genre</label>
                <input type="text" name="genre" value="<?= htmlspecialchars($livre['genre']) ?>">
            </div>
            <div class="form-group">
                <label>Année de publication</label>
                <input type="number" name="annee" value="<?= htmlspecialchars($livre['annee']) ?>" min="0" max="2100">
            </div>
            <div class="form-group">
                <label>URL de l'image (Open Library)</label>
                <input type="url" name="image" value="<?= htmlspecialchars($livre['image']) ?>" placeholder="https://covers.openlibrary.org/...">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description"><?= htmlspecialchars($livre['description']) ?></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="/bibliotheque/admin/index.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>