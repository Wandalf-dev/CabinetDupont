document.addEventListener('DOMContentLoaded', function() {
    // Constantes pour la grille
    const SLOT_HEIGHT = 30; // hauteur d'un créneau de 30 minutes en pixels
    const TIME_FORMAT = /^([0-9]{2}):([0-9]{2})$/;

    // Fonction pour calculer la position verticale
    function calculatePosition(time) {
        const match = time.match(TIME_FORMAT);
        if (!match) return 0;
        
        const hours = parseInt(match[1]);
        const minutes = parseInt(match[2]);
        const totalMinutes = hours * 60 + minutes;
        
        // Position par rapport au début de la journée (8h00)
        const startOfDay = 8 * 60; // 8h00 en minutes
        const offsetMinutes = totalMinutes - startOfDay;
        
        // Utiliser une hauteur de 30px pour chaque créneau de 30 minutes
        return offsetMinutes; // 30px par tranche de 30 minutes
    }

    // Fonction pour calculer la hauteur d'un rendez-vous
    function calculateHeight(startTime, endTime) {
        const match1 = startTime.match(TIME_FORMAT);
        const match2 = endTime.match(TIME_FORMAT);
        if (!match1 || !match2) return 30; // hauteur par défaut
        
        const start = parseInt(match1[1]) * 60 + parseInt(match1[2]);
        const end = parseInt(match2[1]) * 60 + parseInt(match2[2]);
        const durationMinutes = end - start;
        
        // Convertir la durée en pixels (30px par tranche de 30 minutes)
        return Math.max(30, durationMinutes);
    }

    // Fonction pour positionner un rendez-vous
    function positionAppointment(appointment) {
        const startTime = appointment.getAttribute('data-start-time');
        const endTime = appointment.getAttribute('data-end-time');
        
        const top = calculatePosition(startTime);
        const height = calculateHeight(startTime, endTime);
        
        // Appliquer les styles avec précision
        appointment.style.position = 'absolute';
        appointment.style.top = `${top}px`;
        appointment.style.height = `${height}px`;
        appointment.style.left = '2px';
        appointment.style.right = '2px';
        appointment.style.width = 'calc(100% - 4px)';
        
        // S'assurer que le z-index est correct
        appointment.style.zIndex = '2';
    }

    // Fonction pour calculer les colonnes des rendez-vous qui se chevauchent
    function calculateAppointmentColumns(appointments) {
        // Filtrer les rendez-vous qui ont une startTime valide
        const validAppointments = appointments.filter(appt => appt && appt.startTime);
        
        validAppointments.sort((a, b) => {
            return a.startTime.localeCompare(b.startTime);
        });

        let columns = [];
        let maxColumn = 0;

        for (let i = 0; i < validAppointments.length; i++) {
            let currentAppt = validAppointments[i];
            let column = 0;
            
            while (columns.some(appt => {
                return doesOverlap(currentAppt, appt) && appt.column === column;
            })) {
                column++;
            }
            
            currentAppt.column = column;
            columns.push(currentAppt);
            maxColumn = Math.max(maxColumn, column);

            // Application de la colonne et positionnement
            currentAppt.element.setAttribute('data-column', column);
            positionAppointment(currentAppt.element);
        }

        return maxColumn + 1;
    }

    // Fonction pour vérifier si deux rendez-vous se chevauchent
    function doesOverlap(appt1, appt2) {
        return !(appt1.endTime <= appt2.startTime || appt2.endTime <= appt1.startTime);
    }

    // Fonction pour positionner les rendez-vous dans la grille
    window.positionAppointments = function(appointments) {
        // Regroupe les rendez-vous par jour
        const appointmentsByDay = {};
        appointments.forEach(appt => {
            const day = appt.closest('.day-column').getAttribute('data-day');
            if (!appointmentsByDay[day]) {
                appointmentsByDay[day] = [];
            }
            appointmentsByDay[day].push({
                element: appt,
                startTime: appt.getAttribute('data-start-time'),
                endTime: appt.getAttribute('data-end-time')
            });
        });

        // Traite chaque jour séparément
        Object.values(appointmentsByDay).forEach(dayAppointments => {
            calculateAppointmentColumns(dayAppointments);
        });
    };

    // Fonction d'initialisation des rendez-vous
    function initializeAppointments() {
        const appointments = document.querySelectorAll('.slot-cell.reserved');
        if (appointments.length > 0) {
            positionAppointments(Array.from(appointments));
        }
    }

    // Initialisation au chargement
    initializeAppointments();

    // Écoute les changements de vue
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            setTimeout(initializeAppointments, 100);
        });
    });

    // Initialiser la grille
    function initializeGrid() {
        const dayColumns = document.querySelectorAll('.day-column');
        dayColumns.forEach(column => {
            const dayContent = column.querySelector('.day-content');
            if (dayContent) {
                dayContent.style.position = 'relative';
                dayContent.style.height = '600px'; // 12 créneaux de 50px
            }
        });
    }

    // Initialiser la grille au chargement
    initializeGrid();
});