<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/flash-messages.php';
?>

<!-- CSS spécifiques aux rendez-vous -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/modules/rendez-vous/select-time.css">

<main class="container">
    <section class="select-time">
        <h1 class="page-title">
            <i class="fas fa-clock"></i>
            Choisissez un horaire
        </h1>
        <?php if (isset($service) && isset($date)): ?>
            <p class="selected-date">
                Horaires disponibles pour le <?= (new DateTime($date))->format('d/m/Y') ?>
                <br>
                <small>Service : <?= htmlspecialchars($service['titre']) ?></small>
            </p>
            
            <div class="time-slots-wrapper">
                <?php if (empty($availableSlots)): ?>
                    <p class="no-slots">Aucun créneau disponible pour cette date. Veuillez choisir une autre date.</p>
                <?php else: ?>
                    <div class="time-slots">
                        <?php 
                        error_log("Affichage des créneaux disponibles dans la vue:");
                        foreach ($availableSlots as $slot): 
                            error_log("Créneau: ID=" . $slot['id'] . ", début=" . $slot['debut'] . ", fin=" . $slot['fin']);
                        ?>
                            <a href="<?= BASE_URL ?>/index.php?page=rendezvous&action=confirmation&service_id=<?= htmlspecialchars($service['id']) ?>&creneau_id=<?= htmlspecialchars($slot['id']) ?>" 
                               class="time-slot"
                               data-duration="<?= htmlspecialchars($service['duree']) ?>"
                               title="Durée : <?= htmlspecialchars($service['duree']) ?> minutes">
                                <?= date('H:i', strtotime($slot['debut'])) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="navigation-buttons">
                <a href="<?= BASE_URL ?>/index.php?page=rendezvous&action=selectDate&service_id=<?= htmlspecialchars($service['id']) ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Retour à la sélection de date
                </a>
            </div>
        <?php else: ?>
            <div class="error-message">
                <p>Une erreur est survenue. Veuillez recommencer la prise de rendez-vous.</p>
                <a href="<?= BASE_URL ?>/index.php?page=rendezvous&action=selectConsultation" class="btn btn-primary">
                    Reprendre un rendez-vous
                </a>
            </div>
        <?php endif; ?>
    </section>
</main>

<script>
document.querySelectorAll('.time-slot').forEach(button => {
    button.addEventListener('click', function() {
        const time = this.dataset.time;
        const serviceId = new URLSearchParams(window.location.search).get('service_id');
        const date = new URLSearchParams(window.location.search).get('date');
        
        // Redirection vers la confirmation avec toutes les informations
        window.location.href = `index.php?page=rendezvous&action=confirmation&service_id=${serviceId}&date=${date}&time=${time}`;
    });
});
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>