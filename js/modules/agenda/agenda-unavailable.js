document.addEventListener('DOMContentLoaded', function() {
    console.log('[Agenda] Initialisation des créneaux indisponibles...');

    // Fonction pour nettoyer les créneaux indisponibles
    window.resetUnavailableSlots = function() {
        console.log('[Agenda] Réinitialisation des créneaux indisponibles...');
        const activeView = document.querySelector('.week-view.active') ? '.week-view' : '.day-view';
        console.log('[Agenda] Réinitialisation dans la vue:', activeView);
        
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
        console.log('[Agenda] Marquage des pauses déjeuner...');
        // Réinitialiser d'abord
        resetUnavailableSlots();
        
        // Marquer la pause déjeuner
        const lunchBreakStart = 12;
        const lunchBreakEnd = 14;
        
        // Utiliser la vue active (semaine ou jour)
        const activeView = document.querySelector('.week-view.active') ? '.week-view' : '.day-view';
        console.log('[Agenda] Vue active:', activeView);
        
        document.querySelectorAll(`${activeView} .day-column`).forEach(dayColumn => {
            const date = dayColumn.getAttribute('data-date');
            console.log('[Agenda] Traitement de la colonne:', date);
            
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
        console.log('[Agenda] Chargement des créneaux indisponibles...', startDate, endDate);
        
        try {
            const response = await fetch(`index.php?page=agenda&action=getUnavailableSlots&start=${startDate}&end=${endDate}`);
            console.log('[Agenda] Réponse reçue:', response);
            const slots = await response.json();
            console.log('[Agenda] Créneaux indisponibles reçus:', slots);
            
            slots.forEach(slot => {
                const slotDate = new Date(slot.start);
                console.log('[Agenda] Traitement du créneau indisponible:', slotDate);
                
                // Formatage de la date pour correspondre à l'attribut data-date
                const dateStr = slotDate.toISOString().split('T')[0];
                console.log('[Agenda] Recherche de la colonne pour la date:', dateStr);
                
                // Rechercher dans la vue active (semaine ou jour)
                const activeView = document.querySelector('.week-view.active') ? '.week-view' : '.day-view';
                const dayColumn = document.querySelector(`${activeView} .day-column[data-date="${dateStr}"]`);
                console.log('[Agenda] Colonne trouvée dans', activeView, ':', dayColumn);
                
                if (dayColumn) {
                    const hour = slotDate.getHours();
                    const minutes = slotDate.getMinutes();
                    const timeStr = `${hour.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
                    console.log('[Agenda] Recherche du créneau horaire:', timeStr);
                    
                    const slotCell = dayColumn.querySelector(`.slot-cell[data-hour="${timeStr}"]`);
                    console.log('[Agenda] Cellule trouvée:', slotCell);
                    
                    if (slotCell) {
                        // NE PAS marquer comme indisponible si le créneau a déjà un rendez-vous
                        const hasAppointment = slotCell.classList.contains('reserved') || slotCell.querySelector('.appointment');
                        console.log('[Agenda] État du créneau:', {
                            time: timeStr,
                            hasReservedClass: slotCell.classList.contains('reserved'),
                            hasAppointmentElement: !!slotCell.querySelector('.appointment'),
                            hasAppointment: hasAppointment,
                            innerHTML: slotCell.innerHTML
                        });
                        
                        if (hasAppointment) {
                            console.log('[Agenda] ⚠️ Créneau ignoré car il contient un rendez-vous');
                            return;
                        }
                        
                        console.log('[Agenda] ✓ Marquage du créneau comme indisponible');
                        slotCell.classList.add('unavailable-slot');
                        slotCell.setAttribute('data-unavailable-text', 'Indisponibilité');
                    }
                }
            });
            // Fusion immédiate des créneaux indisponibles
            mergeUnavailableBlocks();
        } catch (error) {
            console.error('Erreur lors du chargement des créneaux indisponibles:', error);
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

    // Fonction pour obtenir la date de début de la semaine affichée
    function getDisplayedWeekStart() {
        const weekView = document.querySelector('.week-view');
        if (!weekView) return null;
        
        // Trouver la première colonne de jour
        const firstDayColumn = weekView.querySelector('.day-column');
        if (!firstDayColumn) return null;
        
        // Récupérer la date de cette colonne
        return firstDayColumn.getAttribute('data-date');
    }

    // Appel initial pour charger les créneaux indisponibles
    const startDateString = getDisplayedWeekStart();
    if (startDateString) {
        const endDate = new Date(startDateString);
        endDate.setDate(endDate.getDate() + 6); // Une semaine plus tard
        const endDateString = endDate.toISOString().split('T')[0];
        
        // Chargement initial des créneaux indisponibles
        loadUnavailableSlots(startDateString, endDateString);
    }

    // Répéter le chargement toutes les 10 minutes (600000 ms)
    setInterval(() => {
        const now = new Date();
        const nextEndDate = new Date();
        nextEndDate.setDate(now.getDate() + 6); // Une semaine plus tard
        loadUnavailableSlots(now.toISOString().split('T')[0], nextEndDate.toISOString().split('T')[0]);
    }, 600000);
});