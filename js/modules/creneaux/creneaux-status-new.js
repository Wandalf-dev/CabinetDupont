document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour initialiser les boutons
    const initializeButtons = () => {
        console.log('Initialisation des boutons de disponibilité...');
        document.querySelectorAll('.btn-toggle-dispo').forEach(button => {
            console.log('Bouton trouvé:', button);
            
            // Éviter les doublons d'écouteurs d'événements
            button.removeEventListener('click', handleClick);
            button.addEventListener('click', handleClick);
        });
    };

    // Fonction de gestion du clic
    const handleClick = function(e) {
        e.preventDefault();
        const creneauId = this.dataset.creneauId;
        console.log('Clic sur le bouton pour le créneau:', creneauId);
            
        fetch('index.php?page=creneaux&action=toggleIndisponible', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: creneauId })
        })
        .then(response => {
            console.log('Réponse reçue:', response);
            return response.json();
        })
        .then(data => {
            console.log('Données reçues:', data);
            if (data.success) {
                // Mise à jour du bouton et de l'affichage
                const creneau = this.closest('.creneau-item');
                const statutDiv = creneau.querySelector('.creneau-statut');
                
                if (this.textContent.includes('Marquer indisponible')) {
                    // Passage à indisponible
                    this.textContent = 'Rendre disponible';
                    this.classList.remove('btn-warning');
                    this.classList.add('btn-success');
                    if (statutDiv) {
                        statutDiv.className = 'creneau-statut indisponible';
                        statutDiv.innerHTML = '<i class="fas fa-ban"></i>Indisponible';
                    }
                } else {
                    // Passage à disponible
                    this.textContent = 'Marquer indisponible';
                    this.classList.remove('btn-success');
                    this.classList.add('btn-warning');
                    if (statutDiv) {
                        statutDiv.className = 'creneau-statut disponible';
                        statutDiv.innerHTML = '<i class="fas fa-lock-open"></i>Disponible';
                    }
                }
                
                // Afficher une notification
                if (typeof showNotification === 'function') {
                    showNotification('success', data.message || 'Statut mis à jour avec succès');
                } else {
                    alert(data.message || 'Statut mis à jour avec succès');
                }
            } else {
                console.error('Erreur lors de la mise à jour:', data.error);
                if (typeof showNotification === 'function') {
                    showNotification('error', data.error || 'Une erreur est survenue');
                } else {
                    alert(data.error || 'Une erreur est survenue');
                }
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            if (typeof showNotification === 'function') {
                showNotification('error', 'Une erreur est survenue lors de la communication avec le serveur');
            } else {
                alert('Une erreur est survenue lors de la communication avec le serveur');
            }
        });
    };

    // Initialiser les boutons au chargement
    initializeButtons();

    // Observer les changements dans le DOM pour les accordéons
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.addedNodes.length) {
                console.log('Changements dans le DOM détectés, réinitialisation des boutons...');
                initializeButtons();
            }
        });
    });

    // Observer les changements dans les accordéons
    const accordions = document.querySelectorAll('.accordion-collapse');
    accordions.forEach(accordion => {
        observer.observe(accordion, { childList: true, subtree: true });
    });
});