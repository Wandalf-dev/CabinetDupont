<footer class="site-footer" role="contentinfo">
        <div class="container">
            <!-- Bandeau haut : logo, slogan et réseaux sociaux -->
            <div class="footer-top">
                <a href="index.php" class="footer-brand">
                    <!-- Logo du cabinet affiché en haut du footer -->
                    <img src="<?php echo BASE_URL; ?>/assets/dupontcare-logo-horizontal-DUPONT-white.svg" alt="DupontCare" />
                </a>
                <p class="footer-tagline">
                    <!-- Slogan du cabinet -->
                    Soins modernes, écoute humaine — votre sourire, notre priorité.
                </p>
                
                <!-- Liste des réseaux sociaux avec icônes SVG -->
                <ul class="footer-social" aria-label="Réseaux sociaux">
                    <li>
                        <a href="#" aria-label="Instagram" target="_blank" rel="noopener noreferrer">
                            <!-- Icône Instagram SVG -->
                            <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill="currentColor" d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5m0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3zm11 2a1 1 0 1 1 0 2 1 1 0 0 1 0-2M12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10m0 2.2A2.8 2.8 0 1 0 14.8 12 2.8 2.8 0 0 0 12 9.2"/>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="#" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
                            <!-- Icône Facebook SVG -->
                            <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill="currentColor" d="M13 22v-8h3l.5-4H13V8.5c0-1.1.3-1.8 1.9-1.8H17V3.2C16.6 3.1 15.5 3 14.2 3 11.4 3 9.5 4.6 9.5 7.7V10H6v4h3.5v8z"/>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="#" aria-label="LinkedIn" target="_blank" rel="noopener noreferrer">
                            <!-- Icône LinkedIn SVG -->
                            <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill="currentColor" d="M6.94 8.5V21H3V8.5zM4.97 3a2.2 2.2 0 1 1 0 4.4 2.2 2.2 0 0 1 0-4.4M21 21h-3.93v-6.1c0-1.45-.03-3.31-2.02-3.31-2.03 0-2.34 1.58-2.34 3.21V21H8.78V8.5h3.77v1.7h.05c.53-1 1.82-2.05 3.74-2.05 4 0 4.74 2.63 4.74 6.06z"/>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Barre de bas de page : droits et liens légaux -->
            <div class="footer-bottom">
                <p>
                    <!-- Copyright dynamique -->
                    &copy; <?php echo date('Y'); ?> DupontCare - Tous droits réservés
                </p>
                <ul class="footer-legal">
                    <!-- Liens vers les pages légales du site -->
                    <li><a href="index.php?page=mentions-legales">Mentions légales</a></li>
                    <li><a href="index.php?page=confidentialite">Politique de confidentialité</a></li>
                    <li><a href="index.php?page=accessibilite">Accessibilité</a></li>
                </ul>
            </div>
        </div>
    </footer>
    
    <!-- Scripts globaux du site (JS principal) -->
    <!-- <script src="<?php echo BASE_URL; ?>/js/utils/main.js"></script> -->
</body>
</html>