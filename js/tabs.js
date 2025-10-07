document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    function switchTab(tabId) {
        // Mettre à jour les classes des boutons
        tabButtons.forEach(button => {
            button.classList.toggle('active', button.dataset.tab === tabId);
        });

        // Mettre à jour les classes des contenus
        tabContents.forEach(content => {
            content.classList.toggle('active', content.id === tabId);
        });

        // Sauvegarder le dernier onglet actif
        localStorage.setItem('lastActiveActuTab', tabId);
    }

    // Ajouter les écouteurs d'événements aux boutons
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            switchTab(button.dataset.tab);
        });
    });

    // Restaurer le dernier onglet actif ou utiliser l'onglet par défaut
    const lastActiveTab = localStorage.getItem('lastActiveActuTab');
    if (lastActiveTab && document.getElementById(lastActiveTab)) {
        switchTab(lastActiveTab);
    } else {
        // Par défaut, activer le premier onglet
        const firstTab = tabButtons[0]?.dataset.tab;
        if (firstTab) {
            switchTab(firstTab);
        }
    }
});