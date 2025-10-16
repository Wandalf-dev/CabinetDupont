// Fonction pour créer et afficher une notification
function showNotification(message, type) {
    // Créer l'élément de notification
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;

    // Ajouter au document
    document.body.appendChild(notification);

    // Supprimer après 3 secondes
    setTimeout(() => {
        notification.style.animation = 'fadeOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Fonction pour afficher un message de succès
function showSuccessMessage(message) {
    showNotification(message, 'success');
}

// Fonction pour afficher un message d'erreur
function showErrorMessage(message) {
    showNotification(message, 'error');
}

// Ajouter les fonctions au scope global
window.showSuccessMessage = showSuccessMessage;
window.showErrorMessage = showErrorMessage;