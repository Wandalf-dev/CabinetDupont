// Gestion des créneaux - Administration
document.addEventListener('DOMContentLoaded', function () {
    const creneauxApp = {
        init() {
            this.container = document.querySelector('.creneaux-container');
            if (!this.container) return;

            this.initializeComponents();
            this.bindEvents();
            this.setupViewToggle();
            this.setupDateNavigation();
            this.setupFilters();
            this.setupSelectionHandling();
            this.setupModalHandling();
            this.initializeTooltips();
            // Chargement initial si besoin
            if (this.datePicker && this.datePicker.value) {
                this.loadCreneaux();
            }
        },

        initializeComponents() {
            // Composants principaux
            this.datePicker = document.getElementById('date-picker');

            // Navigation
            this.prevDay = document.querySelector('.prev-day');
            this.nextDay = document.querySelector('.next-day');

            this.viewButtons   = document.querySelectorAll('.view-toggle button');
            this.creneauxView  = document.querySelector('.creneaux-view');
            this.searchInput   = document.querySelector('.search-box input');
            this.statusFilter  = document.getElementById('filter-status');
            this.periodFilter  = document.getElementById('filter-period');
            this.selectedCount = document.querySelector('.selected-count');

            this.bulkActions = {
                markUnavailable: document.getElementById('btn-mark-unavailable'),
                delete: document.getElementById('btn-delete-selected'),
            };

            // Composants du modal
            this.modal      = document.getElementById('modal-generer');
            this.modalForm  = document.getElementById('form-generer');
            this.dateDebut  = document.getElementById('date_debut');
            this.dateFin    = document.getElementById('date_fin');

            // Boutons d'ouverture / validation du modal
            this.btnGenererToolbar = document.getElementById('btn-generer-creneaux');
            this.btnGenererEmpty   = document.getElementById('btn-generer-empty');
            this.btnConfirmerGen   = document.getElementById('btn-confirmer-generation');
        },

        bindEvents() {
            // Événements de navigation
            if (this.prevDay) {
                this.prevDay.addEventListener('click', () => {
                    this.changeDate(-1);
                });
            }
            if (this.nextDay) {
                this.nextDay.addEventListener('click', () => {
                    this.changeDate(1);
                });
            }

            if (this.datePicker) {
                this.datePicker.addEventListener('change', () => this.loadCreneaux());
            } // ←←← *** ACCOLADE MANQUANTE AVANT : corrigée ***

            // Événements de filtrage
            if (this.searchInput)  this.searchInput.addEventListener('input',  () => this.applyFilters());
            if (this.statusFilter) this.statusFilter.addEventListener('change', () => this.applyFilters());
            if (this.periodFilter) this.periodFilter.addEventListener('change', () => this.applyFilters());

            // Événements des actions en masse
            if (this.bulkActions.markUnavailable) {
                this.bulkActions.markUnavailable.addEventListener('click', () => this.markSelectedUnavailable());
            }
            if (this.bulkActions.delete) {
                this.bulkActions.delete.addEventListener('click', () => this.deleteSelected());
            }

            // Événements des actions individuelles (délégation)
            if (this.container) {
                this.container.addEventListener('click', (e) => {
                    const target = e.target.closest('button');
                    if (!target) return;

                    const id = target.dataset.id;
                    if (!id) return;

                    if (target.classList.contains('btn-unavailable')) {
                        this.toggleDisponibilite(id);
                    } else if (target.classList.contains('btn-available')) {
                        this.toggleDisponibilite(id);
                    } else if (target.classList.contains('btn-delete')) {
                        this.deleteCreneau(id);
                    } else if (target.classList.contains('btn-cancel')) {
                        this.cancelRendezVous(id);
                    }
                });
            }

            // Événements du modal (ouverture / validation)
            if (this.btnGenererToolbar) {
                this.btnGenererToolbar.addEventListener('click', () => this.openGenerationModal());
            }
            if (this.btnGenererEmpty) {
                this.btnGenererEmpty.addEventListener('click', () => this.openGenerationModal());
            }
            if (this.btnConfirmerGen) {
                this.btnConfirmerGen.addEventListener('click', () => this.genererCreneaux());
            }
        },

        setupViewToggle() {
            if (!this.viewButtons || this.viewButtons.length === 0 || !this.creneauxView) return;
            
            // Charger la préférence depuis sessionStorage
            const savedView = sessionStorage.getItem('creneaux-view') || 'grid';
            this.setView(savedView);

            // Gérer les clics sur les boutons de vue
            this.viewButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const view = button.dataset.view;
                    this.setView(view);
                    // Sauvegarder la préférence
                    sessionStorage.setItem('creneaux-view', view);
                });
            });
        },

        setView(view) {
            // Mettre à jour les boutons
            this.viewButtons.forEach(btn => {
                btn.classList.toggle('active', btn.dataset.view === view);
            });

            // Mettre à jour l'affichage
            this.creneauxView.dataset.view = view;
        },

        setupDateNavigation() {
            if (!this.datePicker) return;

            // Ne pas réinitialiser la date si elle est déjà définie
            if (!this.datePicker.value) {
                const today = new Date();
                this.datePicker.value = today.toISOString().split('T')[0];
            }
        },

        setupFilters() {
            this.filters = {
                search: '',
                status: 'all',
                period: 'all',
            };
        },

        setupSelectionHandling() {
            this.selectedCreneaux = new Set();

            if (!this.container) return;

            // Gérer les sélections (délégation)
            this.container.addEventListener('change', (e) => {
                const checkbox = e.target;
                if (!checkbox || !checkbox.classList.contains('creneau-select')) return;

                const id = checkbox.dataset.id;
                if (!id) return;

                if (checkbox.checked) {
                    this.selectedCreneaux.add(id);
                } else {
                    this.selectedCreneaux.delete(id);
                }
                this.updateSelectionUI();
            });
        },

        setupModalHandling() {
            if (!this.modal) return;

            const btnClose = this.modal.querySelector('.btn-close');
            const btnDismiss = this.modal.querySelector('[data-dismiss="modal"]');

            if (btnClose)   btnClose.addEventListener('click', () => this.closeModal());
            if (btnDismiss) btnDismiss.addEventListener('click', () => this.closeModal());

            this.modal.addEventListener('click', (e) => {
                if (e.target === this.modal) this.closeModal();
            });

            // Validation / contraintes de dates
            if (this.dateDebut && this.dateFin) {
                this.dateDebut.addEventListener('change', () => {
                    this.dateFin.min = this.dateDebut.value || '';
                    if (this.dateFin.value && this.dateFin.value < this.dateDebut.value) {
                        this.dateFin.value = this.dateDebut.value;
                    }
                });
            }
        },

        async loadCreneaux() {
            if (!this.creneauxView || !this.datePicker) return;
            try {
                const response = await fetch(`index.php?page=creneaux&action=loadCreneaux&date=${encodeURIComponent(this.datePicker.value)}`, {
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                
                if (!response.ok || !data.success) {
                    throw new Error(data.error || 'Erreur lors du chargement des créneaux');
                }

                const parser = new DOMParser();
                const doc = parser.parseFromString(data.html, 'text/html');
                const newContent = doc.querySelector('.creneaux-view');

                if (newContent) {
                    this.creneauxView.innerHTML = newContent.innerHTML;
                    this.initializeTooltips();
                    this.applyFilters();
                    // Réinitialiser la sélection après rechargement
                    this.selectedCreneaux.clear();
                    this.updateSelectionUI();
                } else {
                    throw new Error('Structure HTML invalide dans la réponse');
                }
            } catch (error) {
                console.error('Erreur détaillée:', error);
                this.showError(error.message || 'Erreur lors du chargement des créneaux');
            }
        },

        changeDate(days) {
            if (!this.datePicker) return;

            const currentDate = new Date(this.datePicker.value || new Date());
            const newDate = new Date(currentDate);
            newDate.setDate(currentDate.getDate() + days);

            this.datePicker.value = newDate.toISOString().split('T')[0];
            this.loadCreneaux();
        },

        applyFilters() {
            if (!this.creneauxView) return;

            const searchTerm = (this.searchInput?.value || '').toLowerCase();
            const status = this.statusFilter?.value || 'all';
            const period = this.periodFilter?.value || 'all';

            const cards = this.creneauxView.querySelectorAll('.creneau-card');
            cards.forEach(card => {
                let visible = true;

                // Filtre de recherche
                if (searchTerm) {
                    const searchableContent = (card.textContent || '').toLowerCase();
                    visible = searchableContent.includes(searchTerm);
                }

                // Filtre de statut
                if (visible && status !== 'all') {
                    visible = card.classList.contains(status);
                }

                // Filtre de période
                if (visible && period !== 'all') {
                    const timeNode = card.querySelector('.time-badge');
                    if (timeNode) {
                        const time = timeNode.textContent.trim();
                        const hour = parseInt(time, 10); // "10:30" -> 10
                        if (!isNaN(hour)) {
                            visible = period === 'morning' ? hour < 12 : hour >= 12;
                        }
                    }
                }

                card.style.display = visible ? '' : 'none';
            });
        },

        updateSelectionUI() {
            const count = this.selectedCreneaux.size;
            if (this.selectedCount) {
                this.selectedCount.textContent = `${count} sélectionné${count > 1 ? 's' : ''}`;
            }
            const hasSelection = count > 0;
            if (this.bulkActions.markUnavailable) this.bulkActions.markUnavailable.disabled = !hasSelection;
            if (this.bulkActions.delete)          this.bulkActions.delete.disabled          = !hasSelection;
        },

        async toggleDisponibilite(id) {
            try {
                console.log('Envoi de la requête pour le créneau:', id);
                const response = await fetch('index.php?page=creneaux&action=toggleIndisponible', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    },
                    body: JSON.stringify({ id })
                });

                const responseText = await response.text();
                console.log('Réponse brute du serveur:', responseText);

                let data;
                try {
                    data = JSON.parse(responseText);
                    console.log('Données JSON parsées:', data);
                } catch (e) {
                    console.error('Erreur de parsing JSON:', e);
                    throw new Error('Réponse invalide du serveur');
                }

                if (!response.ok) {
                    console.error('Statut HTTP non-ok:', response.status);
                    throw new Error(data.error || 'Erreur lors du changement de disponibilité');
                }

                if (!data.success) {
                    console.error('Réponse indique un échec:', data);
                    throw new Error(data.error || 'La modification a échoué');
                }

                await this.loadCreneaux();
                this.showSuccess(data.message || 'Statut du créneau mis à jour');
            } catch (error) {
                console.error('Erreur complète:', error);
                this.showError(error.message || 'Erreur lors de la modification du créneau');
            }
        },

        async deleteCreneau(id) {
            const ok = await this.confirm('Êtes-vous sûr de vouloir supprimer ce créneau ?');
            if (!ok) return;

            try {
                const response = await fetch('index.php?page=admin&action=deleteCreneau', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    },
                    body: JSON.stringify({ id })
                });

                if (!response.ok) throw new Error('Erreur lors de la suppression');

                await this.loadCreneaux();
                this.showSuccess('Créneau supprimé avec succès');
            } catch (error) {
                console.error(error);
                this.showError('Erreur lors de la suppression du créneau');
            }
        },

        async cancelRendezVous(id) {
            const ok = await this.confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?');
            if (!ok) return;

            try {
                const response = await fetch('index.php?page=rendezvous&action=annuler', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    },
                    body: JSON.stringify({ id })
                });

                if (!response.ok) throw new Error('Erreur lors de l\'annulation');

                await this.loadCreneaux();
                this.showSuccess('Rendez-vous annulé avec succès');
            } catch (error) {
                console.error(error);
                this.showError('Erreur lors de l\'annulation du rendez-vous');
            }
        },

        async markSelectedUnavailable() {
            const ok = await this.confirm('Êtes-vous sûr de vouloir marquer ces créneaux comme indisponibles ?');
            if (!ok) return;

            try {
                const response = await fetch('index.php?page=creneaux&action=markUnavailableBulk', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    },
                    body: JSON.stringify({ ids: Array.from(this.selectedCreneaux) })
                });

                if (!response.ok) throw new Error('Erreur lors du marquage des créneaux');

                await this.loadCreneaux();
                this.selectedCreneaux.clear();
                this.updateSelectionUI();
                this.showSuccess('Créneaux marqués comme indisponibles');
            } catch (error) {
                console.error(error);
                this.showError('Erreur lors de la modification des créneaux');
            }
        },

        async deleteSelected() {
            const ok = await this.confirm('Êtes-vous sûr de vouloir supprimer ces créneaux ?');
            if (!ok) return;

            try {
                const response = await fetch('index.php?page=creneaux&action=deleteCreneauxBulk', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    },
                    body: JSON.stringify({ ids: Array.from(this.selectedCreneaux) })
                });

                if (!response.ok) throw new Error('Erreur lors de la suppression des créneaux');

                await this.loadCreneaux();
                this.selectedCreneaux.clear();
                this.updateSelectionUI();
                this.showSuccess('Créneaux supprimés avec succès');
            } catch (error) {
                console.error(error);
                this.showError('Erreur lors de la suppression des créneaux');
            }
        },

        openGenerationModal() {
            if (!this.modal) return;
            this.modal.classList.add('show');

            // Pré-remplir les dates du modal avec la date courante sélectionnée
            if (this.dateDebut) this.dateDebut.value = this.datePicker?.value || '';
            if (this.dateFin) {
                this.dateFin.min = this.dateDebut?.value || (this.datePicker?.value || '');
                this.dateFin.value = this.datePicker?.value || '';
            }
        },

        closeModal() {
            if (!this.modal) return;
            this.modal.classList.remove('show');
            if (this.modalForm) this.modalForm.reset();
        },

        async genererCreneaux() {
            const dateDebut = this.dateDebut?.value || '';
            const dateFin   = this.dateFin?.value || '';

            if (!dateDebut || !dateFin) {
                this.showError('Veuillez sélectionner les dates');
                return;
            }

            try {
                const response = await fetch('index.php?page=creneaux&action=genererCreneaux', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    },
                    body: JSON.stringify({ date_debut: dateDebut, date_fin: dateFin })
                });

                if (!response.ok) throw new Error('Erreur lors de la génération des créneaux');

                this.closeModal();
                await this.loadCreneaux();
                this.showSuccess('Créneaux générés avec succès');
            } catch (error) {
                console.error(error);
                this.showError('Erreur lors de la génération des créneaux');
            }
        },

        // Utilitaires
        getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content || '';
        },

        showSuccess(message) {
            if (window.showAlert) {
                window.showAlert('success', message);
            } else {
                alert(message);
            }
        },

        showError(message) {
            if (window.showAlert) {
                window.showAlert('error', message);
            } else {
                alert(message);
            }
        },

        async confirm(message) {
            return window.showConfirmDialog
                ? await window.showConfirmDialog(message)
                : confirm(message);
        },

        initializeTooltips() {
            if (window.initTooltips) {
                window.initTooltips();
            }
        }
    };

    // Initialisation
    creneauxApp.init();
});
