<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/flash-messages.php';
?>

<!-- CSS spécifiques aux rendez-vous -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/modules/rendez-vous/select-date.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/components/calendar-align.css">

<main class="container">
    <section class="select-date">
        <h1 class="page-title">
            <i class="fas fa-calendar-day"></i>
            Choisissez une date de rendez-vous
        </h1>
        
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
        
        <div class="navigation-buttons">
            <a href="<?= BASE_URL ?>/index.php?page=rendezvous&action=selectConsultation" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Retour à la sélection du motif
            </a>
        </div>
    </section>
</main>

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