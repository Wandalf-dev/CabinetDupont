document.addEventListener('DOMContentLoaded', function() {
    let contextMenu = null;

    // Créer le menu contextuel
    function createContextMenu(e, appointmentId) {
        // Supprimer tout menu contextuel existant
        removeContextMenu();

        // Sur mobile, créer un overlay
        if (isMobile()) {
            const overlay = document.createElement('div');
            overlay.className = 'context-menu-overlay';
            overlay.addEventListener('click', removeContextMenu);
            document.body.appendChild(overlay);
        }

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
        
        // Calculer la position en fonction du curseur/touch
        let x, y;
        
        // Sur mobile, centrer le menu sur l'écran
        if (isMobile()) {
            x = Math.max(10, (window.innerWidth - menuWidth) / 2);
            y = Math.max(10, (window.innerHeight - menuHeight) / 2);
        } else {
            // Sur desktop, près du curseur
            x = e.clientX + 2;
            y = e.clientY + 2;
            
            // Ajuster si le menu dépasse à droite
            if (x + menuWidth > window.innerWidth) {
                x = e.clientX - menuWidth;
            }
            
            // Ajuster si le menu dépasse en bas
            if (y + menuHeight > window.innerHeight) {
                y = e.clientY - menuHeight;
            }
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
        // Supprimer l'overlay mobile
        const overlay = document.querySelector('.context-menu-overlay');
        if (overlay) {
            overlay.remove();
        }
    }

    // Gérer les actions du menu
    function handleContextMenuAction(action, appointmentId) {
        switch(action) {
            case 'edit':
                const appointmentElement = document.querySelector(`[data-id="${appointmentId}"]`);
                if (appointmentElement) {
                    const appointmentData = JSON.parse(appointmentElement.getAttribute('data-appointment'));
                    showEditForm(appointmentElement);
                }
                break;
            case 'honore':
                showConfirmationDialog({
                    title: 'Marquer comme honoré',
                    message: 'Confirmez-vous que le patient s\'est présenté à ce rendez-vous ?',
                    onConfirm: () => {
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
                        cancelAppointment(appointmentId);
                    }
                });
                break;
            case 'email':
                // TODO: Implémenter l'envoi d'email
                break;
        }
    }

    // Écouter le clic droit sur les rendez-vous
    // Détection du mode mobile
    function isMobile() {
        return window.innerWidth <= 768 || 'ontouchstart' in window;
    }

    // Gestion du clic droit (desktop)
    document.addEventListener('contextmenu', function(e) {
        const appointment = e.target.closest('.slot-cell.reserved');
        if (appointment) {
            e.preventDefault();
            const appointmentId = appointment.getAttribute('data-id');
            if (appointmentId) {
                createContextMenu(e, appointmentId);
            }
        }
    });

    // Gestion du clic/tap (mobile et desktop)
    document.addEventListener('click', function(e) {
        // Si on clique sur le menu contextuel, ne rien faire
        if (e.target.closest('.context-menu')) {
            return;
        }

        // Si on clique sur un rendez-vous
        const appointment = e.target.closest('.slot-cell.reserved');
        if (appointment) {
            // Sur mobile, ouvrir le menu contextuel
            if (isMobile()) {
                e.preventDefault();
                const appointmentId = appointment.getAttribute('data-id');
                if (appointmentId) {
                    createContextMenu(e, appointmentId);
                }
            }
            // Sur desktop, ne rien faire (le clic droit gère ça)
        } else {
            // Fermer le menu si on clique ailleurs
            removeContextMenu();
        }
    });

    // Fermer le menu quand on sort de la fenêtre
    window.addEventListener('blur', removeContextMenu);
});