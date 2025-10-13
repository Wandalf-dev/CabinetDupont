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
                deleteSelectedButton.textContent = `Supprimer la sélection (${selectedCount})`;
            } else {
                deleteSelectedButton.classList.remove('active');
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

            if (confirm(`Êtes-vous sûr de vouloir supprimer ${selectedIds.length} créneau(x) ?`)) {
                try {
                    const response = await fetch('index.php?page=creneaux&action=delete-multiple', {
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
                        
                        // Afficher un message de succès
                        alert('Les créneaux sélectionnés ont été supprimés avec succès.');
                    } else {
                        throw new Error('Erreur lors de la suppression');
                    }
                } catch (error) {
                    alert('Une erreur est survenue lors de la suppression des créneaux.');
                    console.error(error);
                }
            }
        });
    };

    // Initialiser la sélection pour chaque période
    document.querySelectorAll('.periode-collapse').forEach(initCreneauxSelection);
});