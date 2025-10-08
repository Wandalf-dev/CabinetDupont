function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (!section) return;

    const header = document.querySelector('header');
    const headerHeight = header ? header.offsetHeight : 0;
    
    // Calcul de la position avec une animation personnalisée
    const start = window.pageYOffset;
    const target = section.getBoundingClientRect().top + window.pageYOffset - headerHeight;
    const distance = target - start;
    const duration = 800;
    let startTime = null;

    function animation(currentTime) {
        if (startTime === null) startTime = currentTime;
        const progress = currentTime - startTime;
        const percent = Math.min(progress / duration, 1);
        
        // Fonction d'assouplissement
        const easing = t => t < 0.5 ? 4 * t * t * t : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1;
        
        window.scrollTo(0, start + distance * easing(percent));

        if (progress < duration) {
            requestAnimationFrame(animation);
        }
    }

    requestAnimationFrame(animation);
}

document.addEventListener('DOMContentLoaded', function() {
    // Empêche le scroll natif à l'arrivée sur la page
    if (window.location.hash) {
        window.scrollTo(0, 0);
        setTimeout(() => {
            const sectionId = window.location.hash.replace('#', '');
            scrollToSection(sectionId);
        }, 120);
    }

    // Gérer les clics sur les liens avec ancre
    document.querySelectorAll('a[href*="#"]').forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Vérifier si le lien pointe vers une ancre dans la page actuelle
            if (href.includes('#')) {
                const [path, hash] = href.split('#');
                const currentPath = window.location.pathname;
                
                // Si c'est un lien interne ou si on est déjà sur la bonne page
                if (!path || currentPath.endsWith(path)) {
                    e.preventDefault();
                    scrollToSection(hash);
                    history.pushState(null, null, '#' + hash);
                }
            }
        });
    });
    // Gérer le scroll lors d'un changement de hash (ex: navigation ou pushState)
    window.addEventListener('hashchange', function() {
        const hash = window.location.hash.replace('#', '');
        if (hash) {
            scrollToSection(hash);
        }
    });
});