document.addEventListener('DOMContentLoaded', function() {
    // Gestion du clic sur les boutons "Marquer indisponible"/"Rendre disponible"
    function initializeButtons() {
        console.log('Initialisation des boutons de disponibilité...');
        document.querySelectorAll('.btn-toggle-dispo').forEach(function(button) {
            console.log('Bouton trouvé:', button);
            button.addEventListener('click', function(e) {
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
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mise à jour du bouton et de l'affichage
                        const creneau = this.closest('tr');
                        const statutCell = creneau.querySelector('.statut-creneau');
                        
                        // Mettre à jour le texte et les classes en fonction du retour serveur
                        if (data.estIndisponible) {
                            // Créneau maintenant indisponible
                            this.textContent = 'Rendre disponible';
                            this.classList.remove('btn-warning');
                            this.classList.add('btn-success');
                            if (statutCell) {
                                statutCell.innerHTML = '<span class="badge indisponible">Indisponible</span>';
                            }
                        } else {
                            // Créneau maintenant disponible
                            this.textContent = 'Marquer indisponible';
                            this.classList.remove('btn-success');
                            this.classList.add('btn-warning');
                            if (statutCell) {
                                statutCell.innerHTML = '<span class="badge disponible">Disponible</span>';
                            }
                        }
                        
                        // Afficher le message de succès retourné par le serveur
                        showNotification('success', data.message);
                    } else {
                        showNotification('error', data.error || 'Une erreur est survenue');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showNotification('error', 'Une erreur est survenue lors de la communication avec le serveur');
                });
            });
        });
    }

    // Initialiser les boutons au chargement de la page
    initializeButtons();
});