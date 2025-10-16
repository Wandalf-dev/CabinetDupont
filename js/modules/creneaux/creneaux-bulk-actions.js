document.addEventListener('DOMContentLoaded', function() {

    const initCreneauxBulkActions = (periodeElement) => {
        const selectAllCheckbox = periodeElement.querySelector('.select-all-checkbox');
        const creneauCheckboxes = periodeElement.querySelectorAll('.creneau-select');
        const markUnavailableButton = periodeElement.querySelector('.mark-unavailable-selected');
        
        // Mettre à jour le bouton de marquage multiple
        const updateMarkButton = () => {
            const selectedCheckboxes = periodeElement.querySelectorAll('.creneau-select:checked');
            const selectedCount = selectedCheckboxes.length;

            if (selectedCount > 0) {
                markUnavailableButton.classList.add('active');
                markUnavailableButton.disabled = false;

                // Vérifier si tous les créneaux sélectionnés sont indisponibles
                const allIndisponibles = Array.from(selectedCheckboxes).every(checkbox => {
                    const creneauItem = checkbox.closest('.creneau-item');
                    return creneauItem.querySelector('.creneau-statut.indisponible');
                });

                // Mettre à jour le texte et la classe du bouton
                const pluriel = selectedCount > 1 ? 'créneaux' : 'créneau';
                if (allIndisponibles) {
                    markUnavailableButton.innerHTML = `<i class="fas fa-check"></i>&nbsp;Rendre ${selectedCount} ${pluriel} disponible${selectedCount > 1 ? 's' : ''}`;
                    markUnavailableButton.classList.remove('warning');
                    markUnavailableButton.classList.add('success');
                } else {
                    markUnavailableButton.innerHTML = `<i class="fas fa-ban"></i>&nbsp;Marquer ${selectedCount} ${pluriel} indisponible${selectedCount > 1 ? 's' : ''}`;
                    markUnavailableButton.classList.remove('success');
                    markUnavailableButton.classList.add('warning');
                }
            } else {
                markUnavailableButton.classList.remove('active', 'warning', 'success');
                markUnavailableButton.disabled = true;
                markUnavailableButton.innerHTML = '<i class="fas fa-ban"></i>&nbsp;Marquer indisponible';
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
            updateMarkButton();
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
                
                updateMarkButton();
            });
        });

        // Gérer le clic sur le bouton de marquage
        markUnavailableButton?.addEventListener('click', async function() {
            const selectedCheckboxes = periodeElement.querySelectorAll('.creneau-select:checked');
            const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.dataset.id);
            
            if (!selectedIds.length) return;

            // Vérifier si tous les créneaux sélectionnés sont indisponibles
            const allIndisponibles = Array.from(selectedCheckboxes).every(checkbox => {
                const creneauItem = checkbox.closest('.creneau-item');
                return creneauItem.querySelector('.creneau-statut.indisponible');
            });

            // Configurer le message de confirmation en fonction du statut
            const count = selectedIds.length;
            const action = allIndisponibles ? "rendre disponibles" : "marquer indisponibles";
            const confirmation = await showConfirmDialog(
                `Êtes-vous sûr de vouloir ${action} ${count} créneau${count > 1 ? 'x' : ''} ?`
            );

            if (confirmation) {
                // TODO: Ajouter l'appel à l'API pour mettre à jour les créneaux
            }
        });
    };

    // Initialiser la gestion des actions en masse pour chaque période
    document.querySelectorAll('.periode-collapse').forEach(initCreneauxBulkActions);
});