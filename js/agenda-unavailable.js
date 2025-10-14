document.addEventListener('DOMContentLoaded', function() {
    function markUnavailableSlots() {
        // Marquer la pause déjeuner
        const lunchBreakStart = 12;
        const lunchBreakEnd = 14;
        
        document.querySelectorAll('.day-column').forEach(dayColumn => {
            dayColumn.querySelectorAll('.slot-cell').forEach(slot => {
                const hour = parseInt(slot.getAttribute('data-hour').split(':')[0]);
                
                // Marquer les créneaux de pause déjeuner
                if (hour >= lunchBreakStart && hour < lunchBreakEnd) {
                    slot.classList.add('lunch-break');
                    
                    // Ajouter le texte "Pause déjeuner" uniquement à la première cellule
                    if (hour === lunchBreakStart && slot.getAttribute('data-hour').endsWith(':00')) {
                        slot.classList.add('lunch-break-start');
                    }
                }
            });
        });
    }

    // Exécuter après le chargement des créneaux
    markUnavailableSlots();

    // Réexécuter quand la vue est mise à jour (si nécessaire)
    document.addEventListener('calendarViewUpdated', markUnavailableSlots);
});