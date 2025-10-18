<?php require_once 'app/views/templates/header.php'; ?>
<link rel="stylesheet" href="css/modules/rendez-vous/list-rendezvous.css">

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

<!-- Modal de confirmation -->
<div class="modal fade modal-custom" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header flex-column align-items-center border-0 pb-0" style="background:transparent;">
                <i class="fas fa-exclamation-triangle warning-icon"></i>
                <h5 class="modal-title modal-title-center" id="confirmationModalLabel">Confirmation d'annulation</h5>
            </div>
            <div class="modal-body text-center pt-2">
                <p class="modal-text">Êtes-vous sûr de vouloir annuler ce rendez-vous ?</p>
                <p class="modal-subtext">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel btn-modal-cancel" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <button type="button" class="btn btn-confirm" id="confirmAnnulation">
                    <i class="fas fa-check me-2"></i>Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

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
/* Popup confirmation d'annulation moderne */
.modal-custom .modal-content {
    border-radius: 20px;
    border: none;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
    overflow: hidden;
}

.modal-custom .modal-header {
    background: white;
    border-bottom: 1px solid #f3f4f6;
    padding: 2rem 2rem 1rem 2rem;
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-custom .modal-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    text-align: center;
    margin: 0;
}

.modal-custom .modal-body {
    padding: 1.5rem 2rem 2rem 2rem;
    text-align: center;
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
.modal-custom .modal-footer {
    justify-content: center !important;
    display: flex !important;
    gap: 1rem;
    padding: 1.5rem 2rem;
    border-top: 1px solid #f3f4f6;
    background: #fafafa;
}

.modal-custom .btn-confirm {
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

.modal-custom .btn-confirm:hover {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
    transform: translateY(-2px);
}

.modal-custom .btn-confirm:active {
    transform: translateY(0);
}

@media (max-width: 768px) {
    .modal-custom .modal-header,
    .modal-custom .modal-body,
    .modal-custom .modal-footer {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }

    .modal-custom .modal-footer {
        flex-direction: column-reverse;
        gap: 0.75rem;
    }

    .modal-custom .btn-cancel,
    .modal-custom .btn-confirm {
        width: 100% !important;
        height: auto !important;
        padding: 0.875rem 1.5rem !important;
        font-size: 1rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0.5rem !important;
        border-radius: 10px !important;
        text-indent: 0 !important;
        overflow: visible !important;
    }
    
    .modal-custom .btn-cancel i,
    .modal-custom .btn-confirm i {
        margin: 0 !important;
        font-size: 1.1rem !important;
        position: static !important;
        transform: none !important;
    }

    .warning-icon {
        font-size: 3.5rem !important;
    }

    .modal-custom .modal-title {
        font-size: 1.25rem;
    }

    .modal-text {
        font-size: 1rem;
    }
}
</style>

<!-- Modal de confirmation -->
<div class="modal fade modal-custom" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirmation d'annulation</h5>
            </div>
            <div class="modal-body">
                <i class="fas fa-exclamation-triangle warning-icon"></i>
                <p class="modal-text">Êtes-vous sûr de vouloir annuler ce rendez-vous ?</p>
                <p class="modal-subtext">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
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
let confirmationModal = null;

// Fonction pour créer et afficher un toast
function showToast(message, type = 'success') {
    console.log('Création du toast:', message, type);
    
    const toastId = 'toast-' + Date.now();
    const bgColor = type === 'success' ? 'bg-success' : 'bg-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    const toastHtml = `
        <div id="${toastId}" class="toast text-white ${bgColor} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex align-items-center">
                <div class="toast-body">
                    <i class="fas ${icon} me-2"></i>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2" data-bs-dismiss="toast" aria-label="Fermer"></button>
            </div>
        </div>
    `;
    
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        console.error('Toast container not found');
        return;
    }
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    const toastElement = document.getElementById(toastId);
    if (!toastElement) {
        console.error('Toast element not found after creation');
        return;
    }
    
    const toast = new bootstrap.Toast(toastElement, {
        animation: true,
        autohide: true,
        delay: type === 'success' ? 3000 : 5000
    });
    
    toastElement.addEventListener('hidden.bs.toast', () => {
        console.log('Toast hidden, removing from DOM');
        toastElement.remove();
    });
    
    console.log('Showing toast');
    toast.show();
}

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

function annulerRendezVous(button) {
    console.log('Bouton annuler cliqué');
    currentButton = button;
    
    // S'assurer que la modal est initialisée
    if (!confirmationModal) {
        const modalElement = document.getElementById('confirmationModal');
        if (!modalElement) {
            console.error('Modal element not found');
            return;
        }
        confirmationModal = new bootstrap.Modal(modalElement);
    }
    
    console.log('ID du rendez-vous:', button.getAttribute('data-rdv-id'));
    confirmationModal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialiser la modal
    const modalElement = document.getElementById('confirmationModal');
    if (!modalElement) {
        console.error('Modal element not found on page load');
        return;
    }
    confirmationModal = new bootstrap.Modal(modalElement);

    // Gestionnaire pour le bouton de confirmation
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
            confirmationModal.hide();
            
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
            confirmationModal.hide();
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