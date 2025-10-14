<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/flash-messages.php';
?>
<link rel="stylesheet" href="/CabinetDupont/css/services-legend.css">
<link rel="stylesheet" href="/CabinetDupont/css/agenda-unavailable.css">

<main class="calendar-container">
    <div class="calendar-header">
        <h2 class="planning-title">Mon Planning</h2>
        
        <!-- Navigation principale -->
        <div class="calendar-navigation">
            <div class="nav-buttons">
                <button class="nav-btn prev">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="current-period"></div>
                <button class="nav-btn next">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            
            <div class="view-selector">
                <button class="view-btn active" data-view="week">Semaine</button>
                <button class="view-btn" data-view="day">Jour</button>
            </div>
        </div>
    </div>

    <!-- Légende des services -->
    <div class="services-legend">
        <?php foreach ($services as $service): ?>
        <div class="legend-item">
            <span class="color-dot" style="background-color: <?php echo htmlspecialchars($service['couleur'] ?? '#4CAF50'); ?>"></span>
            <span class="service-name"><?php echo htmlspecialchars($service['titre']); ?></span>
        </div>
        <?php endforeach; ?>
    </div>

        <!-- Conteneur des vues -->
        <div class="calendar-views">
            <!-- Vue semaine -->
            <div class="week-view active">
                <div class="calendar-grid">
                    <!-- Colonne des heures -->
                    <div class="time-column">
                        <div class="time-header"></div>
                        <div class="time-slots">
                        <?php
                        for ($hour = $heure_min; $hour <= $heure_max; $hour++) {
                            echo '<div class="time-row">';
                            echo '<div class="hour-cell">' . sprintf('%02d:00', $hour) . '</div>';
                            if ($hour < $heure_max) {
                                echo '<div class="half-hour-cell">' . sprintf('%02d:30', $hour) . '</div>';
                            }
                            echo '</div>';
                        }
                        ?>
                        </div>
                    </div>

                    <!-- Colonnes des jours -->
                    <?php
                    $jours_fr = [
                        'lundi' => 'Lundi',
                        'mardi' => 'Mardi',
                        'mercredi' => 'Mercredi',
                        'jeudi' => 'Jeudi',
                        'vendredi' => 'Vendredi',
                        'samedi' => 'Samedi'
                    ];
                    
                    foreach ($jours_fr as $jour_en => $jour_fr) {
                        if (in_array($jour_en, $jours_ouverture)) {
                    ?>
                        <div class="day-column" data-day="<?php echo $jour_en; ?>">
                            <div class="day-header">
                                <span class="day-name"><?php echo $jour_fr; ?></span>
                                <span class="day-date">
                                    <?php echo date('d/m', strtotime($jour_en . ' this week')); ?>
                                </span>
                            </div>
                            <div class="day-content">
                                <?php
                                for ($hour = $heure_min; $hour <= $heure_max; $hour++) {
                                    echo '<div class="time-row">';
                                    echo '<div class="slot-cell" data-hour="' . sprintf('%02d:00', $hour) . '"></div>';
                                    if ($hour < $heure_max) {
                                        echo '<div class="slot-cell" data-hour="' . sprintf('%02d:30', $hour) . '"></div>';
                                    }
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    <?php 
                        }
                    }
                    ?>
                </div>
            </div>

            <!-- Vue jour -->
            <div class="day-view">
                <div class="calendar-grid">
                    <!-- Colonne des heures -->
                    <div class="time-column">
                        <div class="time-header"></div>
                        <div class="time-slots">
                        <?php
                        for ($hour = $heure_min; $hour <= $heure_max; $hour++) {
                            echo '<div class="time-row">';
                            echo '<div class="hour-cell">' . sprintf('%02d:00', $hour) . '</div>';
                            if ($hour < $heure_max) {
                                echo '<div class="half-hour-cell">' . sprintf('%02d:30', $hour) . '</div>';
                            }
                            echo '</div>';
                        }
                        ?>
                        </div>
                    </div>
                    
                    <!-- Colonne du jour -->
                    <div class="day-column">
                        <div class="day-header">
                            <span class="day-name"></span>
                            <span class="day-date"></span>
                        </div>
                        <div class="day-content">
                            <?php
                            for ($hour = $heure_min; $hour <= $heure_max; $hour++) {
                                echo '<div class="time-row">';
                                echo '<div class="slot-cell" data-hour="' . sprintf('%02d:00', $hour) . '"></div>';
                                if ($hour < $heure_max) {
                                    echo '<div class="slot-cell" data-hour="' . sprintf('%02d:30', $hour) . '"></div>';
                                }
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

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

<script src="<?php echo BASE_URL; ?>/js/agenda.js"></script>
<script src="<?php echo BASE_URL; ?>/js/agenda-unavailable.js"></script>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>