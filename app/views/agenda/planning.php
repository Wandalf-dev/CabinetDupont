<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/flash-messages.php';
?>
<link rel="stylesheet" href="/CabinetDupont/css/services-legend.css">
<link rel="stylesheet" href="/CabinetDupont/css/agenda.css">
<link rel="stylesheet" href="/CabinetDupont/css/agenda-appointments.css">
<link rel="stylesheet" href="/CabinetDupont/css/agenda-unavailable.css">
<link rel="stylesheet" href="/CabinetDupont/css/tooltip.css">
<link rel="stylesheet" href="/CabinetDupont/css/agenda-grid.css">
<link rel="stylesheet" href="/CabinetDupont/css/agenda-slots.css">
<link rel="stylesheet" href="/CabinetDupont/css/agenda-day-view.css">
<link rel="stylesheet" href="/CabinetDupont/css/components/unavailable-slots.css">
<link rel="stylesheet" href="/CabinetDupont/css/context-menu.css">
<link rel="stylesheet" href="/CabinetDupont/css/confirmation-dialog.css">
<link rel="stylesheet" href="/CabinetDupont/css/notifications.css">
<link rel="stylesheet" href="/CabinetDupont/css/edit-appointment.css">

<main class="calendar-container">
    <div class="calendar-header">
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
                        for ($hour = $heure_min; $hour < $heure_max; $hour++) {
                            echo '<div class="time-row">';
                            echo '<div class="hour-cell">' . sprintf('%02d:00', $hour) . '</div>';
                            echo '<div class="half-hour-cell">' . sprintf('%02d:30', $hour) . '</div>';
                            echo '</div>';
                        }
                        // Dernière ligne uniquement pour 20:00
                        echo '<div class="time-row">';
                        echo '<div class="hour-cell">' . sprintf('%02d:00', $heure_max) . '</div>';
                        echo '</div>';
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
                        <div class="day-column" 
                             data-day="<?php echo $jour_en; ?>"
                             data-date="<?php echo date('Y-m-d', strtotime($jour_en . ' this week')); ?>">
                            <div class="day-header">
                                <span class="day-name"><?php echo $jour_fr; ?></span>
                                <span class="day-date">
                                    <?php echo date('d/m', strtotime($jour_en . ' this week')); ?>
                                </span>
                            </div>
                            <div class="day-content">
                                <?php
                                for ($hour = $heure_min; $hour < $heure_max; $hour++) {
                                    echo '<div class="time-row">';
                                    echo '<div class="slot-cell" data-hour="' . sprintf('%02d:00', $hour) . '"></div>';
                                    echo '<div class="slot-cell" data-hour="' . sprintf('%02d:30', $hour) . '"></div>';
                                    echo '</div>';
                                }
                                // Dernier créneau pour 20h00
                                echo '<div class="time-row">';
                                echo '<div class="slot-cell" data-hour="' . sprintf('%02d:00', $heure_max) . '"></div>';
                                echo '</div>';
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
                        // Heures avec demi-heures (jusqu'à heure_max - 1)
                        for ($hour = $heure_min; $hour < $heure_max; $hour++) {
                            echo '<div class="time-row">';
                            echo '<div class="hour-cell">' . sprintf('%02d:00', $hour) . '</div>';
                            echo '<div class="half-hour-cell">' . sprintf('%02d:30', $hour) . '</div>';
                            echo '</div>';
                        }
                        // Dernière heure (sans demi-heure)
                        echo '<div class="time-row">';
                        echo '<div class="hour-cell">' . sprintf('%02d:00', $heure_max) . '</div>';
                        echo '</div>';
                        ?>
                        </div>
                    </div>
                    
                    <!-- Colonne du jour -->
                    <div class="day-column" data-date="">
                        <div class="day-header">
                            <span class="day-name"></span>
                            <span class="day-date"></span>
                        </div>
                        <div class="day-content">
                            <?php
                            for ($hour = $heure_min; $hour < $heure_max; $hour++) {
                                echo '<div class="time-row">';
                                echo '<div class="slot-cell" data-hour="' . sprintf('%02d:00', $hour) . '"></div>';
                                echo '<div class="slot-cell" data-hour="' . sprintf('%02d:30', $hour) . '"></div>';
                                echo '</div>';
                            }
                            // Dernier créneau pour 20h00
                            echo '<div class="time-row">';
                            echo '<div class="slot-cell" data-hour="' . sprintf('%02d:00', $heure_max) . '"></div>';
                            echo '</div>';
                            ?>
                        </div>
                    </div>
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

<!-- Formulaire de modification de rendez-vous -->
<div class="edit-appointment-overlay">
    <div class="edit-appointment-form">
        <h3>Modifier le rendez-vous</h3>
        <div class="edit-appointment-details">
            <p><strong>Patient :</strong> <span class="patient-name"></span></p>
            <p><strong>Service :</strong> <span class="service-name"></span></p>
            <p><strong>Durée :</strong> <span class="appointment-duration"></span> minutes</p>
        </div>
        <form id="edit-appointment-form">
            <input type="hidden" name="appointment_id" id="edit-appointment-id">
            <div class="form-group">
                <label for="edit-appointment-date">Nouvelle date :</label>
                <input type="date" id="edit-appointment-date" name="appointment_date" required>
            </div>
            <div class="form-group">
                <label for="edit-appointment-time">Nouvelle heure :</label>
                <select id="edit-appointment-time" name="appointment_time" required>
                    <!-- Les options seront remplies dynamiquement -->
                </select>
            </div>
            <div class="buttons">
                <button type="button" class="cancel">Annuler</button>
                <button type="submit" class="confirm">Confirmer</button>
            </div>
        </form>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>/js/notifications.js"></script>
<script src="<?php echo BASE_URL; ?>/js/agenda.js"></script>
<script src="<?php echo BASE_URL; ?>/js/agenda-appointments.js"></script>
<script src="<?php echo BASE_URL; ?>/js/agenda-unavailable.js"></script>
<script src="<?php echo BASE_URL; ?>/js/context-menu.js"></script>
<script src="<?php echo BASE_URL; ?>/js/appointment-actions.js"></script>
<script src="<?php echo BASE_URL; ?>/js/edit-appointment.js"></script>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>