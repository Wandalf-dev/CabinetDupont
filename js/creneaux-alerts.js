function showConfirmDialog(message) {
    return new Promise((resolve) => {
        const confirmDialog = document.createElement('div');
        confirmDialog.classList.add('alert-popup');
        confirmDialog.innerHTML = `
            <i class="fas fa-question-circle"></i>
            <span class="message">${message}</span>
            <div class="alert-actions">
                <button class="btn-confirm">Confirmer</button>
                <button class="btn-cancel">Annuler</button>
            </div>
        `;
        document.body.appendChild(confirmDialog);

        const btnConfirm = confirmDialog.querySelector('.btn-confirm');
        const btnCancel = confirmDialog.querySelector('.btn-cancel');

        btnConfirm.addEventListener('click', () => {
            confirmDialog.remove();
            resolve(true);
        });

        btnCancel.addEventListener('click', () => {
            confirmDialog.remove();
            resolve(false);
        });
    });
}

function showAlert(message, type = 'success') {
    const alert = document.createElement('div');
    alert.classList.add('alert-popup', type);
    alert.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i>
        <span class="message">${message}</span>
    `;
    document.body.appendChild(alert);

    setTimeout(() => {
        if (alert && alert.parentElement) {
            alert.parentElement.removeChild(alert);
        }
    }, 5000);
}