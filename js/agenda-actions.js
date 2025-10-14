// Gestion des rendez-vous
$(document).ready(function() {
    // Créer le menu contextuel
    const contextMenu = $('<div class="context-menu">')
        .append($('<ul>')
            .append($('<li>').text('Modifier'))
            .append($('<li>').text('Annuler'))
            .append($('<li>').text('Envoyer un mail au patient'))
        )
        .appendTo('body');

    // Gestion du clic droit sur un rendez-vous
    $(document).on('contextmenu', '.fc-event', function(e) {
        e.preventDefault();
        
        // Récupérer l'ID du rendez-vous
        const rdvId = $(this).data('rdv-id');
        if (!rdvId) return;

        // Stocker l'ID pour les actions du menu
        contextMenu.data('rdv-id', rdvId);

        // Positionner le menu
        contextMenu
            .css({
                top: e.pageY + 'px',
                left: e.pageX + 'px'
            })
            .show();
    });

    // Fermer le menu sur un clic ailleurs
    $(document).on('click', function() {
        contextMenu.hide();
    });

    // Empêcher la fermeture du menu lors d'un clic sur le menu
    contextMenu.on('click', function(e) {
        e.stopPropagation();
    });

    // Gestion des actions du menu
    contextMenu.find('li').on('click', function() {
        const action = $(this).text();
        const rdvId = contextMenu.data('rdv-id');

        switch(action) {
            case 'Annuler':
                // Afficher la boîte de dialogue de confirmation
                if (confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?')) {
                    $.ajax({
                        url: 'index.php?page=rendezvous&action=annuler',
                        method: 'POST',
                        data: { rdv_id: rdvId },
                        dataType: 'json',
                        success: function(result) {
                            alert(result.message);
                            if (result.success) {
                                location.reload(); // Recharger la page pour mettre à jour le calendrier
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Erreur:', error);
                            let message = 'Une erreur est survenue lors de l\'annulation du rendez-vous';
                            try {
                                const result = JSON.parse(xhr.responseText);
                                if (result.message) {
                                    message = result.message;
                                }
                            } catch (e) {
                                console.error('Erreur parsing JSON:', e);
                            }
                            alert(message);
                        }
                    });
                }
                break;
            
            case 'Modifier':
                alert('Fonctionnalité de modification en cours de développement');
                break;
            
            case 'Envoyer un mail au patient':
                alert('Fonctionnalité d\'envoi de mail en cours de développement');
                break;
        }

        contextMenu.hide();
    });
});