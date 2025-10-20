/**
 * Améliorations UX pour l'administration mobile
 * Détection du scroll pour les onglets et autres optimisations
 */

document.addEventListener('DOMContentLoaded', function() {
    // Détection du scroll horizontal pour les onglets
    const tabsNav = document.querySelector('.tabs-nav');
    
    if (tabsNav) {
        function checkScrollability() {
            if (tabsNav.scrollWidth > tabsNav.clientWidth) {
                tabsNav.classList.add('has-scroll');
            } else {
                tabsNav.classList.remove('has-scroll');
            }
        }

        // Vérifier au chargement
        checkScrollability();

        // Vérifier lors du redimensionnement
        window.addEventListener('resize', checkScrollability);

        // Masquer l'indicateur après un scroll
        tabsNav.addEventListener('scroll', function() {
            if (this.scrollLeft > 0) {
                this.classList.remove('has-scroll');
            }
        });
    }

    // Animation d'apparition des cartes quand elles entrent dans le viewport
    if ('IntersectionObserver' in window) {
        const cards = document.querySelectorAll('.admin-table tr');
        
        const cardObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        cards.forEach(card => {
            cardObserver.observe(card);
        });
    }

    // Amélioration du feedback tactile
    const actionButtons = document.querySelectorAll('.admin-table .actions-cell .btn-admin');
    
    actionButtons.forEach(button => {
        button.addEventListener('touchstart', function() {
            this.style.opacity = '0.8';
        });
        
        button.addEventListener('touchend', function() {
            this.style.opacity = '1';
        });
        
        button.addEventListener('touchcancel', function() {
            this.style.opacity = '1';
        });
    });

    // Smooth scroll vers le haut après une action
    function scrollToTop(smooth = true) {
        if (smooth) {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        } else {
            window.scrollTo(0, 0);
        }
    }

    // Message de confirmation amélioré pour mobile
    const deleteButtons = document.querySelectorAll('.btn-admin.delete');
    
    deleteButtons.forEach(button => {
        const originalOnClick = button.getAttribute('onclick');
        
        if (originalOnClick && window.innerWidth <= 768) {
            button.addEventListener('click', function(e) {
                // Ajouter une petite vibration si disponible
                if ('vibrate' in navigator) {
                    navigator.vibrate(50);
                }
            });
        }
    });

    // Optimisation des performances sur mobile
    if (window.innerWidth <= 768) {
        // Désactiver les animations complexes si le device est lent
        if (navigator.hardwareConcurrency && navigator.hardwareConcurrency <= 2) {
            document.body.classList.add('reduce-animations');
        }

        // Lazy loading des images si présentes
        const images = document.querySelectorAll('img[data-src]');
        if ('IntersectionObserver' in window && images.length > 0) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        imageObserver.unobserve(img);
                    }
                });
            });

            images.forEach(img => imageObserver.observe(img));
        }
    }

    // Indicateur de chargement pour les actions AJAX
    const forms = document.querySelectorAll('.admin-table form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton && window.innerWidth <= 768) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>&nbsp;Traitement...';
            }
        });
    });

    // Gestion du pull-to-refresh (désactivation si non souhaité)
    if (window.innerWidth <= 768) {
        let startY = 0;
        
        document.addEventListener('touchstart', function(e) {
            startY = e.touches[0].pageY;
        }, { passive: true });
        
        document.addEventListener('touchmove', function(e) {
            const y = e.touches[0].pageY;
            // Désactiver le pull-to-refresh si on scroll depuis le haut
            if (window.scrollY === 0 && y > startY) {
                e.preventDefault();
            }
        }, { passive: false });
    }

    // ===== NOTE: Les cellules grip sont désormais cachées via CSS en mobile =====
    // Le CSS gère automatiquement l'affichage/masquage des cellules grip
    // Pas besoin de code JavaScript pour les supprimer du DOM

});

// Classe pour réduire les animations sur appareils lents
const style = document.createElement('style');
style.textContent = `
    .reduce-animations * {
        animation-duration: 0.1s !important;
        transition-duration: 0.1s !important;
    }
`;
document.head.appendChild(style);
