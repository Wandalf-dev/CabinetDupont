
document.addEventListener('DOMContentLoaded', function() {
    const initCreneauxSelection = (periodeElement) => {
        const selectAllCheckbox = periodeElement.querySelector('.select-all-checkbox');
        const creneauCheckboxes = periodeElement.querySelectorAll('.creneau-select');
        const deleteSelectedButton = periodeElement.querySelector('.delete-selected');
        
        // Mettre à jour le bouton de suppression multiple
        const updateDeleteButton = () => {
            const selectedCount = periodeElement.querySelectorAll('.creneau-select:checked').length;
            if (selectedCount > 0) {
                deleteSelectedButton.classList.add('active');
                deleteSelectedButton.disabled = false;
                const pluriel = selectedCount > 1 ? 'créneaux' : 'créneau';
                deleteSelectedButton.textContent = `Supprimer la sélection (${selectedCount} ${pluriel})`;
            } else {
                deleteSelectedButton.classList.remove('active');
                deleteSelectedButton.disabled = true;
                deleteSelectedButton.textContent = 'Supprimer la sélection';
            }
        };

        // Gérer la sélection de tous les créneaux
        selectAllCheckbox?.addEventListener('change', function() {
            creneauCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                const creneauItem = checkbox.closest('.creneau-item');
                if (creneauItem) {
                    creneauItem.classList.toggle('selected', this.checked);
                }
            });
            updateDeleteButton();
        });

        // Gérer la sélection individuelle
        creneauCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const creneauItem = this.closest('.creneau-item');
                creneauItem.classList.toggle('selected', this.checked);
                
                // Mettre à jour la case "tout sélectionner"
                const allChecked = [...creneauCheckboxes].every(cb => cb.checked);
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = allChecked;
                }
                
                updateDeleteButton();
            });
        });

        // Gérer la suppression multiple
        deleteSelectedButton?.addEventListener('click', async function() {
            const selectedIds = [...periodeElement.querySelectorAll('.creneau-select:checked')]
                .map(checkbox => checkbox.dataset.id);

            if (!selectedIds.length) return;

            // Créer une boîte de dialogue de confirmation personnalisée
            const confirmDialog = document.createElement('div');
            confirmDialog.classList.add('alert-popup');
                const pluriel = selectedIds.length > 1 ? 'créneaux' : 'créneau';
                confirmDialog.innerHTML = `
                <i class="fas fa-question-circle"></i>
                <span class="message">Êtes-vous sûr de vouloir supprimer ${selectedIds.length} ${pluriel} ?</span>
                <div class="alert-actions">
                    <button class="btn-confirm">Confirmer</button>
                    <button class="btn-cancel">Annuler</button>
                </div>
            `;
            document.body.appendChild(confirmDialog);

            // Gérer la confirmation
            const proceed = await new Promise(resolve => {
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

            if (proceed) {
                try {
                    const response = await fetch('index.php?page=creneaux&action=deleteMultiple', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ ids: selectedIds })
                    });

                    if (response.ok) {
                        // Supprimer les éléments du DOM
                        selectedIds.forEach(id => {
                            const creneauItem = periodeElement.querySelector(`[data-id="${id}"]`).closest('.creneau-item');
                            creneauItem.remove();
                        });
                        
                        // Réinitialiser la sélection
                        if (selectAllCheckbox) {
                            selectAllCheckbox.checked = false;
                        }
                        updateDeleteButton();

                        // Mettre à jour les compteurs dans le bandeau de la période
                        const periodeSection = periodeElement.closest('.periode-section');
                        if (periodeSection) {
                            const periodeHeader = periodeSection.querySelector('.periode-header');
                            const periodeBadge = periodeHeader?.querySelector('.badge');
                            
                            if (periodeBadge) {
                                const remainingCreneaux = periodeElement.querySelectorAll('.creneau-item').length;
                                periodeBadge.textContent = `${remainingCreneaux} créneaux`;
                            }

                            // Mettre à jour le compteur global de la date
                            const accordionDate = periodeSection.closest('.accordion-date');
                            const dateBadge = accordionDate?.querySelector('.accordion-header .badge');
                            
                            if (dateBadge) {
                                const totalCreneaux = accordionDate.querySelectorAll('.creneau-item').length;
                                dateBadge.textContent = `${totalCreneaux} créneaux`;
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
                        
                        // Afficher un message de succès personnalisé
                        const successAlert = document.createElement('div');
                        successAlert.classList.add('alert-popup', 'success');
                        successAlert.innerHTML = `
                            <i class="fas fa-check-circle"></i>
                            <span class="message">Les créneaux sélectionnés ont été supprimés avec succès.</span>
                        `;
                        document.body.appendChild(successAlert);
                        
                        // Auto-supprimer l'alerte après 5 secondes
                        setTimeout(() => {
                            if (successAlert && successAlert.parentElement) {
                                successAlert.parentElement.removeChild(successAlert);
                            }
                        }, 5000);
                    } else {
                        const data = await response.json();
                        if (data.errors) {
                            const errorAlert = document.createElement('div');
                            errorAlert.classList.add('alert-popup', 'error');
                            errorAlert.innerHTML = `
                                <i class="fas fa-exclamation-circle"></i>
                                <span class="message">Erreurs lors de la suppression :<br>${data.errors.join('<br>')}</span>
                            `;
                            document.body.appendChild(errorAlert);
                            setTimeout(() => errorAlert.remove(), 5000);
                        } else if (!data.success) {
                            const errorAlert = document.createElement('div');
                            errorAlert.classList.add('alert-popup', 'error');
                            errorAlert.innerHTML = `
                                <i class="fas fa-exclamation-circle"></i>
                                <span class="message">${data.message || 'Erreur lors de la suppression'}</span>
                            `;
                            document.body.appendChild(errorAlert);
                            setTimeout(() => errorAlert.remove(), 5000);
                        }
                    }
                } catch (error) {
                    console.error('Erreur détaillée:', error);
                    const errorAlert = document.createElement('div');
                    errorAlert.classList.add('alert-popup', 'error');
                    errorAlert.innerHTML = `
                        <i class="fas fa-exclamation-circle"></i>
                        <span class="message">Une erreur est survenue lors de la suppression des créneaux.</span>
                    `;
                    document.body.appendChild(errorAlert);
                    setTimeout(() => errorAlert.remove(), 5000);
                }
            }
        });
    };

    // Initialiser la sélection pour chaque période
    document.querySelectorAll('.periode-collapse').forEach(initCreneauxSelection);
});