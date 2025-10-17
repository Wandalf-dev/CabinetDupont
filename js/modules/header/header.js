document.addEventListener('DOMContentLoaded', function() {
    const userButton = document.querySelector('.user-button');
    const userDropdown = document.querySelector('.user-dropdown');

    // Gestionnaire pour le clic sur le bouton utilisateur
    if (userButton) {
        userButton.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('active');
        });
    }

    // Fermer le dropdown quand on clique ailleurs sur la page
    document.addEventListener('click', function(e) {
        if (userDropdown && !userDropdown.contains(e.target)) {
            userDropdown.classList.remove('active');
        }
    });
});