// Singleton pour gérer toutes les alertes
const AlertManager = {
    show(message, type = 'success', duration = 5000) {
        // Créer l'alerte
        const alert = document.createElement('div');
        alert.className = `flash-message ${type}`;
        
        // Ajouter l'icône
        const icon = document.createElement('i');
        icon.className = `fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}`;
        
        // Ajouter le message
        const messageSpan = document.createElement('span');
        messageSpan.className = 'message';
        messageSpan.textContent = message;
        
        // Assembler
        alert.appendChild(icon);
        alert.appendChild(messageSpan);
        
        // Ajouter au DOM
        document.body.appendChild(alert);
        
        // Retirer les anciennes alertes si présentes
        document.querySelectorAll('.flash-message').forEach(oldAlert => {
            if (oldAlert !== alert) {
                oldAlert.remove();
            }
        });
        
        // Force un reflow pour assurer le début propre de l'animation
        alert.offsetHeight;

        // Programmer la disparition
        setTimeout(() => {
            alert.classList.add('hide');
            // Écouter la fin de l'animation avant de supprimer
            alert.addEventListener('animationend', function(e) {
                if (e.animationName === 'slideRightOut') {
                    alert.remove();
                }
            }, { once: true });
        }, duration);

        return alert;
    },

    success(message, duration = 5000) {
        return this.show(message, 'success', duration);
    },

    error(message, duration = 5000) {
        return this.show(message, 'error', duration);
    }
};

// Export global
window.AlertManager = AlertManager;