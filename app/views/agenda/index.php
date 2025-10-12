<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/flash-messages.php';
?>

<main class="container">
    <section class="agenda-section">
        <h2>Mon Agenda</h2>

        <!-- Barre d'outils de l'agenda -->
        <div class="agenda-toolbar">
            <div class="date-navigation">
                <button class="btn-nav prev-week">
                    <i class="fas fa-chevron-left"></i> Semaine précédente
                </button>
                <span class="current-week">
                    Du <?php echo date('d/m/Y', strtotime('monday this week')); ?> 
                    au <?php echo date('d/m/Y', strtotime('sunday this week')); ?>
                </span>
                <button class="btn-nav next-week">
                    Semaine suivante <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="view-options">
                <button class="btn-view active" data-view="week">Vue semaine</button>
                <button class="btn-view" data-view="day">Vue jour</button>
            </div>
        </div>

        <!-- Grille de l'agenda -->
        <div class="agenda-grid week-view">
            <!-- En-têtes des jours -->
            <div class="time-column">
                <div class="time-header"></div>
                <?php
                // Génération des heures (de 8h à 19h)
                for ($hour = 8; $hour <= 19; $hour++) {
                    echo '<div class="time-slot">' . sprintf('%02d:00', $hour) . '</div>';
                }
                ?>
            </div>

            <?php
            $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
            foreach ($jours as $jour) :
            ?>
            <div class="day-column">
                <div class="day-header">
                    <?php echo $jour; ?>
                    <span class="date">
                        <?php 
                        $date = date('d/m', strtotime(strtolower($jour) . ' this week'));
                        echo $date;
                        ?>
                    </span>
                </div>
                <?php
                // Cases horaires
                for ($hour = 8; $hour <= 19; $hour++) {
                    echo '<div class="time-slot"></div>';
                }
                ?>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Vue journalière (masquée par défaut) -->
        <div class="agenda-grid day-view" style="display: none;">
            <div class="time-column">
                <div class="time-header"></div>
                <?php
                for ($hour = 8; $hour <= 19; $hour++) {
                    echo '<div class="time-slot">' . sprintf('%02d:00', $hour) . '</div>';
                }
                ?>
            </div>
            <div class="day-column active">
                <div class="day-header">
                    <?php echo date('l d/m'); ?>
                </div>
                <?php
                for ($hour = 8; $hour <= 19; $hour++) {
                    echo '<div class="time-slot"></div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Légende des rendez-vous -->
    <div class="agenda-legend">
        <div class="legend-item">
            <span class="legend-color" style="background-color: #4CAF50;"></span>
            <span>Rendez-vous confirmé</span>
        </div>
        <div class="legend-item">
            <span class="legend-color" style="background-color: #FFC107;"></span>
            <span>En attente de confirmation</span>
        </div>
        <div class="legend-item">
            <span class="legend-color" style="background-color: #f44336;"></span>
            <span>Urgence</span>
        </div>
    </div>
</main>

<!-- Modal détail rendez-vous -->
<div id="rdv-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Détails du rendez-vous</h3>
        <div class="rdv-details">
            <p><strong>Patient:</strong> <span id="rdv-patient"></span></p>
            <p><strong>Date:</strong> <span id="rdv-date"></span></p>
            <p><strong>Heure:</strong> <span id="rdv-heure"></span></p>
            <p><strong>Service:</strong> <span id="rdv-service"></span></p>
            <p><strong>Statut:</strong> <span id="rdv-statut"></span></p>
            <p><strong>Notes:</strong> <span id="rdv-notes"></span></p>
        </div>
        <div class="modal-actions">
            <button class="btn-admin confirm">Confirmer</button>
            <button class="btn-admin reschedule">Reprogrammer</button>
            <button class="btn-admin cancel">Annuler le rendez-vous</button>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>