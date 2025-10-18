document.addEventListener('DOMContentLoaded', function() {
    let contextMenu = null;

    // Créer le menu contextuel
    function createContextMenu(e, appointmentId) {
        // Supprimer tout menu contextuel existant
        removeContextMenu();

        // Créer le nouveau menu
        contextMenu = document.createElement('div');
        contextMenu.className = 'context-menu';
        contextMenu.innerHTML = `
            <div class="context-menu-item" data-action="edit">
                <i class="fas fa-edit"></i>
                Modifier
            </div>
            <div class="context-menu-separator"></div>
            <div class="context-menu-item" data-action="honore">
                <i class="fas fa-check-circle"></i>
                Marquer honoré
            </div>
            <div class="context-menu-item" data-action="absent">
                <i class="fas fa-user-times"></i>
                Marquer absent
            </div>
            <div class="context-menu-separator"></div>
            <div class="context-menu-item" data-action="cancel">
                <i class="fas fa-times"></i>
                Annuler
            </div>
            <div class="context-menu-separator"></div>
            <div class="context-menu-item" data-action="email">
                <i class="fas fa-envelope"></i>
                Envoyer un mail au patient
            </div>
        `;

        // Ajouter au document pour obtenir les dimensions
        contextMenu.style.visibility = 'hidden';
        document.body.appendChild(contextMenu);
        
        const menuWidth = contextMenu.offsetWidth;
        const menuHeight = contextMenu.offsetHeight;
        
        // Calculer la position en fonction du curseur
        let x = e.clientX + 2; // Légèrement décalé du curseur
        let y = e.clientY + 2;
        
        // Ajuster si le menu dépasse à droite
        if (x + menuWidth > window.innerWidth) {
            x = e.clientX - menuWidth;
        }
        
        // Ajuster si le menu dépasse en bas
        if (y + menuHeight > window.innerHeight) {
            y = e.clientY - menuHeight;
        }
        
        // Appliquer la position finale avec le scroll
        contextMenu.style.left = `${x + window.scrollX}px`;
        contextMenu.style.top = `${y + window.scrollY}px`;
        contextMenu.style.visibility = 'visible';

        // Ajouter au document
        document.body.appendChild(contextMenu);

        // Gestionnaires d'événements pour les actions
        contextMenu.querySelectorAll('.context-menu-item').forEach(item => {
            item.addEventListener('click', () => {
                const action = item.dataset.action;
                handleContextMenuAction(action, appointmentId);
                removeContextMenu();
            });
        });
    }

    // Supprimer le menu contextuel
    function removeContextMenu() {
        if (contextMenu) {
            contextMenu.remove();
            contextMenu = null;
        }
    }

    // Gérer les actions du menu
    function handleContextMenuAction(action, appointmentId) {
        console.log('Action:', action, 'AppointmentId:', appointmentId);
        switch(action) {
            case 'edit':
                console.log('Modifier le rendez-vous:', appointmentId);
                const appointmentElement = document.querySelector(`[data-id="${appointmentId}"]`);
                if (appointmentElement) {
                    const appointmentData = JSON.parse(appointmentElement.getAttribute('data-appointment'));
                    console.log('Données du rendez-vous à modifier:', appointmentData);
                    showEditForm(appointmentElement);
                }
                break;
            case 'honore':
                showConfirmationDialog({
                    title: 'Marquer comme honoré',
                    message: 'Confirmez-vous que le patient s\'est présenté à ce rendez-vous ?',
                    onConfirm: () => {
                        console.log('Marquer honoré pour ID:', appointmentId);
                        if (window.changerStatutRdv) {
                            window.changerStatutRdv(appointmentId, 'HONORE');
                        }
                    }
                });
                break;
            case 'absent':
                showConfirmationDialog({
                    title: 'Marquer comme absent',
                    message: 'Confirmez-vous que le patient ne s\'est PAS présenté à ce rendez-vous ?',
                    onConfirm: () => {
                        console.log('Marquer absent pour ID:', appointmentId);
                        if (window.changerStatutRdv) {
                            window.changerStatutRdv(appointmentId, 'ABSENT');
                        }
                    }
                });
                break;
            case 'cancel':
                showConfirmationDialog({
                    title: 'Annuler le rendez-vous',
                    message: 'Êtes-vous sûr de vouloir annuler ce rendez-vous ? Cette action est irréversible.',
                    onConfirm: () => {
                        console.log('Confirmation annulation pour ID:', appointmentId);
                        cancelAppointment(appointmentId);
                    }
                });
                break;
            case 'email':
                console.log('Envoyer un mail au patient:', appointmentId);
                // TODO: Implémenter l'envoi d'email
                break;
        }
    }

    // Écouter le clic droit sur les rendez-vous
    document.addEventListener('contextmenu', function(e) {
        console.log('Target:', e.target);
        const appointment = e.target.closest('.slot-cell.reserved');
        console.log('Appointment found:', appointment);
        if (appointment) {
            e.preventDefault();
            const appointmentId = appointment.getAttribute('data-id');
            console.log('AppointmentId:', appointmentId);
            if (appointmentId) {
                createContextMenu(e, appointmentId);
            }
        }
    });

    // Fermer le menu au clic ailleurs
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.context-menu')) {
            removeContextMenu();
        }
    });

    // Fermer le menu quand on sort de la fenêtre
    window.addEventListener('blur', removeContextMenu);
});