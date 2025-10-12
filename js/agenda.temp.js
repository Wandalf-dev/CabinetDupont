document.addEventListener('DOMContentLoaded', function() {
    // État de l'application
    let currentDate = new Date();
    let currentView = 'week';

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
        updateView();
        updateNavigation();
    }

    // Mise à jour de l'affichage
    function updateView() {
        if (currentView === 'week') {
            updateWeekView();
        } else {
            updateDayView();
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
                header.querySelector('.day-name').textContent = locale.days[date.toLocaleString('en-US', { weekday: 'long' })];
                header.querySelector('.day-date').textContent = formatDate(date);
            }
        });

        // Mise à jour des attributs data pour les créneaux
        updateTimeSlots(monday, dayElements);
    }

    function updateDayView() {
        const dayElement = document.querySelector('.day-view .day-column');
        if (dayElement) {
            const header = dayElement.querySelector('.day-header');
            if (header) {
                const dayName = locale.days[currentDate.toLocaleString('en-US', { weekday: 'long' })];
                header.querySelector('.day-name').textContent = dayName;
                header.querySelector('.day-date').textContent = formatDate(currentDate);
            }

            // Mise à jour des attributs data pour les créneaux
            const slots = dayElement.querySelectorAll('.day-content .time-slot');
            slots.forEach(slot => {
                const hour = slot.dataset.hour;
                const dateStr = formatDateISO(currentDate);
                slot.dataset.date = dateStr;
                slot.dataset.datetime = `${dateStr}T${hour}:00`;
            });
        }
    }

    function updateTimeSlots(startDate, dayElements) {
        dayElements.forEach((dayElement, index) => {
            const date = new Date(startDate);
            date.setDate(date.getDate() + index);
            const dateStr = formatDateISO(date);
            
            const slots = dayElement.querySelectorAll('.day-content .time-slot');
            slots.forEach(slot => {
                const hour = slot.dataset.hour;
                slot.dataset.date = dateStr;
                slot.dataset.datetime = `${dateStr}T${hour}:00`;
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
        const day = d.getDay() || 7;
        if (day !== 1) {
            d.setHours(-24 * (day - 1));
        }
        return d;
    }

    function formatDate(date) {
        return `${date.getDate()} ${locale.months[date.getMonth()]}`;
    }

    function formatDateISO(date) {
        return date.toISOString().split('T')[0];
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