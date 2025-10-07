<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DupontCare – Cabinet dentaire</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/login.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/register.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/actu.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/admin.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/actu-create.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/actu-posts.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/table-sort.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/tabs.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/profil.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/alerts.css" />
    <script src="https://unpkg.com/lottie-web@5.12.2/build/player/lottie.min.js"></script>
</head>
<body>
    <header>
        <div class="container">
            <a href="index.php" class="logo">
                <img src="/cabinetdupont/assets/dupontcare-logo-horizontal-DUPONT-white.svg" alt="DupontCare" />
            </a>

            <nav>
                <ul>
                    <li><a href="index.php?page=home">Accueil</a></li>
                    <li><a href="index.php?page=actus">Actualités</a></li>
                    <li><a href="index.php?page=home#services">Services</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="index.php?page=user&action=profile">Mon profil</a></li>
                        <li><a href="index.php?page=auth&action=logout">Déconnexion</a></li>
                    <?php else: ?>
                        <li><a href="index.php?page=auth&action=login">Connexion</a></li>
                        <li><a href="index.php?page=auth&action=register">Inscription</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>