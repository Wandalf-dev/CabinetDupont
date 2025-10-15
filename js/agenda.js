document.addEventListener('DOMContentLoaded', function() {
    // État de l'application
    let currentDate = new Date();
    let currentView = 'week';

    // Écouter l'événement de mise à jour des rendez-vous
    document.addEventListener('appointmentUpdated', function(event) {
        // Recharger la vue et les rendez-vous
        updateView();
        loadAppointments();
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
        attachEventListeners();
        updateView();
        updateNavigation();
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
    }

    // Fonctions de gestion des vues
    function switchView(view) {
        currentView = view;
        elements.viewButtons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.view === view);
        });

        elements.weekView.classList.toggle('active', view === 'week');
        elements.dayView.classList.toggle('active', view === 'day');

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

    // Mise à jour de l'affichage
    function updateView() {
        if (currentView === 'week') {
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
        loadAppointments();
        
        // Déclencher l'événement de mise à jour du calendrier
        const viewStartDate = currentView === 'week' ? getMonday(currentDate) : new Date(currentDate);
        const viewEndDate = new Date(viewStartDate);
        if (currentView === 'week') {
            viewEndDate.setDate(viewEndDate.getDate() + 6);
        }
        
        const startDateStr = formatDateISO(viewStartDate);
        const endDateStr = formatDateISO(viewEndDate);
        
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
    function loadAppointments() {
        // Nettoyer les rendez-vous existants
        document.querySelectorAll('.slot-cell.reserved').forEach(el => el.remove());

        const viewStartDate = currentView === 'week' ? getMonday(currentDate) : new Date(currentDate);
        const viewEndDate = new Date(viewStartDate);
        if (currentView === 'week') {
            viewEndDate.setDate(viewEndDate.getDate() + 6);
        }

        const start = formatDateISO(viewStartDate);
        const end = formatDateISO(viewEndDate);

        // Réinitialiser les créneaux de la vue active
        const activeView = currentView === 'week' ? '.week-view' : '.day-view';
        const slots = document.querySelectorAll(`${activeView} .slot-cell`);
        slots.forEach(slot => {
            slot.classList.remove('reserved');
            slot.removeAttribute('title');
            slot.style.backgroundColor = ''; // Réinitialiser la couleur
        });

        console.log('Chargement des rendez-vous pour la période:', { 
            start, 
            end, 
            view: currentView,
            activeView: activeView
        });

        fetch(`index.php?page=agenda&action=getAppointments&start=${start}&end=${end}`)
            .then(response => response.json())
            .then(events => {
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
                        
                        // Format uniforme pour les deux vues
                        appointmentElement.innerHTML = `${formatTime(startTime)} - ${formatTime(endTime)} ${titleDisplay} ${durationText}`;

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
            })
            .catch(error => console.error('Erreur lors du chargement des rendez-vous:', error));
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

    // Démarrage de l'application
    init();
});