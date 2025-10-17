<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/flash-messages.php';
?>

<main class="container">
    <section class="select-date">
        <h2 class="section-title">Choisissez une date de rendez-vous</h2>
        
        <div class="calendar-wrapper">
            <!-- Dates disponibles en format JSON pour JavaScript -->
            <script>
                var datesDisponibles = <?php echo json_encode($datesDisponibles); ?>;
                var serviceId = <?php echo json_encode($service['id']); ?>;
            </script>
            
            <div class="calendar-navigation">
                <button class="nav-btn prev-month">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h3 class="current-month"><!-- Sera rempli par JS --></h3>
                <button class="nav-btn next-month">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <div class="calendar">
                <div class="calendar-header">
                    <div>Lun</div>
                    <div>Mar</div>
                    <div>Mer</div>
                    <div>Jeu</div>
                    <div>Ven</div>
                    <div>Sam</div>
                    <div>Dim</div>
                </div>
                <div class="calendar-days">
                    <!-- Les jours seront générés par JS -->
                </div>
            </div>
        </div>
    </section>
</main>

<style>
.select-date {
    padding: 2rem 0;
    max-width: 800px;
    margin: 0 auto;
}

.calendar-wrapper {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    margin-top: 1.5rem;
    max-width: 500px;  /* Réduire la largeur maximale */
    margin-left: auto;
    margin-right: auto;
}

.calendar-navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.current-month {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1976d2;
}

.nav-btn {
    background: none;
    border: 2px solid #1976d2;
    color: #1976d2;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.nav-btn:hover {
    background: #1976d2;
    color: white;
}

.calendar {
    width: 100%;
}

.calendar {
    width: 100%;
    padding: 0 8px; /* Ajouter un peu de padding pour éviter que les cercles touchent les bords */
}

.calendar-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    text-align: center;
    font-weight: 600;
    color: #666;
    margin-bottom: 0.5rem; /* Réduire l'espace entre l'en-tête et les jours */
    padding: 0 4px; /* Aligner avec les cercles en dessous */
}

.calendar-header div {
    width: 36px; /* Même largeur que les cercles */
    margin: 0 auto; /* Centrer dans la colonne */
}

.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
    font-size: 0.9rem;
    justify-items: center; /* Centrer les éléments horizontalement */
}

.calendar-day {
    width: 36px; /* Taille fixe pour les cercles */
    height: 36px; /* Taille fixe pour les cercles */
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

.calendar-day:hover:not(.disabled) {
    background: #e3f2fd;
    color: #1976d2;
}

.calendar-day.active {
    background: #1976d2;
    color: white;
}

.calendar-day.disabled {
    color: #ccc;
    cursor: not-allowed;
    background: #f5f5f5;
}

.calendar-day.today {
    border: 2px solid #1976d2;
    font-weight: bold;
}
</style>

<script>
class Calendar {
    constructor() {
        this.currentDate = new Date();
        this.selectedDate = null;
        this.serviceId = new URLSearchParams(window.location.search).get('service_id');
        
        this.init();
    }

    init() {
        this.renderMonth();
        this.attachEventListeners();
    }

    renderMonth() {
        const year = this.currentDate.getFullYear();
        const month = this.currentDate.getMonth();
        
        // Mise à jour du titre du mois
        document.querySelector('.current-month').textContent = 
            new Date(year, month).toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });

        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        
        // Ajuster pour commencer par lundi (1) au lieu de dimanche (0)
        let startDay = firstDay.getDay() - 1;
        if (startDay === -1) startDay = 6;

        const daysContainer = document.querySelector('.calendar-days');
        daysContainer.innerHTML = '';

        // Jours du mois précédent
        for (let i = 0; i < startDay; i++) {
            daysContainer.appendChild(this.createDayElement(null, true));
        }

        // Jours du mois actuel
        for (let day = 1; day <= lastDay.getDate(); day++) {
            const date = new Date(year, month, day);
            const isDisabled = this.isDateDisabled(date);
            daysContainer.appendChild(this.createDayElement(day, isDisabled));
        }
    }

    createDayElement(day, isDisabled) {
        const div = document.createElement('div');
        div.className = 'calendar-day';
        
        if (day) {
            div.textContent = day;
            
            if (isDisabled) {
                div.classList.add('disabled');
            } else {
                div.addEventListener('click', () => this.selectDate(day));
            }

            // Marquer le jour actuel
            const today = new Date();
            if (day === today.getDate() && 
                this.currentDate.getMonth() === today.getMonth() && 
                this.currentDate.getFullYear() === today.getFullYear()) {
                div.classList.add('today');
            }
        }
        
        return div;
    }

    isDateDisabled(date) {
        // Désactiver les dates passées
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const dateToCheck = new Date(date);
        dateToCheck.setHours(0, 0, 0, 0);
        
        if (dateToCheck < today) return true;
        
        // Désactiver uniquement les dimanches
        if (date.getDay() === 0) return true;
        
        // Désactiver les dates au-delà de 2 semaines
        const twoWeeksFromNow = new Date();
        twoWeeksFromNow.setDate(twoWeeksFromNow.getDate() + 14);
        twoWeeksFromNow.setHours(23, 59, 59, 999);
        if (dateToCheck > twoWeeksFromNow) return true;
        
        // Vérifier si la date est dans la liste des dates disponibles
        const formattedDate = this.formatDate(date);
        const isAvailable = datesDisponibles.includes(formattedDate);
        console.log('Vérification de la date:', formattedDate, '- Disponible:', isAvailable);
        console.log('Liste complète des dates disponibles:', datesDisponibles);
        return !isAvailable;
    }

    formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    selectDate(day) {
        const selectedDate = new Date(
            this.currentDate.getFullYear(),
            this.currentDate.getMonth(),
            day
        );
        
        // Formater la date en préservant le fuseau horaire local
        const year = selectedDate.getFullYear();
        const month = String(selectedDate.getMonth() + 1).padStart(2, '0');
        const dayStr = String(selectedDate.getDate()).padStart(2, '0');
        const formattedDate = `${year}-${month}-${dayStr}`;
        
        window.location.href = `index.php?page=rendezvous&action=selectTime&service_id=${this.serviceId}&date=${formattedDate}`;
    }

    attachEventListeners() {
        document.querySelector('.prev-month').addEventListener('click', () => {
            this.currentDate.setMonth(this.currentDate.getMonth() - 1);
            this.renderMonth();
        });

        document.querySelector('.next-month').addEventListener('click', () => {
            this.currentDate.setMonth(this.currentDate.getMonth() + 1);
            this.renderMonth();
        });
    }
}

// Initialiser le calendrier
document.addEventListener('DOMContentLoaded', () => {
    new Calendar();
});
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>