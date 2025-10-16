// Fonction d'animation de défilement
function animateScroll(destination, duration = 600) {
    const start = window.pageYOffset;
    const distance = destination - start;
    const startTime = performance.now();

    function step(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);

        // Fonction d'assouplissement (easeInOutQuad)
        const ease = t => t < .5 ? 2 * t * t : -1 + (4 - 2 * t) * t;
        
        window.scrollTo(0, start + distance * ease(progress));

        if (progress < 1) {
            requestAnimationFrame(step);
        }
    }

    requestAnimationFrame(step);
}

// Gestionnaire de défilement fluide
document.addEventListener('DOMContentLoaded', () => {
    // Attendre que le DOM soit complètement chargé
    setTimeout(() => {
        // Gérer les liens avec ancres
        document.querySelectorAll('a[href*="#"]').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                if (href.includes('#')) {
                    const [path, hash] = href.split('#');
                    const currentPath = window.location.pathname;
                    
                    if (!path || currentPath.endsWith(path)) {
                        e.preventDefault();
                        
                        const targetElement = document.getElementById(hash);
                        if (targetElement) {
                            const headerHeight = document.querySelector('header').offsetHeight || 0;
                            const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight;
                            
                            animateScroll(targetPosition);
                            
                            // Mise à jour de l'URL sans déclencher de scroll
                            history.pushState(null, null, '#' + hash);
                        }
                    }
                }
            });
        });

        // Gérer le hash initial dans l'URL
        if (window.location.hash) {
            const hash = window.location.hash.substring(1);
            const targetElement = document.getElementById(hash);
            
            if (targetElement) {
                // Réinitialiser d'abord le scroll
                window.scrollTo(0, 0);
                
                setTimeout(() => {
                    const headerHeight = document.querySelector('header').offsetHeight || 0;
                    const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight;
                    animateScroll(targetPosition);
                }, 100);
            }
        }
    }, 0);
});