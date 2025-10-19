<?php require_once 'app/views/templates/header.php'; ?>

<!-- CSS spécifique à la liste des rendez-vous -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/modules/rendez-vous/list-rendezvous.css">

<main>
    <div class="rdv-container">
        <h1 class="page-title">
            <i class="fas fa-calendar-alt"></i>
            Mes rendez-vous
        </h1>
        <div class="rdv-header">
            <a href="index.php?page=rendezvous&action=selectConsultation" class="btn btn-primary btn-new-rdv">
                <i class="fas fa-plus me-2"></i>
                Nouveau rendez-vous
            </a>
        </div>
        
        <?php if (empty($rendezvous)): ?>
            <div class="rdv-empty">
                <i class="fas fa-calendar-times"></i>
                <p>Vous n'avez aucun rendez-vous programmé.</p>
                <p class="text-muted">Cliquez sur le bouton "Nouveau rendez-vous" pour en planifier un.</p>
            </div>
        <?php else: ?>
            <div class="rdv-table">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Heure</th>
                                <th>Type de consultation</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Regrouper les créneaux par date et service
                            $groupes = [];
                            foreach ($rendezvous as $rdv) {
                                $date = date('Y-m-d', strtotime($rdv['debut']));
                                $service = $rdv['service_titre'];
                                $debut = strtotime($rdv['debut']);
                                
                                // Clé unique pour regrouper les créneaux du même rdv
                                $key = $date . '_' . $service;
                                
                                if (!isset($groupes[$key])) {
                                    $groupes[$key] = [
                                        'service_titre' => $service,
                                        'date' => date('d/m/Y', $debut),
                                        'debut' => $debut,
                                        'fin' => strtotime($rdv['debut']) + 1800, // +30min pour le premier créneau
                                        'rdv_id' => $rdv['rdv_id']
                                    ];
                                } else {
                                    // Si les créneaux sont consécutifs, étendre la plage horaire
                                    if ($debut <= $groupes[$key]['fin'] + 60) { // +60 secondes de marge
                                        $groupes[$key]['fin'] = strtotime($rdv['debut']) + 1800; // +30min
                                    }
                                }
                            }

                            foreach ($groupes as $groupe) {
                                $heure_debut = date('H:i', $groupe['debut']);
                                $heure_fin = date('H:i', $groupe['fin']);
                                $plage = $heure_debut . '-' . $heure_fin;
                            ?>
                                <tr>
                                    <td><?php echo $groupe['date']; ?></td>
                                    <td><?php echo $plage; ?></td>
                                    <td><?php echo htmlspecialchars($groupe['service_titre'] ?? 'Non spécifié'); ?></td>
                                    <td>
                                        <button type="button"
                                           class="btn btn-outline-danger btn-sm btn-cancel"
                                           data-rdv-id="<?php echo $groupe['rdv_id']; ?>"
                                           onclick="annulerRendezVous(this)">
                                            <i class="fas fa-times me-1"></i>
                                            Annuler
                                        </button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Scripts Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Styles -->
<style>
/* Style du titre principal comme sur les autres pages */
.section-title {
    font-size: 2.1rem;
    font-weight: 700;
    color: #23408e;
    font-family: 'Montserrat', 'Arial', sans-serif;
    margin-bottom: 28px;
    margin-top: 0;
    letter-spacing: 0.5px;
    text-align: center;
    position: relative;
}
/* Popup confirmation d'annulation - Custom simple */
.custom-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
    align-items: center;
    justify-content: center;
}

.custom-modal.show {
    display: flex !important;
}

.custom-modal.closing .custom-modal-dialog {
    animation: modalZoomOut 0.3s ease-out forwards;
}

.custom-modal.closing .custom-modal-backdrop {
    opacity: 0;
}

.custom-modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1;
    transition: opacity 0.3s ease;
}

.custom-modal-dialog {
    position: relative;
    z-index: 2;
    max-width: 500px;
    width: 90%;
    animation: modalZoom 0.3s ease-out;
}

@keyframes modalZoom {
    from {
        transform: scale(0.7);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes modalZoomOut {
    from {
        transform: scale(1);
        opacity: 1;
    }
    to {
        transform: scale(0.7);
        opacity: 0;
    }
}

.custom-modal-content {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.custom-modal-header {
    padding: 2rem 2rem 1rem 2rem;
    text-align: center;
    border-bottom: 1px solid #f3f4f6;
}

.custom-modal-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.custom-modal-body {
    padding: 1.5rem 2rem;
    text-align: center;
}

.custom-modal-footer {
    padding: 1.5rem 2rem;
    display: flex;
    gap: 1rem;
    justify-content: center;
    border-top: 1px solid #f3f4f6;
    background: #fafafa;
    border-radius: 0 0 20px 20px;
}

.warning-icon {
    font-size: 4rem !important;
    color: #ef4444;
    margin-bottom: 1.25rem;
    display: block;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.05);
    }
}

.modal-text {
    font-size: 1.125rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.75rem;
}

.modal-subtext {
    font-size: 0.9375rem;
    color: #9ca3af;
    font-style: italic;
    margin-bottom: 0;
}
.section-title::after {
    content: '';
    display: block;
    width: 48px;
    height: 3px;
    background: #23408e;
    border-radius: 2px;
    margin: 8px auto 0 auto;
}


/* Les styles de la liste sont dans css/modules/rendez-vous/list-rendezvous.css */

/* Centrage et style des boutons dans la popup */
.custom-modal-footer .btn-confirm {
    font-size: 1rem;
    border-radius: 10px;
    padding: 0.75rem 1.75rem;
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    border: none;
    font-weight: 600;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.custom-modal-footer .btn-confirm:hover {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
    transform: translateY(-2px);
}

.custom-modal-footer .btn-confirm:active {
    transform: translateY(0);
}

@media (max-width: 768px) {
    .custom-modal-header,
    .custom-modal-body,
    .custom-modal-footer {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }

    .custom-modal-footer {
        flex-direction: column-reverse;
        gap: 0.75rem;
    }

    .custom-modal-footer .btn-cancel,
    .custom-modal-footer .btn-confirm {
        width: 100%;
    }

    .warning-icon {
        font-size: 3.5rem !important;
    }

    .custom-modal-title {
        font-size: 1.25rem;
    }

    .modal-text {
        font-size: 1rem;
    }
}

/* Forcer le masquage de la modal par défaut */
.modal {
    display: none;
}

.modal.show {
    display: flex !important;
}

.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1050;
    width: 100vw;
    height: 100vh;
    background-color: #000;
    opacity: 0;
    transition: opacity 0.15s linear;
}

.modal-backdrop.show {
    opacity: 0.5;
}

.modal-backdrop.fade {
    opacity: 0;
}
</style>

<!-- Modal de confirmation -->
<div id="confirmationModal" class="custom-modal" style="display: none;">
    <div class="custom-modal-backdrop"></div>
    <div class="custom-modal-dialog">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <i class="fas fa-exclamation-triangle warning-icon"></i>
                <h5 class="custom-modal-title">Confirmation d'annulation</h5>
            </div>
            <div class="custom-modal-body">
                <p class="modal-text">Êtes-vous sûr de vouloir annuler ce rendez-vous ?</p>
                <p class="modal-subtext">Cette action est irréversible.</p>
            </div>
            <div class="custom-modal-footer">
                <button type="button" class="btn btn-cancel" onclick="closeModal()">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <button type="button" class="btn btn-confirm" id="confirmAnnulation">
                    <i class="fas fa-check me-2"></i>Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentButton = null;

function openModal() {
    const modal = document.getElementById('confirmationModal');
    modal.style.display = 'flex';
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const modal = document.getElementById('confirmationModal');
    modal.classList.add('closing');
    
    setTimeout(() => {
        modal.classList.remove('show');
        modal.classList.remove('closing');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }, 300);
}

function annulerRendezVous(button) {
    currentButton = button;
    openModal();
}

// Fermer la modal en cliquant sur le backdrop
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('custom-modal-backdrop')) {
        closeModal();
    }
});

function showEmptyMessage() {
    const container = document.querySelector('.rdv-container');
    const table = document.querySelector('.rdv-table');
    if (table) table.remove();
    
    const emptyMessage = document.createElement('div');
    emptyMessage.className = 'rdv-empty';
    emptyMessage.innerHTML = `
        <i class="fas fa-calendar-times"></i>
        <p>Vous n'avez aucun rendez-vous programmé.</p>
        <p class="text-muted">Cliquez sur le bouton "Nouveau rendez-vous" pour en planifier un.</p>
    `;
    container.appendChild(emptyMessage);
}

document.addEventListener('DOMContentLoaded', function() {
    const confirmButton = document.getElementById('confirmAnnulation');
    if (!confirmButton) {
        console.error('Bouton de confirmation non trouvé');
        return;
    }

    // Fonction pour mettre à jour l'interface après une annulation réussie
    function updateUIAfterCancellation() {
        const row = currentButton.closest('tr');
        if (row) {
            row.remove();
            
            const tbody = document.querySelector('.rdv-table tbody');
            if (tbody && tbody.children.length === 0) {
                showEmptyMessage();
            }
        }
    }

    // Gestionnaire de clic sur le bouton de confirmation
    confirmButton.addEventListener('click', function() {
        if (!currentButton) {
            console.error('Aucun rendez-vous sélectionné');
            return;
        }

        const rdvId = currentButton.getAttribute('data-rdv-id');
        if (!rdvId) {
            console.error('ID du rendez-vous non trouvé');
            return;
        }

        // Désactiver le bouton pendant la requête
        confirmButton.disabled = true;
        
        fetch('index.php?page=rendezvous&action=annuler', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `rdv_id=${rdvId}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.json();
        })
        .then(data => {
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
            
            if (data.success) {
                updateUIAfterCancellation();
                const successDiv = document.createElement('div');
                successDiv.className = 'alert alert-success';
                successDiv.style.position = 'fixed';
                successDiv.style.top = '20px';
                successDiv.style.right = '20px';
                successDiv.style.zIndex = '9999';
                successDiv.style.borderRadius = '10px';
                successDiv.style.maxWidth = '400px';
                successDiv.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
                successDiv.style.border = 'none';
                successDiv.style.margin = '0';
                successDiv.style.transform = 'translateZ(0)';
                successDiv.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-2"></i>
                        Le rendez-vous a été annulé avec succès.
                    </div>
                `;
                document.body.appendChild(successDiv);
                
                setTimeout(() => {
                    successDiv.style.transition = 'opacity 0.5s ease-out';
                    successDiv.style.opacity = '0';
                    setTimeout(() => successDiv.remove(), 500);
                }, 4000);
            } else {
                alert(data.message || 'L\'annulation a échoué');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            closeModal();
            alert('Une erreur est survenue lors de l\'annulation du rendez-vous.');
        })
        .finally(() => {
            confirmButton.disabled = false;
            currentButton = null;
        });
    });
});
</script>

<?php require_once 'app/views/templates/footer.php'; ?>