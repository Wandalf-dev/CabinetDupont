document.addEventListener('DOMContentLoaded', function() {
    // Détection du mode responsive (mobile/tablette)
    const isMobile = () => window.innerWidth <= 768;
    
    // État de l'application - forcer vue jour sur mobile
    let currentDate = new Date();
    let currentView = isMobile() ? 'day' : 'week';

    // Écouter l'événement de mise à jour des rendez-vous
    document.addEventListener('appointmentUpdated', function(event) {
        // Réinitialiser les créneaux indisponibles avant de recharger
        if (window.resetUnavailableSlots) {
            window.resetUnavailableSlots();
        }
        
        // Recharger la vue et les rendez-vous
        updateView();
        loadAppointments();
        
        // Recharger les créneaux indisponibles
        const viewStartDate = currentView === 'week' ? getMonday(currentDate) : new Date(currentDate);
        const viewEndDate = new Date(viewStartDate);
        if (currentView === 'week') {
            viewEndDate.setDate(viewEndDate.getDate() + 6);
        }
        const startDate = formatDateISO(viewStartDate);
        const endDate = formatDateISO(viewEndDate);
        
        if (window.loadUnavailableSlots) {
            window.loadUnavailableSlots(startDate, endDate);
        }
    });

    // Configuration locale
    const locale = {
        days: {
            'Monday': 'Lundi',
            'Tuesday': 'Mardi',
            'Wednesday': 'Mercredi',
            'Thursday': 'Jeudi',
            'Friday': 'Vendredi',
            'Saturday': 'Samedi',
            'Sunday': 'Dimanche'
        },
        months: {
            0: 'janvier', 1: 'février', 2: 'mars', 3: 'avril',
            4: 'mai', 5: 'juin', 6: 'juillet', 7: 'août',
            8: 'septembre', 9: 'octobre', 10: 'novembre', 11: 'décembre'
        }
    };

    // Sélecteurs DOM
    const elements = {
        container: document.querySelector('.calendar-container'),
        weekView: document.querySelector('.week-view'),
        dayView: document.querySelector('.day-view'),
        viewButtons: document.querySelectorAll('.view-btn'),
        navButtons: document.querySelectorAll('.nav-btn'),
        currentPeriod: document.querySelector('.current-period'),
        timeSlots: document.querySelectorAll('.time-slot')
    };

    // Helper pour formater les dates avec le fuseau horaire
    function formatDateWithTimezone(date) {
        return date.toLocaleString('fr-FR', { 
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            timeZone: 'Europe/Paris'
        }).split('/').reverse().join('-');
    }

    // Helper pour formater une date en format ISO (YYYY-MM-DD)
    function formatDateISO(date) {
        return date.getFullYear() + '-' + 
               String(date.getMonth() + 1).padStart(2, '0') + '-' + 
               String(date.getDate()).padStart(2, '0');
    }

    // Initialisation
    function init() {
        // Forcer la vue jour sur mobile dès le chargement
        if (isMobile()) {
            currentView = 'day';
            elements.weekView.classList.remove('active');
            elements.dayView.classList.add('active');
            elements.viewButtons.forEach(btn => {
                btn.classList.toggle('active', btn.dataset.view === 'day');
            });
        }
        
        attachEventListeners();
        updateView();
        updateNavigation();
        // Déclencher le chargement des indisponibilités après l'initialisation
        const startDate = formatDateISO(getMonday(currentDate));
        const endDate = new Date(startDate);
        endDate.setDate(endDate.getDate() + 6);
        if (window.loadUnavailableSlots) {
            window.loadUnavailableSlots(startDate, formatDateISO(endDate));
        }
    }

    // Gestionnaires d'événements
    function attachEventListeners() {
        // Boutons de vue
        elements.viewButtons.forEach(button => {
            button.addEventListener('click', () => {
                const view = button.dataset.view;
                switchView(view);
            });
        });

        // Boutons de navigation
        elements.navButtons.forEach(button => {
            button.addEventListener('click', () => {
                navigateCalendar(button.classList.contains('prev') ? -1 : 1);
            });
        });

        // Créneaux horaires
        document.addEventListener('click', function(e) {
            const slot = e.target.closest('.time-slot');
            if (slot && !slot.closest('.time-column')) {
                handleTimeSlotClick(e);
            }
        });

        // Gestion du resize : forcer vue jour sur mobile
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                if (isMobile() && currentView === 'week') {
                    switchView('day');
                }
            }, 250);
        });
    }

    // Fonctions de gestion des vues
    function switchView(view) {
        // Empêcher la vue semaine sur mobile
        if (isMobile() && view === 'week') {
            console.warn('[Agenda] Vue semaine désactivée sur mobile');
            return;
        }
        
        currentView = view;
        elements.viewButtons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.view === view);
        });

        elements.weekView.classList.toggle('active', view === 'week');
        elements.dayView.classList.toggle('active', view === 'day');

        // Reset à la date actuelle si on passe en vue jour
        if (view === 'day') {
            currentDate = new Date();
        }

        updateView();
        updateNavigation();
    }

    // Navigation
    function navigateCalendar(direction) {
        if (currentView === 'week') {
            const monday = getMonday(currentDate);
            monday.setDate(monday.getDate() + (direction * 7));
            currentDate = monday;
        } else {
            currentDate.setDate(currentDate.getDate() + direction);
        }
        // Réinitialiser les créneaux indisponibles avant de mettre à jour la vue
        if (window.resetUnavailableSlots) {
            window.resetUnavailableSlots();
        }
        updateView();
        updateNavigation();
        
        // Recharger les créneaux indisponibles pour la nouvelle période
        if (window.loadUnavailableSlots) {
            const startDate = currentView === 'week' ? 
                formatDateISO(getMonday(currentDate)) : 
                formatDateISO(currentDate);
            const endDate = new Date(startDate);
            if (currentView === 'week') {
                endDate.setDate(endDate.getDate() + 6);
            }
            window.loadUnavailableSlots(startDate, formatDateISO(endDate));
        }
    }

    // Fonction pour nettoyer complètement la vue semaine
    function resetWeekView() {
        const weekView = document.querySelector('.week-view');
        if (!weekView) return;
        
        // Supprimer tous les rendez-vous
        weekView.querySelectorAll('.appointment').forEach(el => el.remove());
        
        // Supprimer tous les marqueurs indisponibles
        weekView.querySelectorAll('.unavailable-marker').forEach(el => el.remove());
        
        // Nettoyer tous les slots
        const slots = weekView.querySelectorAll('.slot-cell');
        slots.forEach(slot => {
            slot.classList.remove('reserved', 'unavailable');
            slot.innerHTML = '';
            slot.style.backgroundColor = '';
            slot.removeAttribute('title');
        });
    }

    // Mise à jour de l'affichage
    async function updateView() {
        // Nettoyer complètement la vue avant de recharger
        if (currentView === 'week') {
            resetWeekView();
            updateWeekView();
        } else {
            updateDayView();
            // Mettre à jour l'attribut data-date pour la vue jour
            const dayColumn = document.querySelector('.day-view .day-column');
            if (dayColumn) {
                dayColumn.setAttribute('data-date', formatDateISO(currentDate));
                const dayName = locale.days[currentDate.toLocaleString('en-US', { weekday: 'long' })];
                const formattedDate = currentDate.getDate() + ' ' + locale.months[currentDate.getMonth()];
                dayColumn.querySelector('.day-name').textContent = dayName;
                dayColumn.querySelector('.day-date').textContent = formattedDate;
            }
        }
        
        // Charger les rendez-vous en premier
        await loadAppointments();
        
        // Attendre que le DOM soit mis à jour
        await new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve)));
        
        // Calculer la période à afficher
        const viewStartDate = currentView === 'week' ? getMonday(currentDate) : new Date(currentDate);
        const viewEndDate = new Date(viewStartDate);
        if (currentView === 'week') {
            viewEndDate.setDate(viewEndDate.getDate() + 6);
        }
        
        const startDateStr = formatDateISO(viewStartDate);
        const endDateStr = formatDateISO(viewEndDate);
        
        // Ensuite charger les créneaux indisponibles (qui ne doivent pas écraser les rendez-vous)
        if (window.loadUnavailableSlots) {
            await window.loadUnavailableSlots(startDateStr, endDateStr);
        }
        
        console.log('[Agenda] Déclenchement de calendarViewUpdated:', {
            startDate: startDateStr,
            endDate: endDateStr,
            view: currentView
        });
        
        document.dispatchEvent(new CustomEvent('calendarViewUpdated', {
            detail: {
                startDate: startDateStr,
                endDate: endDateStr,
                view: currentView
            }
        }));
    }

    // Charger les rendez-vous
    async function loadAppointments() {
        const viewStartDate = currentView === 'week' ? getMonday(currentDate) : new Date(currentDate);
        const viewEndDate = new Date(viewStartDate);
        if (currentView === 'week') {
            viewEndDate.setDate(viewEndDate.getDate() + 6);
        }

        const start = formatDateISO(viewStartDate);
        const end = formatDateISO(viewEndDate);

        // Réinitialiser les créneaux de la vue active (sans supprimer les éléments)
        const activeView = currentView === 'week' ? '.week-view' : '.day-view';
        
        // Supprimer tous les rendez-vous affichés (qui sont en position absolute)
        const appointments = document.querySelectorAll(`${activeView} .day-content .slot-cell.reserved`);
        appointments.forEach(apt => {
            if (apt.style.position === 'absolute') {
                apt.remove();
            }
        });
        
        // Réinitialiser les créneaux de base
        const slots = document.querySelectorAll(`${activeView} .slot-cell:not(.reserved)`);
        slots.forEach(slot => {
            slot.classList.remove('reserved');
            slot.removeAttribute('title');
            slot.style.backgroundColor = '';
        });

        console.log('[Agenda] Chargement des rendez-vous pour la période:', { 
            start, 
            end, 
            view: currentView,
            activeView: activeView
        });

        try {
            const response = await fetch(`index.php?page=agenda&action=getAppointments&start=${start}&end=${end}`);
            const events = await response.json();
                console.log('Rendez-vous reçus (données brutes):', events);
                events.forEach(event => {
                    // Vérification des dates brutes
                    console.log('Données brutes du rendez-vous:', {
                        start: event.start,
                        end: event.end,
                        duree: event.duree,
                        raw_start: new Date(event.start),
                        raw_end: new Date(event.end)
                    });

                    const startTime = new Date(event.start);
                    const endTime = new Date(startTime);
                    // Si la durée est spécifiée, l'utiliser pour calculer l'heure de fin
                    if (event.duree) {
                        endTime.setMinutes(endTime.getMinutes() + event.duree);
                    } else {
                        endTime.setTime(new Date(event.end).getTime());
                    }
                    const slotDate = formatDateISO(startTime);

                    console.log('Traitement du rendez-vous:', {
                        date: slotDate,
                        start: event.start,
                        startObj: startTime,
                        end: event.end,
                        endObj: endTime,
                        duration: (endTime - startTime) / (60 * 1000), // Durée en minutes
                        title: event.title,
                        view: currentView
                    });

                        // Vérifier si le rendez-vous est dans la période affichée
                    const slotDateTime = new Date(slotDate);
                    if (currentView === 'week' || 
                        (currentView === 'day' && formatDateISO(slotDateTime) === formatDateISO(currentDate))) {
                        
                        const dayName = startTime.toLocaleString('en-US', { weekday: 'long' }).toLowerCase();
                        console.log('Recherche de la colonne pour:', {
                            date: formatDateISO(startTime),
                            dayName: dayName,
                            selector: `${activeView} .day-column[data-date="${formatDateISO(startTime)}"]`
                        });

                        // Trouver la colonne du jour correspondant selon la vue
                        let dayColumn;
                        if (currentView === 'week') {
                            dayColumn = document.querySelector(`${activeView} .day-column[data-date="${formatDateISO(startTime)}"]`);
                            if (!dayColumn) {
                                dayColumn = document.querySelector(`${activeView} .day-column[data-day="${dayName}"]`);
                            }
                        } else {
                            // Pour la vue jour, utiliser la seule colonne disponible si la date correspond
                            if (formatDateISO(startTime) === formatDateISO(currentDate)) {
                                dayColumn = document.querySelector('.day-view .day-column');
                            }
                        }

                        if (!dayColumn) {
                            console.log('Colonne non trouvée pour:', {
                                view: currentView,
                                date: formatDateISO(startTime),
                                currentDate: formatDateISO(currentDate),
                                dayName: dayName
                            });
                            return;
                        }

                        // Calculer la position et la hauteur
                        const dayStartHour = 8; // Le calendrier commence à 8h
                        const startMinutes = (startTime.getHours() - dayStartHour) * 60 + startTime.getMinutes();
                        const durationMinutes = (endTime - startTime) / (60 * 1000);
                        const slotHeight = 25; // hauteur d'un créneau de 30min en pixels
                        
                        // Créer un nouvel élément pour le rendez-vous
                        const appointmentElement = document.createElement('div');
                        appointmentElement.classList.add('slot-cell', 'reserved');
                        
                        // Ajouter les données du rendez-vous pour la modification
                        const appointmentData = {
                            id: event.id,
                            patient: event.patient.prenom + ' ' + event.patient.nom,
                            service: event.service.titre,
                            duration: event.service.duree || durationMinutes,
                            date: formatDateISO(startTime),
                            time: startTime.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }),
                            status: event.status
                        };
                        appointmentElement.setAttribute('data-appointment', JSON.stringify(appointmentData));
                        appointmentElement.setAttribute('data-id', event.id);
                        
                        // Calculer la hauteur exacte basée sur la durée
                        const exactHeight = (durationMinutes / 30) * slotHeight;
                        const exactTop = (startMinutes / 30) * slotHeight;
                        
                        // Log pour déboguer
                        console.log('Données du rendez-vous:', event);

                        // Créer le contenu détaillé pour la vue jour
                        // Appliquer le style avec précision
                        appointmentElement.style.position = 'absolute';
                        appointmentElement.style.top = `${exactTop}px`;
                        appointmentElement.style.height = `${exactHeight}px`;
                        appointmentElement.style.left = '2px';
                        appointmentElement.style.right = '2px';
                        appointmentElement.style.width = 'calc(100% - 4px)';
                        appointmentElement.style.backgroundColor = event.couleur || 'var(--calendar-primary)';
                        
                        // Ajouter les informations du rendez-vous
                        appointmentElement.setAttribute('data-id', event.id);
                        appointmentElement.setAttribute('data-tooltip', `${event.title}\nDe ${formatTime(startTime)} à ${formatTime(endTime)}\nDurée : ${durationMinutes} minutes`);
                        
                        // Préparation du contenu commun aux deux vues
                        let titleDisplay = event.title;
                        let durationText = durationMinutes + 'min';
                        
                        // Pour les rendez-vous courts, tronquer après le premier espace si trop long
                        const parts = event.title.split(' ');
                        if (parts.length > 2) {
                            titleDisplay = parts[0] + ' ' + parts[1];
                        }
                        
                        // Créer l'indicateur de statut si nécessaire
                        let statusIcon = '';
                        if (event.status === 'HONORE') {
                            statusIcon = '<span class="status-indicator status-honore" title="Rendez-vous honoré"><svg width="16" height="16" viewBox="0 0 64 64" fill="white" xmlns="http://www.w3.org/2000/svg"><path d="m53.336 20.208-27.353 29.285a1 1 0 0 1 -1.525-.075l-13.858-18.118a1 1 0 0 1 .187-1.4l5.045-3.859a1 1 0 0 1 1.4.187l7.862 10.272a1 1 0 0 0 1.525.075l20.613-22.068a1 1 0 0 1 1.413-.049l4.642 4.342a1 1 0 0 1 .049 1.408z"/></svg></span>';
                        } else if (event.status === 'ABSENT') {
                            statusIcon = '<span class="status-indicator status-absent" title="Patient absent"><svg width="16" height="16" viewBox="0 0 512 512" fill="white" xmlns="http://www.w3.org/2000/svg"><path d="m409.5 440.3c0 11.1-9 20.1-20.1 20.1h-369.3c-11.1.1-20.1-8.9-20.1-20 0-78.9 64.2-143.2 143.1-143.2h123.2c79 0 143.2 64.2 143.2 143.1z"/><path d="m318.4 165.2c0 62.8-50.9 113.6-113.6 113.6s-113.7-50.9-113.7-113.6 50.9-113.6 113.6-113.6c62.8 0 113.6 50.8 113.7 113.6z"/><path d="m470.8 224 35.3-35.3c7.7-8 7.5-20.7-.5-28.4-7.8-7.5-20.2-7.5-28 0l-35.3 35.3-35.3-35.3c-7.9-7.9-20.6-7.9-28.5 0s-7.9 20.6 0 28.5l35.3 35.3-35.3 35.3c-7.9 7.9-7.9 20.6 0 28.5s20.6 7.9 28.5 0l35.3-35.3 35.3 35.3c8 7.7 20.7 7.5 28.5-.5 7.5-7.8 7.5-20.2 0-28z"/></svg></span>';
                        } else if (event.status === 'CONFIRME') {
                            statusIcon = '<span class="status-indicator status-confirme" title="Rendez-vous confirmé"><svg width="16" height="16" viewBox="0 0 512 512" fill="white" xmlns="http://www.w3.org/2000/svg"><path d="m256 486.075c-126.864 0-230.076-103.213-230.076-230.08 0-126.861 103.212-230.07 230.076-230.07s230.076 103.209 230.076 230.07c0 126.866-103.212 230.08-230.076 230.08zm0-428.15c-109.22 0-198.076 88.854-198.076 198.07 0 109.222 88.856 198.08 198.076 198.08s198.076-88.858 198.076-198.08c0-109.216-88.856-198.07-198.076-198.07z"/><path d="m333.12 332.547c-2.838 0-5.711-.755-8.312-2.34l-77.135-47.01c-4.766-2.904-7.673-8.082-7.673-13.663v-113.59c0-8.836 7.164-16 16-16s16 7.164 16 16v104.604l69.461 42.333c7.546 4.599 9.935 14.444 5.336 21.989-3.013 4.946-8.281 7.677-13.677 7.677z"/></svg></span>';
                        }
                        
                        // Format uniforme pour les deux vues
                        appointmentElement.innerHTML = `
                            <span class="rdv-content">${formatTime(startTime)} - ${formatTime(endTime)} ${titleDisplay} ${durationText}</span>
                            ${statusIcon}
                        `;

                        // Gérer l'affichage de l'infobulle
                        let tooltipTimeout;
                        
                        appointmentElement.addEventListener('mouseenter', function(e) {
                            // Supprimer toute infobulle existante
                            const existingTooltip = document.querySelector('.custom-tooltip');
                            if (existingTooltip) {
                                existingTooltip.remove();
                            }
                            
                            // Annuler tout timeout en cours
                            if (tooltipTimeout) {
                                clearTimeout(tooltipTimeout);
                            }
                            
                            const tooltip = document.createElement('div');
                            tooltip.className = 'custom-tooltip';
                            tooltip.textContent = this.getAttribute('data-tooltip');
                            document.body.appendChild(tooltip);

                            const rect = this.getBoundingClientRect();
                            const tooltipRect = tooltip.getBoundingClientRect();
                            
                            // Positionner l'infobulle au-dessus de l'élément
                            tooltip.style.left = `${rect.left + (rect.width / 2) - (tooltipRect.width / 2)}px`;
                            tooltip.style.top = `${rect.top - tooltipRect.height - 5}px`;
                            
                            requestAnimationFrame(() => tooltip.classList.add('show'));
                        });

                        appointmentElement.addEventListener('mouseleave', function() {
                            const tooltip = document.querySelector('.custom-tooltip');
                            if (tooltip) {
                                tooltip.classList.remove('show');
                                tooltipTimeout = setTimeout(() => {
                                    if (tooltip && tooltip.parentNode) {
                                        tooltip.remove();
                                    }
                                }, 200);
                            }
                        });

                        // Ajouter l'élément à la colonne du jour
                        dayColumn.querySelector('.day-content').appendChild(appointmentElement);
                    }
                });
        } catch (error) {
            console.error('Erreur lors du chargement des rendez-vous:', error);
        }
    }

    function updateWeekView() {
        const monday = getMonday(currentDate);
        const dayElements = document.querySelectorAll('.week-view .day-column');
        
        dayElements.forEach((dayElement, index) => {
            const date = new Date(monday);
            date.setDate(date.getDate() + index);
            
            const header = dayElement.querySelector('.day-header');
            if (header) {
                const dayName = date.toLocaleString('en-US', { weekday: 'long' });
                header.querySelector('.day-name').textContent = locale.days[dayName];
                header.querySelector('.day-date').textContent = formatDate(date);
            }
            
            // Mettre à jour les attributs data du jour
            dayElement.setAttribute('data-day', date.toLocaleString('en-US', { weekday: 'long' }).toLowerCase());
            dayElement.setAttribute('data-date', formatDateISO(date));
        });

        console.log('Colonnes de la semaine mises à jour:', [...dayElements].map(col => ({
            dataDay: col.getAttribute('data-day'),
            dataDate: col.getAttribute('data-date')
        })));

        // Mise à jour des attributs data pour les créneaux
        updateTimeSlots(monday, dayElements);
    }

    function updateDayView() {
        const dayElement = document.querySelector('.day-view .day-column');
        if (dayElement) {
            // S'assurer que la colonne a une div .day-content
            if (!dayElement.querySelector('.day-content')) {
                const dayContent = document.createElement('div');
                dayContent.className = 'day-content';
                dayElement.appendChild(dayContent);
            }

            // Mise à jour des attributs data du jour
            const dateStr = formatDateISO(currentDate);
            dayElement.setAttribute('data-date', dateStr);
            dayElement.setAttribute('data-day', currentDate.toLocaleString('en-US', { weekday: 'long' }).toLowerCase());

            // Mise à jour de l'en-tête
            const header = dayElement.querySelector('.day-header');
            if (header) {
                const dayName = locale.days[currentDate.toLocaleString('en-US', { weekday: 'long' })];
                header.querySelector('.day-name').textContent = dayName;
                header.querySelector('.day-date').textContent = formatDate(currentDate);
            }
            
            console.log('Mise à jour de la vue jour:', {
                date: dateStr,
                dataDay: dayElement.getAttribute('data-day'),
                dataDate: dayElement.getAttribute('data-date'),
                hasContent: !!dayElement.querySelector('.day-content'),
                currentView: currentView
            });

            // Récupérer tous les créneaux de la vue jour
            const slots = dayElement.querySelectorAll('.day-content .slot-cell');
            slots.forEach(slot => {
                const hour = slot.dataset.hour;
                slot.dataset.date = dateStr;
                console.log('Configuration du créneau:', {
                    date: dateStr,
                    hour: hour,
                    selector: `.day-view .slot-cell[data-date="${dateStr}"][data-hour="${hour}"]`
                });
            });

            // Recharger les rendez-vous après avoir mis à jour tous les créneaux
            loadAppointments();
        } else {
            console.error('Élément .day-view .day-column non trouvé dans le DOM');
        }
    }

    function updateTimeSlots(startDate, dayElements) {
        dayElements.forEach((dayElement, index) => {
            const date = new Date(startDate);
            date.setDate(date.getDate() + index);
            const dateStr = formatDateISO(date);
            
            const slots = dayElement.querySelectorAll('.day-content .slot-cell');
            slots.forEach(slot => {
                const hour = slot.dataset.hour;
                slot.dataset.date = dateStr;
                console.log('Mise à jour du créneau:', {
                    date: dateStr,
                    hour: hour,
                    view: currentView,
                    element: slot
                });
            });
        });
    }

    function updateNavigation() {
        const periodText = currentView === 'week'
            ? getWeekPeriodText()
            : getDayPeriodText();
        elements.currentPeriod.textContent = periodText;
    }

    // Fonctions utilitaires
    function getMonday(date) {
        const d = new Date(date);
        d.setDate(d.getDate() - d.getDay() + (d.getDay() === 0 ? -6 : 1));
        return d;
    }

    function formatDate(date) {
        return `${date.getDate()} ${locale.months[date.getMonth()]}`;
    }

    function formatDateISO(date) {
        const d = new Date(date);
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function formatTime(date) {
        const d = new Date(date);
        return d.toLocaleTimeString('fr-FR', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function getWeekPeriodText() {
        const monday = getMonday(currentDate);
        const sunday = new Date(monday);
        sunday.setDate(sunday.getDate() + 6);
        return `${formatDate(monday)} - ${formatDate(sunday)}`;
    }

    function getDayPeriodText() {
        return `${locale.days[currentDate.toLocaleString('en-US', { weekday: 'long' })]} ${formatDate(currentDate)}`;
    }

    function handleTimeSlotClick(event) {
        const slot = event.target.closest('.time-slot');
        const hour = slot.dataset.hour;
        const date = slot.dataset.date;
        console.log(`Créneau sélectionné: ${hour}h le ${date}`);
        
        // Ici vous pouvez ajouter la logique pour ouvrir la modal ou gérer le clic
    }

    // Fonction utilitaire pour récupérer les horaires du cabinet
    let horairesCabinet = null;
    function fetchHorairesCabinet() {
        console.log('[fetchHorairesCabinet] Début du chargement...');
        return fetch('index.php?page=agenda&action=getCabinetHoraires')
            .then(response => {
                console.log('[fetchHorairesCabinet] Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('[fetchHorairesCabinet] Data reçue:', data);
                if (data.success) {
                    horairesCabinet = data.horaires;
                    console.log('[fetchHorairesCabinet] horairesCabinet chargé:', horairesCabinet);
                } else {
                    console.error('[fetchHorairesCabinet] Erreur dans la réponse:', data);
                }
            })
            .catch(error => {
                console.error('[fetchHorairesCabinet] Erreur chargement horaires:', error);
            });
    }

    // Fonction pour griser les créneaux hors plages horaires
    function markOutOfHoursSlots() {
        console.log('[markOutOfHoursSlots] Début, horairesCabinet:', horairesCabinet);
        
        if (!horairesCabinet) {
            console.warn('[markOutOfHoursSlots] horairesCabinet est null');
            return;
        }
        
        // Pour chaque colonne de jour
        const dayColumns = document.querySelectorAll('.day-column');
        console.log('[markOutOfHoursSlots] Nombre de colonnes:', dayColumns.length);
        
        dayColumns.forEach(dayCol => {
            const dateStr = dayCol.getAttribute('data-date');
            if (!dateStr) {
                console.warn('[markOutOfHoursSlots] Colonne sans data-date');
                return;
            }
            
            const dateObj = new Date(dateStr);
            const jsDay = dateObj.getDay(); // 0=dimanche, 1=lundi, ...
            const jours = ['dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi'];
            const jour = jours[jsDay];
            
            console.log(`[markOutOfHoursSlots] Traitement du ${jour} (${dateStr})`);
            
            // Chercher les horaires pour ce jour
            const horaires = horairesCabinet.find(h => h.jour === jour);
            
            // Pour chaque créneau
            const slots = dayCol.querySelectorAll('.slot-cell');
            console.log(`[markOutOfHoursSlots] ${slots.length} créneaux à traiter`);
            
            slots.forEach(slot => {
                const hourStr = slot.getAttribute('data-hour');
                if (!hourStr) return;
                
                // Si le créneau est déjà indisponible ou réservé, on ne touche pas
                if (slot.classList.contains('unavailable-slot') || slot.classList.contains('reserved')) return;
                
                const hourNum = parseInt(hourStr.split(':')[0], 10);
                const minuteNum = parseInt(hourStr.split(':')[1], 10);
                
                // Exception : ne jamais griser après 20h
                if (hourNum >= 20) {
                    slot.classList.remove('out-of-hours-slot');
                    return;
                }
                
                // Si pas d'horaires pour ce jour, griser tout
                if (!horaires) {
                    slot.classList.add('out-of-hours-slot');
                    return;
                }
                
                // Récupérer les plages horaires (format HH:MM:SS)
                const matinStart = horaires.ouverture_matin;
                const matinEnd = horaires.fermeture_matin;
                const apremStart = horaires.ouverture_apresmidi;
                const apremEnd = horaires.fermeture_apresmidi;
                
                // Vérifier si le créneau est DANS une plage d'ouverture
                let isOpen = false;
                
                // Vérifier plage matin (si définie et non 00:00:00)
                if (matinStart && matinEnd && matinStart !== '00:00:00' && matinEnd !== '00:00:00') {
                    const matinStartStr = matinStart.substr(0, 5); // HH:MM
                    const matinEndStr = matinEnd.substr(0, 5);
                    if (hourStr >= matinStartStr && hourStr < matinEndStr) {
                        isOpen = true;
                    }
                }
                
                // Vérifier plage après-midi (si définie et non 00:00:00)
                if (apremStart && apremEnd && apremStart !== '00:00:00' && apremEnd !== '00:00:00') {
                    const apremStartStr = apremStart.substr(0, 5); // HH:MM
                    const apremEndStr = apremEnd.substr(0, 5);
                    if (hourStr >= apremStartStr && hourStr < apremEndStr) {
                        isOpen = true;
                    }
                }
                
                // Griser si FERMÉ (hors plages d'ouverture)
                if (!isOpen) {
                    slot.classList.add('out-of-hours-slot');
                    if (hourStr >= '12:00' && hourStr < '14:00') {
                        console.log(`[markOutOfHoursSlots] Grisage créneau pause déjeuner: ${hourStr}`);
                    }
                } else {
                    slot.classList.remove('out-of-hours-slot');
                }
            });
        });
        
        console.log('[markOutOfHoursSlots] Fin du traitement');
    }

    // Charger les horaires cabinet au démarrage
    fetchHorairesCabinet().then(() => {
        markOutOfHoursSlots();
    });

    // Re-marquer à chaque mise à jour de la vue
    document.addEventListener('calendarViewUpdated', () => {
        markOutOfHoursSlots();
    });

    // Démarrage de l'application
    init();
});