// Fonction universelle pour afficher une popup de confirmation
// action: 'delete' ou 'statut'
// count: nombre de créneaux concernés
// type: 'indisponible', 'disponible', ou null

function showConfirmationPopup({ action, count, type }) {
    return new Promise((resolve) => {
        // Overlay sombre
        const overlay = document.createElement('div');
        overlay.style.position = 'fixed';
        overlay.style.top = '0';
        overlay.style.left = '0';
        overlay.style.width = '100vw';
        overlay.style.height = '100vh';
        overlay.style.zIndex = '9998';
        overlay.style.background = 'rgba(0,0,0,0.35)';
        document.body.appendChild(overlay);

        // Texte dynamique
        let message = '';
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
            message = `Êtes-vous sûr de vouloir effectuer cette action sur ${count} ${pluriel} ?`;
        }

        // Popup
        const confirmDialog = document.createElement('div');
        confirmDialog.classList.add('alert-popup');
    confirmDialog.style.position = 'fixed';
    confirmDialog.style.top = '16px';
    confirmDialog.style.right = '16px';
    confirmDialog.style.width = '400px';
    confirmDialog.style.zIndex = '9999';
        confirmDialog.innerHTML = `
            <div style="display: flex; align-items: center; gap: 12px; justify-content: center; margin-bottom: 12px;">
                <i class=\"fas fa-question-circle\"></i>
                <span class=\"message\">${message}</span>
            </div>
            <div class=\"alert-actions\" style=\"display: flex; gap: 12px; justify-content: center; margin-top: 18px;\">
                <button class=\"btn-confirm\">Confirmer</button>
                <button class=\"btn-cancel\">Annuler</button>
            </div>
        `;
        document.body.appendChild(confirmDialog);

        const closeDialog = (result) => {
            confirmDialog.classList.add('leaving');
            setTimeout(() => {
                confirmDialog.remove();
                overlay.remove();
            }, 300);
            resolve(result);
        };

        const btnConfirm = confirmDialog.querySelector('.btn-confirm');
        const btnCancel = confirmDialog.querySelector('.btn-cancel');
        btnConfirm.addEventListener('click', () => closeDialog(true));
        btnCancel.addEventListener('click', () => closeDialog(false));
        overlay.addEventListener('mousedown', (e) => {
            if (!confirmDialog.contains(e.target)) {
                closeDialog(false);
            }
        });
    });
}

window.showConfirmationPopup = showConfirmationPopup;
