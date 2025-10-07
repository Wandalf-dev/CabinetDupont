// Auto-hide des alertes après un délai
document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner toutes les alertes-popups
    const alerts = document.querySelectorAll('.alert-popup');
    
    // Pour chaque alerte
    alerts.forEach(alert => {
        // Supprimer l'alerte après l'animation
        setTimeout(() => {
            if (alert && alert.parentElement) {
                alert.parentElement.removeChild(alert);
            }
        }, 5000); // 5 secondes (4.5s d'attente + 0.5s d'animation)
    });
});