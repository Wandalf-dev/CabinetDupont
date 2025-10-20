<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/flash-messages.php';
$v = time(); // Version pour forcer le rechargement du cache
?>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/modules/service/services-legend.css?v=<?php echo $v; ?>">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/modules/agenda/agenda.css?v=<?php echo $v; ?>">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/modules/agenda/agenda-appointments.css?v=<?php echo $v; ?>">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/modules/agenda/agenda-unavailable.css?v=<?php echo $v; ?>">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/components/tooltip.css?v=<?php echo $v; ?>">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/modules/agenda/agenda-grid.css?v=<?php echo $v; ?>">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/modules/agenda/agenda-slots.css?v=<?php echo $v; ?>">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/modules/agenda/agenda-day-view.css?v=<?php echo $v; ?>">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/components/context-menu.css?v=<?php echo $v; ?>">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/components/confirmation-popup.css?v=<?php echo $v; ?>">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/modules/agenda/edit-appointment.css?v=<?php echo $v; ?>">

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

    <!-- Légendes : Services + Statuts -->
    <div class="planning-legends">
        <!-- Légende des services (à gauche) -->
        <div class="services-legend">
            <h4 class="legend-title">Services</h4>
            <div class="legend-items">
                <?php foreach ($services as $service): ?>
                <div class="legend-item">
                    <span class="color-dot" style="background-color: <?php echo htmlspecialchars($service['couleur'] ?? '#4CAF50'); ?>"></span>
                    <span class="service-name"><?php echo htmlspecialchars($service['titre']); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Légende des statuts (à droite) -->
        <div class="status-legend">
            <h4 class="legend-title">Statuts</h4>
            <div class="legend-items">
                <div class="legend-item">
                    <span class="status-icon">
                        <svg width="14" height="14" viewBox="0 0 512 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="m256 486.075c-126.864 0-230.076-103.213-230.076-230.08 0-126.861 103.212-230.07 230.076-230.07s230.076 103.209 230.076 230.07c0 126.866-103.212 230.08-230.076 230.08zm0-428.15c-109.22 0-198.076 88.854-198.076 198.07 0 109.222 88.856 198.08 198.076 198.08s198.076-88.858 198.076-198.08c0-109.216-88.856-198.07-198.076-198.07z"/>
                            <path d="m333.12 332.547c-2.838 0-5.711-.755-8.312-2.34l-77.135-47.01c-4.766-2.904-7.673-8.082-7.673-13.663v-113.59c0-8.836 7.164-16 16-16s16 7.164 16 16v104.604l69.461 42.333c7.546 4.599 9.935 14.444 5.336 21.989-3.013 4.946-8.281 7.677-13.677 7.677z"/>
                        </svg>
                    </span>
                    <span class="status-name">Confirmé</span>
                </div>
                <div class="legend-item">
                    <span class="status-icon status-honore">
                        <svg width="14" height="14" viewBox="0 0 64 64" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="m53.336 20.208-27.353 29.285a1 1 0 0 1 -1.525-.075l-13.858-18.118a1 1 0 0 1 .187-1.4l5.045-3.859a1 1 0 0 1 1.4.187l7.862 10.272a1 1 0 0 0 1.525.075l20.613-22.068a1 1 0 0 1 1.413-.049l4.642 4.342a1 1 0 0 1 .049 1.408z"/>
                        </svg>
                    </span>
                    <span class="status-name">Honoré</span>
                </div>
                <div class="legend-item">
                    <span class="status-icon status-absent">
                        <svg width="14" height="14" viewBox="0 0 512 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="m409.5 440.3c0 11.1-9 20.1-20.1 20.1h-369.3c-11.1.1-20.1-8.9-20.1-20 0-78.9 64.2-143.2 143.1-143.2h123.2c79 0 143.2 64.2 143.2 143.1z"/>
                            <path d="m318.4 165.2c0 62.8-50.9 113.6-113.6 113.6s-113.7-50.9-113.7-113.6 50.9-113.6 113.6-113.6c62.8 0 113.6 50.8 113.7 113.6z"/>
                            <path d="m470.8 224 35.3-35.3c7.7-8 7.5-20.7-.5-28.4-7.8-7.5-20.2-7.5-28 0l-35.3 35.3-35.3-35.3c-7.9-7.9-20.6-7.9-28.5 0s-7.9 20.6 0 28.5l35.3 35.3-35.3 35.3c-7.9 7.9-7.9 20.6 0 28.5s20.6 7.9 28.5 0l35.3-35.3 35.3 35.3c8 7.7 20.7 7.5 28.5-.5 7.5-7.8 7.5-20.2 0-28z"/>
                        </svg>
                    </span>
                    <span class="status-name">Absent</span>
                </div>
            </div>
        </div>
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
            <p><strong>Durée :</strong> <span class="appointment-duration"></span>&nbsp;minutes</p>
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

<script src="<?php echo BASE_URL; ?>/js/modules/agenda/agenda-unavailable.js"></script>
<script src="<?php echo BASE_URL; ?>/js/modules/agenda/agenda.js"></script>
<script src="<?php echo BASE_URL; ?>/js/modules/agenda/agenda-appointments.js"></script>
<script src="<?php echo BASE_URL; ?>/js/modules/agenda/context-menu.js"></script>
<script src="<?php echo BASE_URL; ?>/js/modules/agenda/appointment-actions.js"></script>
<script src="<?php echo BASE_URL; ?>/js/modules/agenda/edit-appointment.js"></script>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>