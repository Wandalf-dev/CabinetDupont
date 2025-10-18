document.addEventListener('DOMContentLoaded', function() {
    const userButtons = document.querySelectorAll('.user-button');
    const userDropdowns = document.querySelectorAll('.user-dropdown');
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mainNav = document.querySelector('.main-nav');

    // Gestionnaire pour tous les boutons utilisateur (connecté ou invité)
    userButtons.forEach((button, index) => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = userDropdowns[index];
            if (dropdown) {
                dropdown.classList.toggle('active');
            }
        });
    });

    // Gestionnaire pour le menu burger
    if (mobileMenuToggle && mainNav) {
        mobileMenuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('active');
            mainNav.classList.toggle('active');
        });
    }

    // Fermer les menus quand on clique ailleurs
    document.addEventListener('click', function(e) {
        // Fermer tous les dropdowns utilisateur
        userDropdowns.forEach(dropdown => {
            if (dropdown && !dropdown.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });

        // Fermer le menu mobile
        if (mobileMenuToggle && mainNav && !mobileMenuToggle.contains(e.target)) {
            if (!mainNav.contains(e.target)) {
                mainNav.classList.remove('active');
                mobileMenuToggle.classList.remove('active');
            }
        }
    });

    // Fermer le menu mobile quand on clique sur un lien
    if (mainNav) {
        const navItems = mainNav.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', function() {
                mainNav.classList.remove('active');
                if (mobileMenuToggle) {
                    mobileMenuToggle.classList.remove('active');
                }
            });
        });
    }
});