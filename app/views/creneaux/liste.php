<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/flash-messages.php';
?>

<!-- Inclusion des styles CSS spécifiques -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/creneaux.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/calendar-compact.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/confirmation-popup.css">

<main class="container">
    <section class="admin-section">
        <h2>Liste des créneaux disponibles</h2>

        <div class="filters">
            <form method="get" class="form-inline">
                <input type="hidden" name="page" value="creneaux">
                <input type="hidden" name="action" value="liste">
                
                <div class="form-group">
                    <label for="date">Date :</label>
                    <input type="date" id="date" name="date" 
                           value="<?php echo isset($_GET['date']) ? htmlspecialchars($_GET['date']) : date('Y-m-d'); ?>"
                           min="<?php echo date('Y-m-d'); ?>">
                </div>

                <button type="submit" class="btn-filter">Filtrer</button>
            </form>
        </div>

        <?php if (!empty($creneaux)): ?>
            <div class="creneaux-accordion">
                <!-- Section Matin -->
                <?php
                $creneauxMatins = array_filter($creneaux, function($c) {
                    return (int)date('H', strtotime($c['debut'])) < 12;
                });
                
                if (!empty($creneauxMatins)):
                ?>
                <div class="accordion-item">
                    <div class="accordion-header">
                        <button type="button" class="accordion-button">
                            <span class="date">Matin</span>
                            <span class="count"><?php echo count($creneauxMatins); ?> créneaux</span>
                            <span class="disponibles"><?php 
                                $disponibles = count(array_filter($creneauxMatins, function($c) { 
                                    return !$c['est_reserve']; 
                                }));
                                echo $disponibles . " disponibles";
                            ?></span>
                        </button>
                    </div>
                    <div class="accordion-content">
                        <?php 
                        $creneauxSection = $creneauxMatins;
                        require __DIR__ . '/tableau-creneaux.php'; 
                        ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Section Après-midi -->
                <?php
                $creneauxApresMidi = array_filter($creneaux, function($c) {
                    return (int)date('H', strtotime($c['debut'])) >= 12;
                });
                
                if (!empty($creneauxApresMidi)):
                ?>
                <div class="accordion-item">
                    <div class="accordion-header">
                        <button type="button" class="accordion-button">
                            <span class="date">Après-midi</span>
                            <span class="count"><?php echo count($creneauxApresMidi); ?> créneaux</span>
                            <span class="disponibles"><?php 
                                $disponibles = count(array_filter($creneauxApresMidi, function($c) { 
                                    return !$c['est_reserve']; 
                                }));
                                echo $disponibles . " disponibles";
                            ?></span>
                        </button>
                    </div>
                    <div class="accordion-content">
                        <?php 
                        $creneauxSection = $creneauxApresMidi;
                        require __DIR__ . '/tableau-creneaux.php'; 
                        ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="no-results">Aucun créneau disponible pour cette date.</p>
        <?php endif; ?>
            <div class="admin-actions">
                <a href="index.php?page=creneaux&action=generer" class="btn-admin">
                    <i class="fas fa-calendar-plus"></i> Générer des créneaux
                </a>
                <a href="index.php?page=admin" class="btn-admin">Retour</a>
            </div>
        </div>
    </section>
</main>

<!-- Inclusion du JavaScript spécifique -->
<script src="<?php echo BASE_URL; ?>/js/creneaux-liste.js"></script>
<script src="<?php echo BASE_URL; ?>/js/creneaux-status.js"></script>

<style>
.badge.indisponible {
    background-color: #dc3545;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
}

.btn-group {
    display: flex;
    gap: 8px;
    align-items: center;
}

.btn-toggle-dispo {
    padding: 4px 8px;
    border-radius: 4px;
    border: none;
    color: white;
    cursor: pointer;
    transition: opacity 0.2s;
}

.btn-toggle-dispo:hover {
    opacity: 0.9;
}

.btn-toggle-dispo.btn-warning {
    background-color: #ffc107;
    color: #212529;
}

.btn-toggle-dispo.btn-success {
    background-color: #28a745;
}
</style>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>