<?php
require_once 'includes/db.php';
// On appelle le header qui contient déjà le session_start()
require_once 'includes/header.php';

$message = null; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $mdp = $_POST['mot_de_passe'];

    if (empty($email) || empty($mdp)) {
        $message = ['type' => 'error', 'texte' => 'Veuillez remplir tous les champs.'];
    } else {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($mdp, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['role'] = $user['role'];
            header('Location: /bibliotheque/index.php');
            exit;
        } else {
            $message = ['type' => 'error', 'texte' => 'Email ou mot de passe incorrect.'];
        }
    }
}
?>

<main>
    <div class="form-container">
        <h2>🌸 Connexion</h2>

        <?php if ($message): ?>
            <div class="message <?= $message['type'] ?>">
                <?= htmlspecialchars($message['texte']) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="votre@email.com" required>
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="mot_de_passe" placeholder="Votre mot de passe" required>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Se connecter</button>
        </form>

        <p class="form-footer-text">
            Pas encore de compte ? <a href="/bibliotheque/inscription.php" class="form-link">S'inscrire</a>
        </p>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>