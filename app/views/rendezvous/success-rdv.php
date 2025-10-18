<?php require_once 'app/views/templates/header.php'; ?>

<!-- CSS spécifique à la confirmation de rendez-vous -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/modules/rendez-vous/success-rdv.css">

<main>
    <div class="success-container">
        <div class="success-content">
            <div class="text-center">
                <div class="success-alert">
                    <div class="success-header">
                        <i class="fas fa-check-circle fa-4x success-icon"></i>
                        <h1 class="success-heading">Rendez-vous confirmé avec succès !</h1>
                    </div>
                    <p class="success-message">Un email de confirmation vous a été envoyé avec les détails de votre rendez-vous.</p>
                </div>
                
                <div class="success-buttons">
                    <a href="index.php" class="btn btn-primary me-3">
                        <i class="fas fa-home me-2"></i>
                        Retour à l'accueil
                    </a>
                    <a href="index.php?page=rendezvous&action=list" class="btn btn-secondary">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Voir mes rendez-vous
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'app/views/templates/footer.php'; ?>