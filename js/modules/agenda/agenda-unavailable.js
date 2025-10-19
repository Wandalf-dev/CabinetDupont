document.addEventListener('DOMContentLoaded', function() {

    // Fonction pour nettoyer les créneaux indisponibles
    window.resetUnavailableSlots = function() {
        const activeView = document.querySelector('.week-view.active') ? '.week-view' : '.day-view';
        
        // Supprimer tous les blocs fusionnés
        document.querySelectorAll(`${activeView} .merged-unavailable`).forEach(slot => {
            slot.remove();
        });
        
        // Réinitialiser tous les créneaux normaux
        document.querySelectorAll(`${activeView} .unavailable-slot, ${activeView} .slot-cell.unavailable`).forEach(slot => {
            slot.classList.remove('unavailable-slot', 'unavailable');
            slot.removeAttribute('data-unavailable-text');
            slot.style.visibility = '';
            slot.style.backgroundColor = '';
            slot.style.opacity = '';
        });
    }

    function markUnavailableSlots() {
        // Réinitialiser d'abord
        resetUnavailableSlots();
        
        // Marquer la pause déjeuner
        const lunchBreakStart = 12;
        const lunchBreakEnd = 14;
        
        // Utiliser la vue active (semaine ou jour)
        const activeView = document.querySelector('.week-view.active') ? '.week-view' : '.day-view';
        
        document.querySelectorAll(`${activeView} .day-column`).forEach(dayColumn => {
            const date = dayColumn.getAttribute('data-date');
            
            dayColumn.querySelectorAll('.slot-cell').forEach(slot => {
                const hour = parseInt(slot.getAttribute('data-hour').split(':')[0]);
                
                // Marquer les créneaux de pause déjeuner comme indisponibles
                if (hour >= lunchBreakStart && hour < lunchBreakEnd) {
                    slot.classList.add('unavailable-slot');
                }
            });
        });
    }

    // Fonction pour charger et afficher les créneaux indisponibles
    window.loadUnavailableSlots = async function(startDate, endDate) {
        
        try {
            const response = await fetch(`index.php?page=agenda&action=getUnavailableSlots&start=${startDate}&end=${endDate}`);
            const slots = await response.json();
            
            slots.forEach(slot => {
                const slotDate = new Date(slot.start);
                
                // Formatage de la date pour correspondre à l'attribut data-date
                const dateStr = slotDate.toISOString().split('T')[0];
                
                // Rechercher dans la vue active (semaine ou jour)
                const activeView = document.querySelector('.week-view.active') ? '.week-view' : '.day-view';
                const dayColumn = document.querySelector(`${activeView} .day-column[data-date="${dateStr}"]`);
                
                if (dayColumn) {
                    const hour = slotDate.getHours();
                    const minutes = slotDate.getMinutes();
                    const timeStr = `${hour.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
                    
                    const slotCell = dayColumn.querySelector(`.slot-cell[data-hour="${timeStr}"]`);
                    
                    if (slotCell) {
                        // NE PAS marquer comme indisponible si le créneau a déjà un rendez-vous
                        const hasAppointment = slotCell.classList.contains('reserved') || slotCell.querySelector('.appointment');
                        if (hasAppointment) {
                            return;
                        }
                        
                        slotCell.classList.add('unavailable-slot');
                        slotCell.setAttribute('data-unavailable-text', 'Indisponibilité');
                    }
                }
            });
            // Fusion immédiate des créneaux indisponibles
            mergeUnavailableBlocks();
        } catch (error) {
        }
    }

    // Fonction pour fusionner les indisponibilités consécutives
    function mergeUnavailableBlocks() {
        // Hauteur standard d'un créneau de 30 minutes
        const SLOT_HEIGHT = 25;
        
        requestAnimationFrame(() => {
            document.querySelectorAll('.day-column .day-content').forEach(dayContent => {
                // Supprimer les anciens blocs fusionnés
                dayContent.querySelectorAll('.merged-unavailable').forEach(el => el.remove());
                
                // Réinitialiser la visibilité de tous les créneaux
                dayContent.querySelectorAll('.slot-cell').forEach(slot => {
                    if (slot.classList.contains('unavailable-slot')) {
                        slot.style.visibility = 'hidden';
                    }
                });
                
                // Récupérer tous les slot-cell de la colonne
                let slots = Array.from(dayContent.querySelectorAll('.slot-cell'));
                let i = 0;
                
                while (i < slots.length) {
                    if (slots[i].classList.contains('unavailable-slot')) {
                        let start = i;
                        let end = i;
                        
                        // Trouver tous les créneaux indisponibles consécutifs
                        while (end + 1 < slots.length && slots[end + 1].classList.contains('unavailable-slot')) {
                            end++;
                        }
                        
                        // Créer un bloc pour tous les créneaux indisponibles
                        const nbSlots = end - start + 1;
                        const totalHeight = nbSlots * SLOT_HEIGHT;
                        
                        // Créer le bloc fusionné ou individuel
                        let mergedDiv = document.createElement('div');
                        mergedDiv.className = 'slot-cell unavailable-slot merged-unavailable';
                        mergedDiv.style.height = totalHeight + 'px';
                        mergedDiv.style.width = '100%';
                        mergedDiv.style.display = 'flex';
                        mergedDiv.style.alignItems = 'center';
                        mergedDiv.style.justifyContent = 'center';
                        mergedDiv.style.position = 'absolute';
                        mergedDiv.style.top = (start * SLOT_HEIGHT) + 'px';
                        mergedDiv.style.left = '0';
                        mergedDiv.style.backgroundColor = '#f0f0f0';
                        mergedDiv.style.color = '#666';
                        mergedDiv.style.zIndex = '1';
                        mergedDiv.textContent = 'Indisponible';
                        dayContent.appendChild(mergedDiv);

                        // Masquer les slots d'origine
                        for (let j = start; j <= end; j++) {
                            slots[j].style.visibility = 'hidden';
                        }
                        
                        i = end + 1;
                    } else {
                        i++;
                    }
                }
            });
        });
    }

    // Les créneaux indisponibles sont chargés automatiquement 
    // par loadCalendarView() dans agenda.js lors de chaque changement de vue
});