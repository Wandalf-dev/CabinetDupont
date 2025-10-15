document.addEventListener('DOMContentLoaded', function() {
    const initCreneauxIndisponibilite = (periodeElement) => {
        const markUnavailableButton = periodeElement.querySelector('.mark-unavailable-selected');

        // Mettre à jour le bouton d'indisponibilité multiple
        const updateMarkUnavailableButton = () => {
            const selectedCheckboxes = periodeElement.querySelectorAll('.creneau-select:checked');
            const selectedCount = selectedCheckboxes.length;

            // Nettoyer complètement le bouton
            markUnavailableButton.className = '';
            markUnavailableButton.removeAttribute('disabled');
            markUnavailableButton.style.pointerEvents = '';
            markUnavailableButton.style.opacity = '';
            // Ajouter les classes de base
            markUnavailableButton.classList.add('mark-unavailable-selected', 'btn');
            // Supprimer toutes les classes de couleur personnalisées
            markUnavailableButton.classList.remove('btn-creneau-neutral', 'btn-creneau-warning', 'btn-creneau-success', 'btn-creneau-danger', 'btn-flashy', 'active');

            if (selectedCount === 0) {
                markUnavailableButton.classList.add('btn-creneau-neutral');
                markUnavailableButton.innerHTML = '<i class="fas fa-ban"></i>&nbsp;Marquer indisponible';
                return;
            }

            let disponibleCount = 0;
            let indisponibleCount = 0;
            selectedCheckboxes.forEach(checkbox => {
                const creneauItem = checkbox.closest('.creneau-item');
                const statut = creneauItem.querySelector('.creneau-statut');
                if (statut && statut.classList.contains('indisponible')) {
                    indisponibleCount++;
                } else {
                    disponibleCount++;
                }
            });

            if (disponibleCount > 0 && indisponibleCount > 0) {
                markUnavailableButton.classList.add('btn-creneau-danger', 'btn-flashy');
                markUnavailableButton.classList.remove('active');
                markUnavailableButton.innerHTML = '<i class="fas fa-exclamation-triangle"></i>&nbsp;Sélection mixte non autorisée';
                markUnavailableButton.removeAttribute('disabled');
                markUnavailableButton.style.pointerEvents = 'none';
                markUnavailableButton.style.opacity = '0.5';
                return;
            }
            if (indisponibleCount === selectedCount) {
                markUnavailableButton.classList.add('btn-creneau-success', 'active');
                const pluriel = selectedCount > 1 ? 'créneaux' : 'créneau';
                markUnavailableButton.innerHTML = `<i class="fas fa-check"></i>&nbsp;Rendre ${selectedCount} ${pluriel} disponible${selectedCount > 1 ? 's' : ''}`;
                markUnavailableButton.removeAttribute('disabled');
                markUnavailableButton.style.pointerEvents = 'auto';
                markUnavailableButton.style.opacity = '1';
                return;
            }
            if (disponibleCount === selectedCount) {
                markUnavailableButton.classList.add('btn-creneau-warning', 'active');
                const pluriel = selectedCount > 1 ? 'créneaux' : 'créneau';
                markUnavailableButton.innerHTML = `<i class="fas fa-ban"></i>&nbsp;Marquer ${selectedCount} ${pluriel} indisponible${selectedCount > 1 ? 's' : ''}`;
                markUnavailableButton.removeAttribute('disabled');
                markUnavailableButton.style.pointerEvents = 'auto';
                markUnavailableButton.style.opacity = '1';
                return;
            }
        };

        // Observer les changements de sélection pour mettre à jour le bouton
        const checkboxes = periodeElement.querySelectorAll('.creneau-select');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const creneauItem = checkbox.closest('.creneau-item');
                if (creneauItem) {
                    creneauItem.classList.toggle('selected', checkbox.checked);
                }
                updateMarkUnavailableButton();
            });
        });

        // Gérer la case "Tout sélectionner"
        const selectAllCheckbox = periodeElement.querySelector('.select-all-checkbox');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', () => {
                const creneaux = Array.from(periodeElement.querySelectorAll('.creneau-select'));
                creneaux.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                    const creneauItem = checkbox.closest('.creneau-item');
                    if (creneauItem) {
                        creneauItem.classList.toggle('selected', checkbox.checked);
                    }
                });
                updateMarkUnavailableButton();
            });
        }

        // Gérer les boutons individuels de toggle disponibilité
        periodeElement.querySelectorAll('.btn-toggle-dispo').forEach(button => {
            button.addEventListener('click', async function() {
                const creneauItem = button.closest('.creneau-item');
                const creneauId = creneauItem.querySelector('.creneau-select').dataset.id;
                
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    
                    const response = await fetch('index.php?page=creneaux&action=toggleIndisponible', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ id: creneauId })
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        const wasIndisponible = creneauItem.querySelector('.creneau-statut.indisponible') !== null;
                        const willBeIndisponible = !wasIndisponible;
                        
                        // Mise à jour du statut
                        const statusDiv = creneauItem.querySelector('.creneau-info .creneau-statut');
                        if (statusDiv) {
                            const newStatus = willBeIndisponible ? 'indisponible' : 'disponible';
                            const icon = willBeIndisponible ? 'fa-ban' : 'fa-lock-open';
                            const text = willBeIndisponible ? 'Indisponible' : 'Disponible';
                            
                            statusDiv.className = `creneau-statut ${newStatus}`;
                            statusDiv.innerHTML = `<i class="fas ${icon}"></i> ${text}`;
                        }

                        // Mise à jour du bouton
                        button.className = `btn ${willBeIndisponible ? 'btn-success' : 'btn-warning'} btn-toggle-dispo`;
                        button.textContent = willBeIndisponible ? 'Rendre disponible' : 'Marquer indisponible';

                        // Décocher la case si elle était cochée
                        const checkbox = creneauItem.querySelector('.creneau-select');
                        if (checkbox && checkbox.checked) {
                            checkbox.checked = false;
                            creneauItem.classList.remove('selected');
                        }

                        // Rafraîchir l'affichage du calendrier si présent
                        if (window.loadUnavailableSlots) {
                            window.resetUnavailableSlots();
                            window.loadUnavailableSlots();
                        }

                        // Afficher le message de succès
                        const successAlert = document.createElement('div');
                        successAlert.classList.add('alert-popup', 'success');
                        const statusMessage = willBeIndisponible ? 'marqué indisponible' : 'rendu disponible';
                        successAlert.innerHTML = `
                            <i class="fas fa-check-circle"></i>
                            <span class="message">Le créneau a été ${statusMessage} avec succès.</span>
                        `;
                        document.body.appendChild(successAlert);
                        setTimeout(() => {
                            if (successAlert && successAlert.parentElement) {
                                successAlert.parentElement.removeChild(successAlert);
                            }
                        }, 5000);
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    const errorAlert = document.createElement('div');
                    errorAlert.classList.add('alert-popup', 'error');
                    errorAlert.innerHTML = `
                        <i class="fas fa-exclamation-circle"></i>
                        <span class="message">Une erreur est survenue lors de la modification du créneau.</span>
                    `;
                    document.body.appendChild(errorAlert);
                    setTimeout(() => errorAlert.remove(), 5000);
                }
            });
        });

        // Gérer le clic sur le bouton de marquage multiple
        markUnavailableButton?.addEventListener('click', async function() {
            const selectedIds = [...periodeElement.querySelectorAll('.creneau-select:checked')]
                .map(checkbox => checkbox.dataset.id);

            if (!selectedIds.length) return;

            // Vérifier le statut actuel des créneaux sélectionnés
            const selectedCheckboxes = periodeElement.querySelectorAll('.creneau-select:checked');
            const allIndisponible = Array.from(selectedCheckboxes).every(checkbox => {
                const creneauItem = checkbox.closest('.creneau-item');
                return creneauItem.querySelector('.creneau-statut.indisponible') !== null;
            });

            // Créer une boîte de dialogue de confirmation
            const confirmDialog = document.createElement('div');
            confirmDialog.classList.add('alert-popup');
            const pluriel = selectedIds.length > 1 ? 'créneaux' : 'créneau';
            const action = allIndisponible ? 'rendre' : 'marquer';
            const statut = allIndisponible ? 'disponibles' : 'indisponibles';
            
            confirmDialog.innerHTML = `
                <i class="fas fa-question-circle"></i>
                <span class="message">Êtes-vous sûr de vouloir ${action} ${selectedIds.length} ${pluriel} comme ${statut} ?</span>
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
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    
                    // Créer un tableau de promesses pour traiter chaque créneau
                    const promises = selectedIds.map(id => 
                        fetch('index.php?page=creneaux&action=toggleIndisponible', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ id: id })
                        }).then(response => response.json())
                    );

                    // Attendre que tous les créneaux soient traités
                    const results = await Promise.all(promises);
                    
                    // Compter les succès et les erreurs
                    const successes = results.filter(result => result.success).length;
                    const errors = results.filter(result => !result.success).length;

                    // Mettre à jour l'interface pour chaque créneau modifié avec succès
                    selectedIds.forEach((id, index) => {
                        if (results[index].success) {
                            const checkbox = periodeElement.querySelector(`.creneau-select[data-id="${id}"]`);
                            const creneauItem = checkbox?.closest('.creneau-item');
                            if (creneauItem) {
                                const wasIndisponible = creneauItem.querySelector('.creneau-statut.indisponible') !== null;
                                const willBeIndisponible = !wasIndisponible;
                                
                                // Mise à jour du statut
                                const statusDiv = creneauItem.querySelector('.creneau-info .creneau-statut');
                                if (statusDiv) {
                                    const newStatus = willBeIndisponible ? 'indisponible' : 'disponible';
                                    const icon = willBeIndisponible ? 'fa-ban' : 'fa-lock-open';
                                    const text = willBeIndisponible ? 'Indisponible' : 'Disponible';
                                    
                                    statusDiv.className = `creneau-statut ${newStatus}`;
                                    statusDiv.innerHTML = `<i class="fas ${icon}"></i> ${text}`;
                                }

                                // Mise à jour du bouton
                                const toggleBtn = creneauItem.querySelector('.btn-toggle-dispo');
                                if (toggleBtn) {
                                    toggleBtn.className = `btn ${willBeIndisponible ? 'btn-success' : 'btn-warning'} btn-toggle-dispo`;
                                    toggleBtn.textContent = willBeIndisponible ? 'Rendre disponible' : 'Marquer indisponible';
                                }

                                // Décocher la case
                                const checkbox = creneauItem.querySelector('.creneau-select');
                                if (checkbox) {
                                    checkbox.checked = false;
                                    creneauItem.classList.remove('selected');
                                }
                            }
                        }
                    });

                    // Réinitialiser la sélection
                    const selectAllCheckbox = periodeElement.querySelector('.select-all-checkbox');
                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked = false;
                    }
                    updateMarkUnavailableButton();

                    // Rafraîchir l'affichage du calendrier si présent
                    if (window.loadUnavailableSlots) {
                        window.resetUnavailableSlots();
                        window.loadUnavailableSlots();
                    }

                    // Afficher le message de succès/erreur
                    const successAlert = document.createElement('div');
                    successAlert.classList.add('alert-popup', 'success');
                    const statusMessage = allIndisponible ? 'rendu disponible' : 'marqué indisponible';
                    successAlert.innerHTML = `
                        <i class="fas fa-check-circle"></i>
                        <span class="message">${successes} ${pluriel} ${statusMessage}${selectedIds.length > 1 ? 's' : ''}.${
                            errors ? `<br>${errors} échec${errors > 1 ? 's' : ''}.` : ''
                        }</span>
                    `;
                    document.body.appendChild(successAlert);
                    setTimeout(() => {
                        if (successAlert && successAlert.parentElement) {
                            successAlert.parentElement.removeChild(successAlert);
                        }
                    }, 5000);

                } catch (error) {
                    console.error('Erreur:', error);
                    const errorAlert = document.createElement('div');
                    errorAlert.classList.add('alert-popup', 'error');
                    errorAlert.innerHTML = `
                        <i class="fas fa-exclamation-circle"></i>
                        <span class="message">Une erreur est survenue lors de la modification des créneaux.</span>
                    `;
                    document.body.appendChild(errorAlert);
                    setTimeout(() => errorAlert.remove(), 5000);
                }
            }
        });

        // Initialiser l'état du bouton
        updateMarkUnavailableButton();
    };

    // Initialiser pour chaque période
    document.querySelectorAll('.periode-section').forEach(periodeElement => {
        initCreneauxIndisponibilite(periodeElement);
    });
});
