// Import universel
// Assure-toi que confirmation-popup.js est chargé dans la page
function showConfirmDialog(message) {
    return window.showConfirmationPopup ? window.showConfirmationPopup({ action: 'delete', count: 1 }) : Promise.resolve(false);
}

function showAlert(message, type = 'success') {
    const alert = document.createElement('div');
    alert.className = `flash-message ${type}`;
    
    alert.innerHTML = `
        <span class="message">${message}</span>
    `;
    document.body.appendChild(alert);

    setTimeout(() => {
        alert.classList.add('leaving');
        setTimeout(() => {
            if (alert.parentElement) {
                alert.parentElement.removeChild(alert);
            }
        }, 300);
    }, 5000);
}