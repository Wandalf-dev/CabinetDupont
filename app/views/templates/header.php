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
    <header style="position: relative;">
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

        <?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_prenom']) && isset($_SESSION['user_nom'])): ?>
            <div class="header-user-info">
                <span class="user-avatar">
                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="11" cy="11" r="11" fill="#fff"/>
                        <circle cx="11" cy="9" r="4" fill="#3a6ea5"/>
                        <ellipse cx="11" cy="16.2" rx="6" ry="3.2" fill="#3a6ea5"/>
                    </svg>
                </span>
                <span class="user-name">
                    <?php echo htmlspecialchars($_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom']); ?>
                </span>
            </div>
        <?php endif; ?>
<style>
.header-user-info {
    position: absolute;
    top: 50%;
    right: 2vw;
    transform: translateY(-50%);
    display: flex;
    align-items: center;
    color: #fff;
    font-weight: 600;
    font-size: 1rem;
    gap: 0.5em;
    background: linear-gradient(90deg, #3a6ea5 80%, #1a355b 100%);
    border-radius: 2em;
    padding: 0.3em 1.1em 0.3em 0.7em;
    z-index: 10;
}
.header-user-info .user-avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 1.7em;
    height: 1.7em;
    background: none;
    border-radius: 0;
    margin-right: 0.2em;
    box-shadow: none;
}
.header-user-info .user-avatar svg {
    width: 1.3em;
    height: 1.3em;
    display: block;
}
.header-user-info .user-name {
    white-space: nowrap;
    font-size: 1em;
    font-weight: 600;
    letter-spacing: 0.01em;
}
</style>
    </header>