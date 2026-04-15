<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $mdp = $_POST['mot_de_passe'];
    $mdp_confirm = $_POST['mot_de_passe_confirm'];

    if (empty($nom) || empty($email) || empty($mdp)) {
        $message = ['type' => 'error', 'texte' => 'Veuillez remplir tous les champs.'];
    } elseif ($mdp !== $mdp_confirm) {
        $message = ['type' => 'error', 'texte' => 'Les mots de passe ne correspondent pas.'];
    } elseif (strlen($mdp) < 6) {
        $message = ['type' => 'error', 'texte' => 'Le mot de passe doit faire au moins 6 caractères.'];
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $message = ['type' => 'error', 'texte' => 'Cet email est déjà utilisé.'];
        } else {
            $hash = password_hash($mdp, PASSWORD_DEFAULT);
            // On ajoute 'user' par défaut pour le rôle
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, 'user')");
            $stmt->execute([$nom, $email, $hash]);
            $message = ['type' => 'success', 'texte' => 'Compte créé avec succès ! Vous pouvez vous connecter.'];
        }
    }
}
?>

<main>
    <div class="form-container">
        <h2>✨ Inscription</h2>

        <?php if ($message): ?>
            <div class="message <?= $message['type'] ?>">
                <?= htmlspecialchars($message['texte']) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nom</label>
                <input type="text" name="nom" placeholder="Votre nom" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="votre@email.com" required>
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="mot_de_passe" placeholder="Min. 6 caractères" required>
            </div>
            <div class="form-group">
                <label>Confirmer le mot de passe</label>
                <input type="password" name="mot_de_passe_confirm" placeholder="Répétez le mot de passe" required>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Créer mon compte</button>
        </form>

        <p class="form-footer-text">
            Déjà un compte ? <a href="/bibliotheque/connexion.php" class="form-link">Se connecter</a>
        </p>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>