function showConfirmationDialog({ title, message, onConfirm, onCancel }) {
    // Créer l'overlay
    const overlay = document.createElement('div');
    overlay.className = 'confirmation-overlay';

    // Créer la boîte de dialogue
    const dialog = document.createElement('div');
    dialog.className = 'confirmation-dialog';

    // Contenu de la boîte de dialogue
    dialog.innerHTML = `
        <div class="confirmation-title">${title}</div>
        <div class="confirmation-message">${message}</div>
        <div class="confirmation-buttons">
            <button class="confirmation-btn cancel-btn">Annuler</button>
            <button class="confirmation-btn confirm-btn">Confirmer</button>
        </div>
    `;

    // Ajouter la boîte de dialogue à l'overlay
    overlay.appendChild(dialog);
    document.body.appendChild(overlay);

    // Gestionnaires d'événements
    const confirmBtn = dialog.querySelector('.confirm-btn');
    const cancelBtn = dialog.querySelector('.cancel-btn');

    confirmBtn.addEventListener('click', () => {
        onConfirm();
        overlay.remove();
    });

    cancelBtn.addEventListener('click', () => {
        if (onCancel) onCancel();
        overlay.remove();
    });
}

// Fonction pour annuler un rendez-vous
async function cancelAppointment(appointmentId) {
    try {
        const response = await fetch('index.php?page=rendezvous&action=annuler', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `rdv_id=${appointmentId}`
        });

        const data = await response.json();
        
        if (data.success) {
            // Supprimer visuellement le rendez-vous
            const appointment = document.querySelector(`.slot-cell[data-id="${appointmentId}"]`);
            if (appointment) {
                appointment.remove();
            }
            
            // Afficher un message de succès
            showSuccessMessage('Le rendez-vous a été annulé avec succès.');
        } else {
            throw new Error(data.message || 'Erreur lors de l\'annulation du rendez-vous');
        }
    } catch (error) {
        showErrorMessage('Une erreur est survenue lors de l\'annulation du rendez-vous.');
        console.error('Erreur:', error);
    }
}