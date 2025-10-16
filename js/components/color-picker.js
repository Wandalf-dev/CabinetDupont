// Met à jour tous les éléments liés à la couleur lorsque le sélecteur de couleur change
function updateColorInputs(colorPicker) {
    const colorValue = colorPicker.value.toUpperCase();
    const textInput = document.getElementById('couleurText');
    const previewDot = document.getElementById('colorPreviewDot');
    
    if (textInput) {
        textInput.value = colorValue;
    }
    if (previewDot) {
        previewDot.style.backgroundColor = colorValue;
    }
}

// Met à jour tous les éléments liés à la couleur lorsque le champ texte change
function updateColorFromText(textInput) {
    let colorValue = textInput.value.toUpperCase();
    
    // Ajoute le # si manquant
    if (!colorValue.startsWith('#')) {
        colorValue = '#' + colorValue;
        textInput.value = colorValue;
    }
    
    // Vérifie si c'est un code hexadécimal valide
    if (/^#[0-9A-F]{6}$/.test(colorValue)) {
        const colorPicker = document.getElementById('couleur');
        const previewDot = document.getElementById('colorPreviewDot');
        
        if (colorPicker) {
            colorPicker.value = colorValue;
        }
        if (previewDot) {
            previewDot.style.backgroundColor = colorValue;
        }
    }
}