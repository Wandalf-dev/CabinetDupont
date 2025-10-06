<?php
session_start();

require_once __DIR__ . '/app/models/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        header('Location: login.php?error=missing');
        exit();
    }

    $pdo = Database::getPDO();
    $stmt = $pdo->prepare('SELECT id, nom, prenom, email, password_hash, avatar, date_inscription FROM utilisateur WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // Auth OK : on stocke l'id en session
        $_SESSION['user_id'] = $user['id'];
        header('Location: index.php');
        exit();
    } else {
        // Auth KO
        header('Location: login.php?error=invalid');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}
