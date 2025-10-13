document.addEventListener('DOMContentLoaded', function() {
    // Gestionnaire pour les boutons de suppression individuels
    document.querySelectorAll('.btn-delete-creneau').forEach(button => {
        button.addEventListener('click', async function(e) {
            const id = this.dataset.id;
            
            const result = await showConfirmDialog('Êtes-vous sûr de vouloir supprimer ce créneau ?');
            
            if (result) {
                try {
                    const response = await fetch(`index.php?page=creneaux&action=delete&id=${id}`, {
                        method: 'POST'
                    });

                    if (response.ok) {
                        // Supprimer l'élément du DOM
                        const creneauElement = button.closest('.creneau-item');
                        if (creneauElement) {
                            const periodeElement = creneauElement.closest('.periode-collapse');
                            creneauElement.remove();

                            // Mettre à jour les compteurs
                            const periodeSection = periodeElement.closest('.periode-section');
                            if (periodeSection) {
                                const periodeHeader = periodeSection.querySelector('.periode-header');
                                const periodeBadge = periodeHeader?.querySelector('.badge');
                            
                                if (periodeBadge) {
                                    const remainingCreneaux = periodeElement.querySelectorAll('.creneau-item').length;
                                    periodeBadge.textContent = `${remainingCreneaux} ${remainingCreneaux > 1 ? 'créneaux' : 'créneau'}`;
                                }

                                // Mettre à jour le compteur global de la date
                                const accordionDate = periodeSection.closest('.accordion-date');
                                const dateBadge = accordionDate?.querySelector('.accordion-header .badge');
                                
                                if (dateBadge) {
                                    const totalCreneaux = accordionDate.querySelectorAll('.creneau-item').length;
                                    dateBadge.textContent = `${totalCreneaux} ${totalCreneaux > 1 ? 'créneaux' : 'créneau'}`;
                                }

                                // Masquer la section période si elle est vide
                                if (periodeElement.querySelectorAll('.creneau-item').length === 0) {
                                    periodeSection.style.display = 'none';
                                    
                                    // Si c'était la dernière période, masquer aussi la section date
                                    const visiblePeriodes = accordionDate.querySelectorAll('.periode-section:not([style*="display: none"])').length;
                                    if (visiblePeriodes === 0) {
                                        accordionDate.style.display = 'none';
                                    }
                                }
                            }

                            showAlert('Le créneau a été supprimé avec succès.', 'success');
                        }
                    } else {
                        throw new Error('Erreur lors de la suppression');
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    showAlert('Une erreur est survenue lors de la suppression du créneau.', 'error');
                }
            }
        });
    });
});