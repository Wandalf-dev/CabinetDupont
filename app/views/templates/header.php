<?php
error_log("Début du chargement du header.php");
// Démarre la session avec des paramètres sécurisés si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true, // Empêche l'accès au cookie via JavaScript
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on', // Cookie envoyé uniquement en HTTPS
        'samesite' => 'Strict' // Empêche l'envoi du cookie sur des requêtes cross-site
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
    <!-- Importation des polices et des feuilles de style du projet -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/base/style.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts/header.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts/footer.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/modules/horaires/horaires.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/login.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/register.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/modules/actu/actu.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/admin.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/modules/actu/actu-create.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/modules/actu/actu-posts.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/components/table-sort.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/components/tabs.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/profil.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/components/alerts.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/modules/horaires/horaires-admin.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/about.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/modules/creneaux/creneaux.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/modules/agenda/agenda.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/components/table-actions.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/modules/rendez-vous/confirmation-rdv.css" />
    <!-- Animation Lottie pour des illustrations animées -->
    <script src="https://unpkg.com/lottie-web@5.12.2/build/player/lottie.min.js"></script>
</head>
<body>
    <header>
        <div class="container">
            <!-- Logo du cabinet en haut à gauche -->
            <a href="index.php" class="logo">
                <img src="<?php echo BASE_URL; ?>/assets/dupontcare-logo-horizontal-DUPONT-white.svg" alt="DupontCare" />
            </a>

            <!-- Menu de navigation principal -->
            <nav>
                <ul>
                    <li><a href="index.php?page=home">Accueil</a></li>
                    <li><a href="index.php?page=actus">Actualités</a></li>
                    <li><a href="index.php?page=about">À propos</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'MEDECIN' || $_SESSION['user_role'] === 'SECRETAIRE')): ?>
                            <li>
                                <a href="index.php?page=admin">Administration</a>
                            </li>
                            <?php if ($_SESSION['user_role'] === 'MEDECIN'): ?>
                            <li>
                                <a href="index.php?page=agenda&action=planning" class="nav-agenda">
                                    <i class="fas fa-calendar-alt"></i> Agenda
                                </a>
                            </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'PATIENT'): ?>
                            <li>
                                <a href="index.php?page=rendezvous&action=list" class="nav-rdv">
                                    <i class="fas fa-calendar-check"></i> Mes Rendez-vous
                                </a>
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
                <!-- Avatar SVG et nom de l'utilisateur connecté -->
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

    </header>