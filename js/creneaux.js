document.addEventListener('DOMContentLoaded', function() {
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');

    // S'assurer que la date de fin n'est pas antérieure à la date de début
    dateDebut.addEventListener('change', function() {
        if (dateFin.value < dateDebut.value) {
            dateFin.value = dateDebut.value;
        }
        dateFin.min = dateDebut.value;
    });

    // Gestion des boutons de périodes prédéfinies
    document.querySelectorAll('.btn-group .btn').forEach(button => {
        button.addEventListener('click', function() {
            const days = parseInt(this.dataset.days);
            // Mettre à jour la date de début à aujourd'hui
            dateDebut.value = new Date().toISOString().split('T')[0];
            // Calculer la date de fin
            const endDate = new Date();
            endDate.setDate(endDate.getDate() + days - 1);
            dateFin.value = endDate.toISOString().split('T')[0];
            
            // Mettre à jour le style des boutons
            document.querySelectorAll('.btn-group .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');
        });
    });

    // Initialisation des variables
    const modal = document.getElementById('confirmationModal');
    const modalMessage = document.getElementById('modalMessage');
    const btnGenerer = document.getElementById('btnGenerer');
    const btnConfirmer = document.getElementById('btnConfirmer');
    const btnAnnuler = document.getElementById('btnAnnuler');
    const form = document.querySelector('form');

    if (modal && modalMessage && btnGenerer && btnConfirmer && btnAnnuler) {
        // Ouvrir la modale lors du clic sur Générer
        btnGenerer.addEventListener('click', function() {
            const debut = new Date(dateDebut.value);
            const fin = new Date(dateFin.value);
            const diffTime = Math.abs(fin - debut);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            const creneauxEstimes = diffDays * 20;

            modalMessage.textContent = `Vous allez générer environ ${creneauxEstimes} créneaux sur ${diffDays} jours. Voulez-vous continuer ?`;
            modal.classList.add('show');
        });

        // Gérer la confirmation
        btnConfirmer.addEventListener('click', function() {
            modal.classList.remove('show');
            form.submit();
        });

        // Gérer l'annulation
        btnAnnuler.addEventListener('click', function() {
            modal.classList.remove('show');
        });

        // Fermer la modale si on clique en dehors
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('show');
            }
        });
    } else {
        console.error("Certains éléments de la modale n'ont pas été trouvés");
    }
});