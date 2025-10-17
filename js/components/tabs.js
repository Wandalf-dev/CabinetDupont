document.addEventListener('DOMContentLoaded', function() {
    console.log('Initialisation des onglets...');
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    console.log('Nombre de boutons trouvés:', tabButtons.length);
    console.log('Nombre de contenus trouvés:', tabContents.length);

    function switchTab(tabId) {
        // Retire la classe active de tous les boutons et contenus
        tabButtons.forEach(button => {
            button.classList.remove('active');
        });
        tabContents.forEach(content => {
            content.classList.remove('active');
        });

        // Active le bon onglet et son contenu
        const activeButton = document.querySelector(`.tab-button[data-tab="${tabId}"]`);
        const activeContent = document.getElementById(tabId);

        if (activeButton && activeContent) {
            activeButton.classList.add('active');
            activeContent.classList.add('active');
            // Sauvegarde l'onglet actif sans modifier l'URL
            localStorage.setItem('lastActiveTab', tabId);
        }
    }

    // Ajouter les écouteurs d'événements aux boutons
    tabButtons.forEach(button => {
        console.log('Ajout du listener sur le bouton:', button.dataset.tab);
        button.addEventListener('click', (e) => {
            console.log('Clic sur le bouton:', button.dataset.tab);
            e.preventDefault();
            switchTab(button.dataset.tab);
        });
    });

    // Restaurer le dernier onglet actif ou utiliser le premier par défaut
    window.addEventListener('load', function() {
        // Force la suppression de l'ancre si elle existe
        if (window.location.hash) {
            window.history.replaceState(null, '', window.location.pathname + window.location.search);
        }

        const lastActiveTab = localStorage.getItem('lastActiveTab');
        if (lastActiveTab && document.getElementById(lastActiveTab)) {
            switchTab(lastActiveTab);
        } else if (tabButtons.length > 0) {
            switchTab(tabButtons[0].dataset.tab);
        }
    });
});