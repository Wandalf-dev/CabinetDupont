document.addEventListener('DOMContentLoaded', function() {
    const editOverlay = document.querySelector('.edit-appointment-overlay');
    const editForm = document.querySelector('#edit-appointment-form');
    
    // Fonction pour générer les options de temps par tranches de 30 minutes
    function generateTimeOptions(startHour = 8, endHour = 20) {
        const select = document.querySelector('#edit-appointment-time');
        select.innerHTML = ''; // Vider les options existantes
        
        for (let hour = startHour; hour < endHour; hour++) {
            // Option pour l'heure pile
            const optionHour = document.createElement('option');
            optionHour.value = `${hour.toString().padStart(2, '0')}:00`;
            optionHour.textContent = `${hour.toString().padStart(2, '0')}:00`;
            select.appendChild(optionHour);
            
            // Option pour la demi-heure
            const optionHalfHour = document.createElement('option');
            optionHalfHour.value = `${hour.toString().padStart(2, '0')}:30`;
            optionHalfHour.textContent = `${hour.toString().padStart(2, '0')}:30`;
            select.appendChild(optionHalfHour);
        }
    }
    
    // Fonction pour ouvrir le formulaire de modification
    window.showEditForm = function(appointment) {
        console.log('Ouverture du formulaire de modification');
        const overlay = document.querySelector('.edit-appointment-overlay');
        const form = overlay.querySelector('form');
        const appointmentData = JSON.parse(appointment.getAttribute('data-appointment'));
        console.log('Données du rendez-vous:', appointmentData);
        console.log('Durée récupérée:', appointmentData.duration);
        
        // Remplir les détails du rendez-vous
        overlay.querySelector('.patient-name').textContent = appointmentData.patient || 'Non défini';
        overlay.querySelector('.service-name').textContent = appointmentData.service || 'Non défini';
        
        // Gérer la durée
        const durationElement = overlay.querySelector('.appointment-duration');
        const duration = appointmentData.duration || 30;
        
        if (durationElement) {
            durationElement.textContent = duration;
        }
        
        // Générer et remplir les options de temps
        generateTimeOptions();
        
        // Remplir la date et l'heure actuelles
        document.querySelector('#edit-appointment-date').value = appointmentData.date;
        document.querySelector('#edit-appointment-time').value = appointmentData.time;
        document.querySelector('#edit-appointment-id').value = appointmentData.id;
        
        // Afficher l'overlay
        overlay.style.display = 'flex';
    }
    
    // Gestionnaire pour le bouton annuler
    document.querySelector('.edit-appointment-form .cancel').addEventListener('click', function() {
        editOverlay.style.display = 'none';
    });
    
    // Gestionnaire pour la soumission du formulaire
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const appointmentId = document.querySelector('#edit-appointment-id').value;
        const newDate = document.querySelector('#edit-appointment-date').value;
        const newTime = document.querySelector('#edit-appointment-time').value;
        
        console.log('Soumission du formulaire avec:', {
            appointmentId,
            newDate,
            newTime
        });
        
        // Appeler l'API pour modifier le rendez-vous
        fetch('index.php?page=rendezvous&action=modifier', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `rdv_id=${appointmentId}&nouvelle_date=${newDate}&nouvelle_heure=${newTime}`
        })
        .then(response => {
            console.log('Status de la réponse:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Réponse du serveur:', data);
            if (data.success) {
                // Fermer le formulaire
                editOverlay.style.display = 'none';
                
                // Afficher l'alerte de succès
                AlertManager.show('Le rendez-vous a bien été modifié', 'success');
                
                // Émettre un événement pour notifier que les rendez-vous doivent être rechargés
                document.dispatchEvent(new CustomEvent('appointmentUpdated', {
                    detail: { appointmentId: appointmentId }
                }));
            } else {
                AlertManager.show(data.message || 'Erreur lors de la modification du rendez-vous', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            AlertManager.show('Une erreur est survenue lors de la modification du rendez-vous', 'error');
        });
    });
    
    // Exposer la fonction pour pouvoir l'appeler depuis le menu contextuel
    window.showEditForm = showEditForm;
});