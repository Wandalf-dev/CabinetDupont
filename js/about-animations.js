document.addEventListener('DOMContentLoaded', () => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.2 // L'animation se déclenche quand 20% de l'élément est visible
    });

    // Observer la timeline elle-même pour l'animation de la ligne verticale
    const timeline = document.querySelector('.timeline');
    if (timeline) {
        observer.observe(timeline);
    }

    // Observer chaque élément de la timeline
    const timelineItems = document.querySelectorAll('.timeline-item');
    timelineItems.forEach(item => {
        observer.observe(item);
    });
});