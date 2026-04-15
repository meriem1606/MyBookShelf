<?php
require_once '../includes/db.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérification stricte du rôle admin pour la sécurité 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /bibliotheque/index.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // Requête préparée pour le "Delete" du CRUD
    $stmt = $pdo->prepare("DELETE FROM livres WHERE id = ?");
    $stmt->execute([$id]);
}

// Redirection vers la liste admin après suppression
header('Location: /bibliotheque/admin/index.php');
exit;
?>