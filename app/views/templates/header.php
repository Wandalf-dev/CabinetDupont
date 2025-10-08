<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'samesite' => 'Strict'
    ]);
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
    <header style="position: fixed; width: 100%; top: 0; z-index: 1000;">
        <div class="container">
            <a href="index.php" class="logo">
                <img src="/cabinetdupont/assets/dupontcare-logo-horizontal-DUPONT-white.svg" alt="DupontCare" />
            </a>

            <nav>
                <ul>
                    <li><a href="index.php?page=home">Accueil</a></li>
                    <?php if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'MEDECIN' && $_SESSION['user_role'] !== 'SECRETAIRE')): ?>
                        <li><a href="index.php?page=actus">Actualités</a></li>
                    <?php endif; ?>
                    <li><a href="#services" class="smooth-scroll">Services</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'MEDECIN' || $_SESSION['user_role'] === 'SECRETAIRE')): ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle">Administration</a>
                                <ul class="dropdown-menu">
                                    <li><a href="index.php?page=actus">Gestion des actualités</a></li>
                                    <li><a href="index.php?page=services">Gestion des services</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
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

/* Style pour le menu déroulant */
.dropdown {
    position: relative;
}

.dropdown-toggle::after {
    content: '▼';
    font-size: 0.8em;
    margin-left: 5px;
}

.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    border-radius: 4px;
    padding: 0.5em 0;
    min-width: 200px;
    z-index: 1000;
}

.dropdown:hover .dropdown-menu {
    display: block;
}

.dropdown-menu li {
    display: block;
    margin: 0;
}

.dropdown-menu a {
    color: #333;
    padding: 0.5em 1em;
    display: block;
    text-decoration: none;
}

.dropdown-menu a:hover {
    background-color: #f5f5f5;
    color: #3a6ea5;
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