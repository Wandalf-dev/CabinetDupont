<?php
$csrf_token = \App\Core\Csrf::generateToken();
?>

<div class="creneaux-container">
    <!-- Barre d'outils principale -->
    <div class="creneaux-toolbar">
        <div class="toolbar-left">
            <h2 class="section-title">Gestion des Créneaux</h2>
        </div>
        <div class="toolbar-center">
            <div class="date-navigation">
                <button class="btn-nav prev-day" type="button" aria-label="Jour précédent">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="current-date">
                    <input
                        type="date"
                        id="date-picker"
                        value="<?php echo date('Y-m-d'); ?>"
                        aria-label="Choisir une date"
                    >
                </div>
                <button class="btn-nav next-day" type="button" aria-label="Jour suivant">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        <div class="toolbar-right">
            <div class="view-toggle" role="group" aria-label="Changer de vue">
                <button class="btn-icon" data-view="grid" type="button" title="Grille">
                    <i class="fas fa-th"></i>
                </button>
                <button class="btn-icon active" data-view="list" type="button" title="Liste">
                    <i class="fas fa-list"></i>
                </button>
            </div>
            <button class="btn-primary" id="btn-generer-creneaux" type="button">
                <i class="fas fa-plus"></i>
                Générer des créneaux
            </button>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="filters-bar">
        <div class="search-box">
            <i class="fas fa-search" aria-hidden="true"></i>
            <input type="text" placeholder="Rechercher..." aria-label="Rechercher un créneau">
        </div>
        <div class="filter-options">
            <label>
                <span class="sr-only">Filtrer par statut</span>
                <select id="filter-status">
                    <option value="all">Tous les statuts</option>
                    <option value="available">Disponibles</option>
                    <option value="reserved">Réservés</option>
                    <option value="unavailable">Indisponibles</option>
                </select>
            </label>
            <label>
                <span class="sr-only">Filtrer par période</span>
                <select id="filter-period">
                    <option value="all">Toute la journée</option>
                    <option value="morning">Matin</option>
                    <option value="afternoon">Après-midi</option>
                </select>
            </label>
        </div>
    </div>

    <!-- Actions de sélection -->
    <div class="selected-actions">
        <span class="selected-count">0 sélectionné(s)</span>
        <button type="button" class="btn-secondary btn-select-all" id="btn-select-all">
            <i class="fas fa-check-square"></i>
            Tout sélectionner
        </button>
        <button class="btn-warning" id="btn-mark-unavailable" type="button">
            <i class="fas fa-ban"></i>
            Marquer indisponible
        </button>
        <button class="btn-danger" id="btn-delete-selected" type="button" disabled>
            <i class="fas fa-trash"></i>
            Supprimer
        </button>
    </div>

    <!-- Vue principale des créneaux -->
    <div class="creneaux-view" data-view="list">
        <?php if (isset($creneaux) && !empty($creneaux)): ?>
            <!-- Stats de la journée -->
            <div class="day-stats">
                <div class="stat-item">
                    <span class="stat-value"><?php echo count($creneaux); ?></span>
                    <span class="stat-label">Créneaux total</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">
                        <?php
                        echo count(array_filter($creneaux, function ($c) {
                            return empty($c['est_reserve']) && ($c['statut'] ?? '') !== 'indisponible';
                        }));
                        ?>
                    </span>
                    <span class="stat-label">Disponibles</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">
                        <?php
                        echo count(array_filter($creneaux, function ($c) {
                            return !empty($c['est_reserve']);
                        }));
                        ?>
                    </span>
                    <span class="stat-label">Réservés</span>
                </div>
            </div>

            <!-- Grille/Liste des créneaux -->
            <div class="creneaux-grid">
                <?php foreach ($creneaux as $creneau): ?>
                    <?php
                    $isIndispo  = ($creneau['statut'] ?? '') === 'indisponible';
                    $isReserved = !$isIndispo && !empty($creneau['est_reserve']);
                    $stateClass = $isIndispo ? 'unavailable' : ($isReserved ? 'reserved' : 'available');
                    ?>
                    <div class="creneau-card <?php echo $stateClass; ?>">
                        <div class="card-header">
                            <label class="checkbox-wrapper">
                                <input
                                    type="checkbox"
                                    class="creneau-select"
                                    data-id="<?php echo (int)$creneau['id']; ?>"
                                    <?php echo $isReserved ? 'disabled' : ''; ?>
                                >
                                <span class="checkmark"></span>
                            </label>
                            <div class="time-badge">
                                <i class="far fa-clock" aria-hidden="true"></i>
                                <?php echo date('H:i', strtotime($creneau['debut'])); ?>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="status-info">
                                <i class="fas <?php echo $isReserved ? 'fa-calendar-check' : ($isIndispo ? 'fa-ban' : 'fa-calendar'); ?>"></i>
                                <span>
                                    <?php echo $isReserved ? 'Réservé' : ($isIndispo ? 'Indisponible' : 'Disponible'); ?>
                                </span>
                            </div>

                            <?php if ($isReserved): ?>
                                <div class="reservation-info">
                                    <div class="service">
                                        <i class="fas fa-stethoscope" aria-hidden="true"></i>
                                        <span><?php echo htmlspecialchars($creneau['service_titre'] ?? 'Non spécifié'); ?></span>
                                    </div>
                                    <?php if (!empty($creneau['patient_nom'])): ?>
                                        <div class="patient">
                                            <i class="fas fa-user" aria-hidden="true"></i>
                                            <span><?php echo htmlspecialchars($creneau['patient_nom']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-footer">
                            <?php if ($isReserved): ?>
                                <button type="button" class="btn-danger btn-cancel" data-id="<?php echo (int)($creneau['rdv_id'] ?? $creneau['id']); ?>">
                                    <i class="fas fa-times"></i>
                                    Annuler
                                </button>
                            <?php else: ?>
                                <div class="action-buttons">
                                    <?php if (!$isIndispo): ?>
                                        <button type="button" class="btn-warning btn-unavailable" data-id="<?php echo (int)$creneau['id']; ?>" title="Rendre indisponible">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn-success btn-available" data-id="<?php echo (int)$creneau['id']; ?>" title="Rendre disponible">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    <?php endif; ?>
                                    <button type="button" class="btn-danger btn-delete" data-id="<?php echo (int)$creneau['id']; ?>" title="Supprimer le créneau">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="far fa-calendar-times" aria-hidden="true"></i>
                <h3>Aucun créneau disponible</h3>
                <p>Il n'y a pas de créneaux pour cette date</p>
                <button class="btn-primary" id="btn-generer-empty" type="button">
                    <i class="fas fa-plus"></i>
                    Générer des créneaux
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de génération de créneaux -->
<div class="modal" id="modal-generer" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="modal-content" role="document">
        <div class="modal-header">
            <h3>Générer des créneaux</h3>
            <button class="btn-close" type="button" aria-label="Fermer">×</button>
        </div>
        <div class="modal-body">
            <form id="form-generer">
                <div class="form-group">
                    <label for="date_debut">Date de début</label>
                    <input
                        type="date"
                        id="date_debut"
                        name="date_debut"
                        required
                        min="<?php echo date('Y-m-d'); ?>"
                    >
                </div>
                <div class="form-group">
                    <label for="date_fin">Date de fin</label>
                    <input
                        type="date"
                        id="date_fin"
                        name="date_fin"
                        required
                        min="<?php echo date('Y-m-d'); ?>"
                    >
                </div>
                <div class="form-info">
                    <i class="fas fa-info-circle" aria-hidden="true"></i>
                    <span>Les créneaux seront générés selon les horaires définis dans les paramètres du cabinet.</span>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" data-dismiss="modal" type="button">Annuler</button>
            <button class="btn-primary" id="btn-confirmer-generation" type="button">Générer</button>
        </div>
    </div>
</div>

<!-- Meta tag pour le CSRF token -->
<meta name="csrf-token" content="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
