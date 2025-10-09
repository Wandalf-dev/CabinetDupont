<?php
// Affiche les messages flash de succès ou d'erreur stockés en session
if (isset($_SESSION['success']) || isset($_SESSION['error'])) {
    echo '<div id="flash-messages" class="flash-message">';
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
        unset($_SESSION['success']); // Supprime le message après affichage
    }
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-error">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']); // Supprime le message après affichage
    }
    echo '</div>';
}
?>

<script>
// Script pour faire disparaître automatiquement le message flash après 5 secondes
document.addEventListener('DOMContentLoaded', function() {
    const flashMessages = document.getElementById('flash-messages');
    if (flashMessages) {
        // Faire disparaître le message après 5 secondes
        setTimeout(function() {
            flashMessages.style.animation = 'slideOut 0.5s ease-out forwards';
            setTimeout(function() {
                flashMessages.remove();
            }, 500);
        }, 5000);
    }
});
</script>

<style>
/* Animation pour faire glisser le message vers la droite avant de le retirer */
@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}
</style>