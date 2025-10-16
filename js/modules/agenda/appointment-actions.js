// Fonction pour calculer la position verticale d'un créneau
function calculatePosition(time) {
    if (!time) {
        console.error('Temps non défini dans calculatePosition');
        return 0;
    }
    
    const TIME_FORMAT = /^([0-9]{2}):([0-9]{2})$/;
    
    try {
        const match = time.match(TIME_FORMAT);
        if (!match) {
            console.error('Format de temps invalide:', time);
            return 0;
        }
        
        const hours = parseInt(match[1]);
        const minutes = parseInt(match[2]);
        
        if (isNaN(hours) || isNaN(minutes)) {
            console.error('Valeurs de temps invalides:', hours, minutes);
            return 0;
        }
        
        const totalMinutes = hours * 60 + minutes;
        const startOfDay = 8 * 60; // 8h00 en minutes
        const offsetMinutes = totalMinutes - startOfDay;
        
        return Math.max(0, offsetMinutes); // position en pixels (1 minute = 1 pixel)
    } catch (error) {
        console.error('Erreur dans calculatePosition:', error);
        return 0;
    }
}

function showConfirmationDialog({ title, message, onConfirm, onCancel }) {
    // Créer l'overlay
    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay';

    // Créer la boîte de dialogue
    const dialog = document.createElement('div');
    dialog.className = 'alert-popup';

    // Contenu de la boîte de dialogue
    dialog.innerHTML = `
        <i class="fas fa-question-circle"></i>
        <div>
            <strong>${title}</strong>
            <p>${message}</p>
        </div>
        <div class="alert-actions">
            <button class="btn-cancel">Annuler</button>
            <button class="btn-confirm">Confirmer</button>
        </div>
    `;

    // Ajouter la boîte de dialogue à l'overlay
    overlay.appendChild(dialog);
    document.body.appendChild(overlay);

    // Gestionnaires d'événements
    const confirmBtn = dialog.querySelector('.btn-confirm');
    const cancelBtn = dialog.querySelector('.btn-cancel');

    confirmBtn.addEventListener('click', async () => {
        try {
            await onConfirm();
        } finally {
            overlay.remove();
        }
    });

    cancelBtn.addEventListener('click', async () => {
        try {
            if (onCancel) await onCancel();
        } finally {
            overlay.remove();
        }
    });
}

// Fonction pour annuler un rendez-vous
async function cancelAppointment(appointmentId) {
    let appointment;
    try {
        console.log('=== Début annulation rendez-vous ===');
        console.log('ID du rendez-vous:', appointmentId);
        
        // Vérifier d'abord si l'élément existe avant de faire la requête
        appointment = document.querySelector(`[data-id="${appointmentId}"]`);
        console.log('Élément trouvé:', appointment);
        console.log('Classes de l\'élément:', appointment ? appointment.className : 'non trouvé');
        console.log('Attributs de l\'élément:', appointment ? {
            'data-id': appointment.getAttribute('data-id'),
            'data-start-time': appointment.getAttribute('data-start-time'),
            'style': appointment.getAttribute('style')
        } : 'non trouvé');
        
        if (!appointment) {
            throw new Error('Rendez-vous introuvable dans le planning');
        }

        console.log('=== Envoi requête annulation ===');
        const response = await fetch('index.php?page=rendezvous&action=annuler', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `rdv_id=${appointmentId}`
        });
        console.log('Statut de la réponse:', response.status);

        if (!response.ok) {
            throw new Error(`Erreur serveur: ${response.status}`);
        }

        const data = await response.json();
        console.log('Réponse du serveur:', data);
        
        if (!data.success) {
            throw new Error(data.message || 'Erreur lors de l\'annulation');
        }

        // Vérifier que l'élément est toujours dans le DOM
        if (!appointment.isConnected) {
            console.error('L\'élément du rendez-vous n\'est plus dans le DOM:', appointmentId);
            throw new Error('Élément du rendez-vous non trouvé');
        }

        // On ne vérifie plus les attributs data-* car on fait confiance à la réponse du serveur
        console.log('Élément du rendez-vous trouvé, envoi de la requête d\'annulation...');

        console.log('Mise à jour de l\'interface pour le rendez-vous:', appointmentId);

        // Si on arrive ici, c'est que l'annulation a réussi
            // Supprimer l'élément du rendez-vous
            appointment.remove();
            
            // Nettoyer immédiatement tous les créneaux indisponibles
            if (window.resetUnavailableSlots) {
                window.resetUnavailableSlots();
            }

            // Attendre un court instant pour permettre au DOM de se mettre à jour
            setTimeout(() => {
                // Déclencher une mise à jour complète du calendrier
                document.dispatchEvent(new CustomEvent('appointmentUpdated', {
                    detail: {
                        type: 'cancel',
                        appointmentId: appointmentId
                    }
                }));
                
                // Forcer un rechargement des créneaux disponibles
                const viewStartDate = document.querySelector('.week-view.active, .day-view.active')
                    .querySelector('.day-column').getAttribute('data-date');
                const viewEndDate = new Date(viewStartDate);
                viewEndDate.setDate(viewEndDate.getDate() + 6);
                
                if (window.loadUnavailableSlots) {
                    window.loadUnavailableSlots(
                        viewStartDate,
                        viewEndDate.toISOString().split('T')[0]
                    );
                }
            }, 100);
        
        // Créer et afficher la notification de succès
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert-popup success';
        alertDiv.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <div>
                <strong>Succès!</strong>
                <p>Le rendez-vous a été annulé avec succès.</p>
            </div>
        `;
        document.body.appendChild(alertDiv);

            // Supprimer la notification après 5 secondes
        setTimeout(() => {
            if (alertDiv && alertDiv.parentElement) {
                alertDiv.classList.add('leaving');
                setTimeout(() => {
                    alertDiv.parentElement.removeChild(alertDiv);
                }, 300);
            }
        }, 5000);

    } catch (error) {
        console.error('Erreur complète:', error);
        console.error('Message d\'erreur:', error.message);
        
        // Afficher notification d'erreur
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert-popup error';
        alertDiv.innerHTML = `
            <i class="fas fa-exclamation-circle"></i>
            <div>
                <strong>Erreur!</strong>
                <p>Une erreur est survenue lors de l'annulation du rendez-vous.</p>
            </div>
        `;
        document.body.appendChild(alertDiv);

        // Supprimer la notification après 5 secondes
        setTimeout(() => {
            if (alertDiv && alertDiv.parentElement) {
                alertDiv.classList.add('leaving');
                setTimeout(() => {
                    alertDiv.parentElement.removeChild(alertDiv);
                }, 300);
            }
        }, 5000);
    }
}