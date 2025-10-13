document.addEventListener('DOMContentLoaded', function() {
    // Gestion de l'accordéon pour les créneaux
    document.querySelectorAll('.accordion-button').forEach(button => {
        button.addEventListener('click', function() {
            // Récupérer l'élément parent accordion-item
            const accordionItem = this.closest('.accordion-item');
            
            // Toggle la classe active sur l'élément cliqué
            accordionItem.classList.toggle('active');

            // Fermer les autres sections si elles sont ouvertes
            document.querySelectorAll('.accordion-item').forEach(item => {
                if (item !== accordionItem) {
                    item.classList.remove('active');
                }
            });
        });
    });

    // Ouvrir automatiquement le premier accordéon au chargement
    const firstAccordion = document.querySelector('.accordion-item');
    if (firstAccordion) {
        firstAccordion.classList.add('active');
    }
});