document.addEventListener('DOMContentLoaded', function() {
    var phoneInput = document.getElementById('telephone');
    if (!phoneInput) return;

    // Formatter le champ pour X-XX-XX-XX-XX
    var cleave = new Cleave(phoneInput, {
        delimiters: ['-', '-', '-', '-'],
        blocks: [1, 2, 2, 2, 2],
        numericOnly: true
    });

    // Empêcher la saisie de caractères non numériques
    phoneInput.addEventListener('keydown', function(e) {
        // Autoriser les touches de contrôle
        if (e.ctrlKey || e.metaKey || e.key.length > 1) return;
        if (!/\d/.test(e.key)) {
            e.preventDefault();
        }
    });

    // Nettoyer la valeur initiale si besoin
    if (phoneInput.value) {
        let digits = phoneInput.value.replace(/\D/g, '');
        if (digits.startsWith('33')) digits = digits.substring(2);
        if (digits.startsWith('0')) digits = digits.substring(1);
        if (digits.length === 9) {
            cleave.setRawValue(digits);
        }
    }
});