document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour formater la date pendant la saisie
    function initializeDateFormatter(element) {
        // Ne pas appliquer Cleave sur les input[type="date"] (natifs en responsive)
        if (element.type === 'date') {
            return;
        }
        
        new Cleave(element, {
            date: true,
            datePattern: ['d', 'm', 'Y'],
            delimiter: '/',
            blocks: [2, 2, 4],
            numericOnly: true
        });
    }

    // Appliquer le formateur à tous les champs de date Flatpickr
    document.querySelectorAll('.flatpickr').forEach(function(element) {
        initializeDateFormatter(element);
        
        // Écouter les événements d'input pour valider la date
        element.addEventListener('input', function() {
            const value = this.value;
            if (value.length === 10) { // Format complet JJ/MM/AAAA
                const [day, month, year] = value.split('/');
                const date = new Date(year, month - 1, day);
                const isValid = date instanceof Date && !isNaN(date) &&
                              date.getDate() == day && // Vérifie si le jour est valide
                              date.getMonth() == month - 1 && // Vérifie si le mois est valide
                              date.getFullYear() == year; // Vérifie si l'année est valide
                
                if (!isValid) {
                    this.value = ''; // Réinitialise si la date n'est pas valide
                }
            }
        });
    });
});