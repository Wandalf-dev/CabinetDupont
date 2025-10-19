// Fonction universelle pour afficher une popup de confirmation
// Accepte soit une chaîne de caractères, soit un objet { action, count, type }
// action: 'delete' ou 'statut'
// count: nombre de créneaux concernés
// type: 'indisponible', 'disponible', ou null

function showConfirmationPopup(options) {
    return new Promise((resolve) => {
        // Overlay sombre
        const overlay = document.createElement('div');
        overlay.className = 'modal-overlay';
        document.body.appendChild(overlay);

        // Texte dynamique
        let message = '';
        
        // Si c'est une chaîne, l'utiliser directement
        if (typeof options === 'string') {
            message = options;
        } else {
            const { action, count, type } = options;
            const pluriel = count > 1 ? 'créneaux' : 'créneau';
            
            if (action === 'delete') {
                message = `Êtes-vous sûr de vouloir supprimer ${count} ${pluriel} ?`;
            } else if (action === 'statut') {
                if (type === 'indisponible') {
                    message = `Êtes-vous sûr de vouloir marquer ${count} ${pluriel} comme indisponible${count > 1 ? 's' : ''} ?`;
                } else if (type === 'disponible') {
                    message = `Êtes-vous sûr de vouloir rendre ${count} ${pluriel} disponible${count > 1 ? 's' : ''} ?`;
                } else {
                    message = `Êtes-vous sûr de vouloir changer le statut de ${count} ${pluriel} ?`;
                }
            } else {
                message = `Êtes-vous sûr de vouloir effectuer cette action sur ${count || 1} ${pluriel} ?`;
            }
        }

        // Popup
        const confirmDialog = document.createElement('div');
        confirmDialog.className = 'alert-popup';
        confirmDialog.innerHTML = `
            <div style="display: flex; align-items: center; gap: 12px; justify-content: center; margin-bottom: 12px;">
                <i class="fas fa-question-circle"></i>
                <span class="message">${message}</span>
            </div>
            <div class="alert-actions">
                <button class="btn-confirm">Confirmer</button>
                <button class="btn-cancel">Annuler</button>
            </div>
        `;
        overlay.appendChild(confirmDialog);

        const closeDialog = (result) => {
            overlay.classList.add('leaving');
            setTimeout(() => {
                overlay.remove();
            }, 300);
            resolve(result);
        };

        const btnConfirm = confirmDialog.querySelector('.btn-confirm');
        const btnCancel = confirmDialog.querySelector('.btn-cancel');
        btnConfirm.addEventListener('click', () => closeDialog(true));
        btnCancel.addEventListener('click', () => closeDialog(false));
        overlay.addEventListener('mousedown', (e) => {
            if (confirmDialog.contains(e.target)) {
                return; // Ne pas fermer si on clique dans la popup
            }
            closeDialog(false);
        });
    });
}

window.showConfirmationPopup = showConfirmationPopup;
