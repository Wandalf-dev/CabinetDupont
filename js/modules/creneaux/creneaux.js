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
            this.btnSelectAll  = document.getElementById('btn-select-all');

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

            // Événement pour le bouton "Tout sélectionner"
            if (this.btnSelectAll) {
                this.btnSelectAll.addEventListener('click', () => this.toggleSelectAll());
            }

            // Événements des actions en masse
            if (this.bulkActions.markUnavailable) {
                this.bulkActions.markUnavailable.addEventListener('click', (e) => {
                    // Empêcher le clic si désactivé
                    if (this.bulkActions.markUnavailable.getAttribute('aria-disabled') === 'true') {
                        e.preventDefault();
                        return;
                    }
                    this.markSelectedUnavailable();
                });
                
                // Tooltip custom pour les messages d'erreur
                this.bulkActions.markUnavailable.addEventListener('mouseenter', function(e) {
                    const tooltipText = this.getAttribute('data-tooltip');
                    console.log('Mouseenter sur bouton, tooltip text:', tooltipText);
                    if (!tooltipText) return;
                    // Empêcher les doublons
                    const existing = document.querySelector('.custom-tooltip');
                    if (existing) {
                        existing.remove();
                    }
                    let tooltip = document.createElement('div');
                    tooltip.className = 'custom-tooltip';
                    tooltip.textContent = tooltipText;
                    document.body.appendChild(tooltip);
                    const rect = this.getBoundingClientRect();
                    const tooltipRect = tooltip.getBoundingClientRect();
                    tooltip.style.left = `${rect.left + (rect.width / 2) - (tooltipRect.width / 2)}px`;
                    tooltip.style.top = `${rect.top - tooltipRect.height - 5}px`;
                    console.log('Tooltip créé, position:', tooltip.style.left, tooltip.style.top);
                    requestAnimationFrame(() => {
                        tooltip.classList.add('show');
                        console.log('Classe show ajoutée au tooltip');
                    });
                });
                this.bulkActions.markUnavailable.addEventListener('mouseleave', function(e) {
                    const tooltip = document.querySelector('.custom-tooltip');
                    if (tooltip) {
                        console.log('Suppression tooltip');
                        tooltip.remove();
                    }
                });
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
            
            // Fonction pour détecter si on est sur mobile
            const isMobile = () => window.innerWidth <= 768;
            
            // Charger la préférence depuis sessionStorage ou définir selon l'écran
            let savedView;
            if (isMobile()) {
                // Sur mobile, forcer la vue grille
                savedView = 'grid';
            } else {
                // Sur desktop, utiliser la préférence sauvegardée ou 'list' par défaut
                savedView = sessionStorage.getItem('creneaux-view') || 'list';
            }
            this.setView(savedView);

            // Gérer les clics sur les boutons de vue
            this.viewButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const view = button.dataset.view;
                    this.setView(view);
                    // Sauvegarder la préférence uniquement sur desktop
                    if (!isMobile()) {
                        sessionStorage.setItem('creneaux-view', view);
                    }
                });
            });

            // Gérer le changement de taille d'écran
            window.addEventListener('resize', () => {
                if (isMobile() && this.creneauxView.dataset.view !== 'grid') {
                    this.setView('grid');
                }
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
                    // Réattacher les événements du bouton générer (dans l'empty state)
                    this.reattachGenerateButton();
                } else {
                    throw new Error('Structure HTML invalide dans la réponse');
                }
            } catch (error) {
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
            
            // Vérifier le statut des créneaux sélectionnés
            let hasAvailable = false;
            let hasUnavailable = false;
            
            if (hasSelection) {
                this.selectedCreneaux.forEach(id => {
                    const checkbox = this.creneauxView.querySelector(`.creneau-select[data-id="${id}"]`);
                    if (checkbox) {
                        const card = checkbox.closest('.creneau-card');
                        if (card) {
                            if (card.classList.contains('available')) {
                                hasAvailable = true;
                            } else if (card.classList.contains('unavailable')) {
                                hasUnavailable = true;
                            }
                        }
                    }
                });
            }
            
            // Gérer le bouton "Marquer indisponible/disponible"
            if (this.bulkActions.markUnavailable) {
                const allUnavailable = hasSelection && hasUnavailable && !hasAvailable;
                const mixedSelection = hasSelection && hasAvailable && hasUnavailable;
                
                if (allUnavailable) {
                    // Tous indisponibles → Bouton "Marquer disponible"
                    this.bulkActions.markUnavailable.removeAttribute('aria-disabled');
                    this.bulkActions.markUnavailable.style.pointerEvents = '';
                    this.bulkActions.markUnavailable.style.opacity = '';
                    this.bulkActions.markUnavailable.innerHTML = '<i class="fas fa-check"></i> Marquer disponible';
                    this.bulkActions.markUnavailable.classList.remove('btn-warning');
                    this.bulkActions.markUnavailable.classList.add('btn-success');
                    this.bulkActions.markUnavailable.removeAttribute('title');
                    this.bulkActions.markUnavailable.setAttribute('data-tooltip', 'Rendre ces créneaux disponibles');
                    this.bulkActions.markUnavailable.dataset.action = 'available';
                } else if (mixedSelection) {
                    // Mélange → Désactiver visuellement mais permettre le survol
                    this.bulkActions.markUnavailable.setAttribute('aria-disabled', 'true');
                    this.bulkActions.markUnavailable.style.pointerEvents = 'auto';
                    this.bulkActions.markUnavailable.style.opacity = '0.5';
                    this.bulkActions.markUnavailable.style.cursor = 'not-allowed';
                    this.bulkActions.markUnavailable.innerHTML = '<i class="fas fa-ban"></i> Marquer indisponible';
                    this.bulkActions.markUnavailable.classList.remove('btn-success');
                    this.bulkActions.markUnavailable.classList.add('btn-warning');
                    this.bulkActions.markUnavailable.removeAttribute('title');
                    this.bulkActions.markUnavailable.setAttribute('data-tooltip', 'Impossible : la sélection contient des créneaux avec un statut différent');
                    this.bulkActions.markUnavailable.dataset.action = 'unavailable';
                } else if (hasAvailable) {
                    // Tous disponibles → Bouton "Marquer indisponible"
                    this.bulkActions.markUnavailable.removeAttribute('aria-disabled');
                    this.bulkActions.markUnavailable.style.pointerEvents = '';
                    this.bulkActions.markUnavailable.style.opacity = '';
                    this.bulkActions.markUnavailable.innerHTML = '<i class="fas fa-ban"></i> Marquer indisponible';
                    this.bulkActions.markUnavailable.classList.remove('btn-success');
                    this.bulkActions.markUnavailable.classList.add('btn-warning');
                    this.bulkActions.markUnavailable.removeAttribute('title');
                    this.bulkActions.markUnavailable.removeAttribute('data-tooltip');
                    this.bulkActions.markUnavailable.dataset.action = 'unavailable';
                } else {
                    // Aucune sélection
                    this.bulkActions.markUnavailable.setAttribute('aria-disabled', 'true');
                    this.bulkActions.markUnavailable.style.pointerEvents = 'none';
                    this.bulkActions.markUnavailable.style.opacity = '0.5';
                    this.bulkActions.markUnavailable.innerHTML = '<i class="fas fa-ban"></i> Marquer indisponible';
                    this.bulkActions.markUnavailable.classList.remove('btn-success');
                    this.bulkActions.markUnavailable.classList.add('btn-warning');
                    this.bulkActions.markUnavailable.removeAttribute('title');
                    this.bulkActions.markUnavailable.removeAttribute('data-tooltip');
                    this.bulkActions.markUnavailable.dataset.action = 'unavailable';
                }
                
            }
            
            if (this.bulkActions.delete) {
                this.bulkActions.delete.disabled = !hasSelection;
            }

            // Mettre à jour le texte et l'icône du bouton "Tout sélectionner"
            if (this.btnSelectAll) {
                const checkboxes = this.getSelectableCheckboxes();
                const allSelected = checkboxes.length > 0 && this.selectedCreneaux.size === checkboxes.length;
                
                if (allSelected) {
                    this.btnSelectAll.innerHTML = '<i class="fas fa-square"></i> Tout désélectionner';
                } else {
                    this.btnSelectAll.innerHTML = '<i class="fas fa-check-square"></i> Tout sélectionner';
                }
            }
        },

        getSelectableCheckboxes() {
            if (!this.creneauxView) return [];
            // Récupérer toutes les cases à cocher visibles et non désactivées
            const checkboxes = Array.from(this.creneauxView.querySelectorAll('.creneau-select'));
            return checkboxes.filter(cb => {
                const card = cb.closest('.creneau-card');
                return !cb.disabled && card && card.style.display !== 'none';
            });
        },

        toggleSelectAll() {
            const checkboxes = this.getSelectableCheckboxes();
            if (checkboxes.length === 0) return;

            // Vérifier si tous sont déjà sélectionnés
            const allSelected = checkboxes.every(cb => cb.checked);

            // Inverser la sélection
            checkboxes.forEach(cb => {
                cb.checked = !allSelected;
                const id = cb.dataset.id;
                if (cb.checked) {
                    this.selectedCreneaux.add(id);
                } else {
                    this.selectedCreneaux.delete(id);
                }
            });

            this.updateSelectionUI();
        },

        async toggleDisponibilite(id) {
            try {
                const response = await fetch('index.php?page=creneaux&action=toggleIndisponible', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    },
                    body: JSON.stringify({ id })
                });

                const responseText = await response.text();

                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    throw new Error('Réponse invalide du serveur');
                }

                if (!response.ok) {
                    throw new Error(data.error || 'Erreur lors du changement de disponibilité');
                }

                if (!data.success) {
                    throw new Error(data.error || 'La modification a échoué');
                }

                await this.loadCreneaux();
                this.showSuccess(data.message || 'Statut du créneau mis à jour');
            } catch (error) {
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
                this.showError('Erreur lors de l\'annulation du rendez-vous');
            }
        },

        async markSelectedUnavailable() {
            const count = this.selectedCreneaux.size;
            const action = this.bulkActions.markUnavailable.dataset.action || 'unavailable';
            
            let confirmMessage, apiAction, successMessage;
            
            if (action === 'available') {
                confirmMessage = `Êtes-vous sûr de vouloir marquer ${count} créneau(x) comme disponible(s) ?`;
                apiAction = 'markAvailableBulk';
                successMessage = 'Créneaux marqués comme disponibles';
            } else {
                confirmMessage = `Êtes-vous sûr de vouloir marquer ${count} créneau(x) comme indisponible(s) ?`;
                apiAction = 'markUnavailableBulk';
                successMessage = 'Créneaux marqués comme indisponibles';
            }
            
            const ok = await this.confirm(confirmMessage);
            if (!ok) return;

            try {
                const response = await fetch(`index.php?page=creneaux&action=${apiAction}`, {
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
                this.showSuccess(successMessage);
            } catch (error) {
                this.showError('Erreur lors de la modification des créneaux');
            }
        },

        async deleteSelected() {
            const count = this.selectedCreneaux.size;
            const ok = await this.confirm({ action: 'delete', count });
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
                this.showError('Erreur lors de la génération des créneaux');
            }
        },

        // Utilitaires
        getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content || '';
        },

        showSuccess(message) {
            if (window.AlertManager) {
                AlertManager.show(message, 'success');
            } else {
                alert(message);
            }
        },

        showError(message) {
            if (window.AlertManager) {
                AlertManager.show(message, 'error');
            } else {
                alert(message);
            }
        },

        async confirm(options) {
            // Si showConfirmationPopup existe, l'utiliser
            if (window.showConfirmationPopup) {
                return await window.showConfirmationPopup(options);
            }
            // Sinon, fallback sur confirm() natif
            if (typeof options === 'string') {
                return confirm(options);
            }
            
            let message = '';
            if (options.action === 'delete') {
                message = `Êtes-vous sûr de vouloir supprimer ${options.count} créneau(x) ?`;
            } else if (options.action === 'statut' && options.type === 'indisponible') {
                message = `Êtes-vous sûr de vouloir marquer ${options.count} créneau(x) comme indisponible(s) ?`;
            } else {
                message = `Êtes-vous sûr de vouloir effectuer cette action sur ${options.count || 1} créneau(x) ?`;
            }
            return confirm(message);
        },

        initializeTooltips() {
            if (window.initTooltips) {
                window.initTooltips();
            }
        },

        reattachGenerateButton() {
            // Réattacher l'événement au bouton "Générer des créneaux" dans l'empty state
            const btnGenererEmpty = document.getElementById('btn-generer-empty');
            if (btnGenererEmpty) {
                btnGenererEmpty.addEventListener('click', () => this.openGenerationModal());
            }
        }
    };

    // Initialisation
    creneauxApp.init();
});
