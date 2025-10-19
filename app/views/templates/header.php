<?php
error_log("Début du chargement du header.php");

// Inclusion de la configuration pour avoir accès à BASE_URL
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../../config/config.php';
}

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'samesite' => 'Strict'
    ]);
    session_start();
}

// Récupérer la page et l'action pour le SEO
$currentPage = $_GET['page'] ?? 'home';
$currentAction = $_GET['action'] ?? null;

// Charger la classe SEO
use App\Core\Seo;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="<?php echo \App\Core\Csrf::generateToken(); ?>" />
    
    <!-- Titre dynamique SEO -->
    <title><?php echo Seo::getTitle($currentPage, $currentAction); ?></title>
    
    <!-- Meta tags SEO dynamiques -->
    <?php echo Seo::renderMetaTags($currentPage, $currentAction); ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- CSS de base - Chargés sur TOUTES les pages -->
    <?php 
    $cssFiles = [
        '/css/base/polices.css',         // Polices (Poppins partout)
        '/css/base/style.css',           // Styles globaux
        '/css/layouts/header.css',       // Header du site
        '/css/layouts/footer.css',       // Footer du site
        '/css/components/alerts.css'     // Système d'alertes global
    ];
    $timestamp = time();
    foreach($cssFiles as $css): ?>
        <link rel="stylesheet" href="<?php echo BASE_URL . $css . '?v=' . $timestamp; ?>" />
    <?php endforeach; ?>
    
    <!-- Les autres CSS doivent être chargés dans leurs pages respectives -->
    <script src="https://unpkg.com/lottie-web@5.12.2/build/player/lottie.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/js/components/alerts.js"></script>
    <script src="<?php echo BASE_URL; ?>/js/modules/header/header.js"></script>
    
    <!-- Données structurées Schema.org pour le référencement -->
    <?php if ($currentPage === 'home'): ?>
        <?php echo Seo::renderStructuredData('organization'); ?>
    <?php endif; ?>
</head>
<body>
    <!-- Toast container -->
    <div id="toast-container"></div>

    <header class="modern-header">
        <div class="header-content">
            <!-- Menu burger -->
            <button class="mobile-menu-toggle" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>

            <!-- Logo -->
            <a href="index.php" class="brand">
                <img src="<?php echo BASE_URL; ?>/assets/dupontcare-logo-horizontal-DUPONT-white.svg" alt="DupontCare" class="logo" />
            </a>

            <!-- Menu principal -->
            <nav class="main-nav">
                <div class="nav-links">
                    <a href="index.php?page=home" class="nav-item">Accueil</a>
                    <a href="index.php?page=actus" class="nav-item">Actualités</a>
                    <a href="index.php?page=about" class="nav-item">À propos</a>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'MEDECIN' || $_SESSION['user_role'] === 'SECRETAIRE')): ?>
                            <a href="index.php?page=admin" class="nav-item admin-link">
                                <i class="fas fa-cog"></i>
                                <span>Administration</span>
                            </a>
                            
                            <?php if ($_SESSION['user_role'] === 'MEDECIN'): ?>
                                <a href="index.php?page=agenda&action=planning" class="nav-item agenda-link">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Agenda</span>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>

            </nav>

            <!-- Menu utilisateur -->
            <div class="user-menu">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="user-dropdown">
                        <button class="user-button">
                                <div class="user-avatar">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="12" fill="#fff"/>
                                        <circle cx="12" cy="10" r="4" fill="#3a6ea5"/>
                                        <ellipse cx="12" cy="17.5" rx="6" ry="3.5" fill="#3a6ea5"/>
                                    </svg>
                                </div>
                                <span class="user-name">
                                    <?php 
                                    $nom = htmlspecialchars($_SESSION['user_nom'] ?? '');
                                    $prenom = htmlspecialchars($_SESSION['user_prenom'] ?? '');
                                    $premiereLettre = mb_substr($prenom, 0, 1);
                                    echo strtoupper($nom) . '.' . strtoupper($premiereLettre);
                                    ?>
                                </span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="index.php?page=home" class="dropdown-item mobile-nav-item">
                                    <i class="fas fa-home"></i>
                                    <span>Accueil</span>
                                </a>
                                <a href="index.php?page=actus" class="dropdown-item mobile-nav-item">
                                    <i class="fas fa-newspaper"></i>
                                    <span>Actualités</span>
                                </a>
                                <a href="index.php?page=about" class="dropdown-item mobile-nav-item">
                                    <i class="fas fa-info-circle"></i>
                                    <span>À propos</span>
                                </a>
                                <div class="dropdown-divider mobile-nav-divider"></div>
                                
                                <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'MEDECIN' || $_SESSION['user_role'] === 'SECRETAIRE')): ?>
                                    <a href="index.php?page=admin" class="dropdown-item mobile-nav-item">
                                        <i class="fas fa-cog"></i>
                                        <span>Administration</span>
                                    </a>
                                    
                                    <?php if ($_SESSION['user_role'] === 'MEDECIN'): ?>
                                        <a href="index.php?page=agenda&action=planning" class="dropdown-item mobile-nav-item">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span>Agenda</span>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <div class="dropdown-divider mobile-nav-divider"></div>
                                <?php endif; ?>
                                
                                <a href="index.php?page=user&action=profile" class="dropdown-item">
                                    <i class="fas fa-user"></i>
                                    <span>Mon profil</span>
                                </a>
                                <?php if ($_SESSION['user_role'] === 'PATIENT'): ?>
                                    <a href="index.php?page=rendezvous&action=list" class="dropdown-item">
                                        <i class="fas fa-calendar-check"></i>
                                        <span>Mes rendez-vous</span>
                                    </a>
                                <?php endif; ?>
                                <div class="dropdown-divider"></div>
                                <a href="index.php?page=auth&action=logout" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Déconnexion</span>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="user-dropdown">
                            <button class="user-button guest-button">
                                <div class="user-avatar">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="12" fill="#fff"/>
                                        <circle cx="12" cy="10" r="4" fill="#3a6ea5"/>
                                        <ellipse cx="12" cy="17.5" rx="6" ry="3.5" fill="#3a6ea5"/>
                                    </svg>
                                </div>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="index.php?page=home" class="dropdown-item mobile-nav-item">
                                    <i class="fas fa-home"></i>
                                    <span>Accueil</span>
                                </a>
                                <a href="index.php?page=actus" class="dropdown-item mobile-nav-item">
                                    <i class="fas fa-newspaper"></i>
                                    <span>Actualités</span>
                                </a>
                                <a href="index.php?page=about" class="dropdown-item mobile-nav-item">
                                    <i class="fas fa-info-circle"></i>
                                    <span>À propos</span>
                                </a>
                                <div class="dropdown-divider mobile-nav-divider"></div>
                                <a href="index.php?page=auth&action=login" class="dropdown-item">
                                    <i class="fas fa-sign-in-alt"></i>
                                    <span>Connexion</span>
                                </a>
                                <a href="index.php?page=auth&action=register" class="dropdown-item">
                                    <i class="fas fa-user-plus"></i>
                                    <span>Inscription</span>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
            </div>
        </div>
    </header>