<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/flash-messages.php';
?>

<main class="container">
    <div class="admin-form-container">
        <h1>Génération des créneaux de consultation</h1>
        
        <div class="creneaux-info">
            <small><i class="fas fa-info-circle"></i> Créneaux générés :</small>
            <div class="creneaux-resume">
                <span>Matin : 8h-12h (8 créneaux)</span>
                <span>Après-midi : 14h-20h (12 créneaux)</span>
                <small>Intervalle : 30min</small>
            </div>
        </div>

        <form method="post" class="admin-form">
            <div class="form-group">
                <label>Périodes prédéfinies :</label>
                <div class="btn-group mb-3">
                    <button type="button" class="btn btn-outline-primary" data-days="7">1 semaine</button>
                    <button type="button" class="btn btn-outline-primary" data-days="14">2 semaines</button>
                    <button type="button" class="btn btn-outline-primary" data-days="30">1 mois</button>
                </div>
            </div>

            <div class="form-group">
                <label for="date_debut">Date de début</label>
                <input type="date" id="date_debut" name="date_debut" required
                       min="<?php echo date('Y-m-d'); ?>" 
                       value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
                <label for="date_fin">Date de fin</label>
                <input type="date" id="date_fin" name="date_fin" required
                       min="<?php echo date('Y-m-d'); ?>"
                       value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>">
                <small class="form-text text-muted">
                    Pour une meilleure gestion, il est recommandé de ne pas dépasser 2 semaines à la fois.
                </small>
            </div>

            <!-- Champs cachés pour confirmation -->
            <input type="hidden" id="confirmed_date_debut" name="confirmed_date_debut" value="">
            <input type="hidden" id="confirmed_date_fin" name="confirmed_date_fin" value="">

            <div class="form-actions">
                <button type="button" id="btnGenerer" class="btn-admin save">
                    <i class="fas fa-calendar-plus"></i> Générer les créneaux
                </button>
                <a href="index.php?page=admin" class="btn-admin">Annuler</a>
            </div>
        </form>

        <!-- Modal de confirmation -->
        <div id="confirmationModal" class="modal">
            <div class="modal-content">
                <h3>Confirmation</h3>
                <p id="modalMessage"></p>
                <div class="modal-actions">
                    <button id="btnConfirmer" class="btn-admin save">Confirmer</button>
                    <button id="btnAnnuler" class="btn-admin">Annuler</button>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="<?php echo BASE_URL; ?>/js/creneaux.js"></script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>