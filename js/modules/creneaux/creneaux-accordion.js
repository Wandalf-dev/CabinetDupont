document.addEventListener('DOMContentLoaded', function() {
    // Gestion des accordéons de jours
    const accordionDays = document.querySelectorAll('.accordion-date');
    
    accordionDays.forEach(function(accordion) {
        const button = accordion.querySelector('.accordion-button');
        const content = accordion.querySelector('.accordion-collapse');
        
        if (button && content) {
            content.style.display = 'none';
            
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Fermer les autres jours
                accordionDays.forEach(function(other) {
                    if (other !== accordion) {
                        const otherBtn = other.querySelector('.accordion-button');
                        const otherContent = other.querySelector('.accordion-collapse');
                        if (otherBtn && otherContent) {
                            otherBtn.classList.add('collapsed');
                            otherBtn.setAttribute('aria-expanded', 'false');
                            otherContent.style.display = 'none';
                        }
                    }
                });

                // Basculer l'état de l'accordéon actuel
                button.classList.toggle('collapsed');
                const isExpanded = !button.classList.contains('collapsed');
                button.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
                content.style.display = isExpanded ? 'block' : 'none';

                // Fermer toutes les périodes quand on ferme le jour
                if (!isExpanded) {
                    const allPeriodeButtons = content.querySelectorAll('.periode-button');
                    const allPeriodeContents = content.querySelectorAll('.periode-collapse');
                    allPeriodeButtons.forEach(btn => {
                        btn.classList.add('collapsed');
                        btn.setAttribute('aria-expanded', 'false');
                    });
                    allPeriodeContents.forEach(cont => cont.style.display = 'none');
                }
            });
        }
    });
    
    // Gestionnaire pour les périodes (matin/après-midi)
    const periodeSections = document.querySelectorAll('.periode-section');
    periodeSections.forEach(function(section) {
        const button = section.querySelector('.periode-button');
        const content = section.querySelector('.periode-collapse');
        
        if (button && content) {
            content.style.display = 'none';
            button.setAttribute('aria-expanded', 'false');
            
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Fermer les autres périodes dans le même jour
                const parentAccordion = section.closest('.accordion-collapse');
                if (parentAccordion) {
                    const siblingPeriodes = parentAccordion.querySelectorAll('.periode-section');
                    siblingPeriodes.forEach(function(otherPeriode) {
                        if (otherPeriode !== section) {
                            const otherButton = otherPeriode.querySelector('.periode-button');
                            const otherContent = otherPeriode.querySelector('.periode-collapse');
                            
                            if (otherButton && otherContent) {
                                otherButton.classList.add('collapsed');
                                otherButton.setAttribute('aria-expanded', 'false');
                                otherContent.style.display = 'none';
                            }
                        }
                    });
                }

                // Basculer l'état de la période actuelle
                button.classList.toggle('collapsed');
                const isExpanded = !button.classList.contains('collapsed');
                button.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
                content.style.display = isExpanded ? 'block' : 'none';
            });
        }
    });
});