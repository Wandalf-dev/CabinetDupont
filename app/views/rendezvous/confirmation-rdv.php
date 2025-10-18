<?php
include __DIR__ . '/../templates/header.php';
?>

<main class="confirmation-page">
    <div class="container">
        <h1 class="page-title">
            <i class="fas fa-check-circle"></i>
            Confirmation de votre rendez-vous
        </h1>

        <div class="confirmation-card">
            <!-- Informations du patient -->
            <section class="info-section">
                <h2>Vos informations personnelles</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Nom :</span>
                        <span class="value"><?php echo htmlspecialchars($patient['nom']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Prénom :</span>
                        <span class="value"><?php echo htmlspecialchars($patient['prenom']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Date de naissance :</span>
                        <span class="value"><?php echo date('d/m/Y', strtotime($patient['date_naissance'])); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Téléphone :</span>
                        <span class="value"><?php 
                            use App\Core\Utils;
                            echo htmlspecialchars(Utils::formatTelephone($patient['telephone'])); 
                        ?></span>
                    </div>
                </div>
            </section>

            <!-- Informations du rendez-vous -->
            <section class="info-section">
                <h2>Détails du rendez-vous</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Motif de consultation :</span>
                        <span class="value"><?php echo htmlspecialchars($service['titre']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Durée :</span>
                        <span class="value"><?php echo htmlspecialchars($service['duree']); ?> minutes</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Date :</span>
                        <span class="value"><?php echo date('d/m/Y', strtotime($creneau['debut'])); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Heure :</span>
                        <span class="value"><?php echo date('H:i', strtotime($creneau['debut'])); ?></span>
                    </div>
                </div>
            </section>

            <!-- Description du service -->
            <section class="info-section">
                <h2>Détails de la consultation</h2>
                <div class="info-grid">
                    <div class="info-item description">
                        <p class="value"><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                    </div>
                </div>
            </section>

            <!-- Actions -->
            <div class="button-group">
                <div class="button-wrapper">
                    <form action="index.php?page=rendezvous&action=confirmer" method="POST" class="confirmation-form">
                        <input type="hidden" name="creneau_id" value="<?php echo $creneau['id']; ?>">
                        <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check me-2"></i>
                            Confirmer le rendez-vous
                        </button>
                    </form>
                </div>
                <div class="button-wrapper">
                    <a href="index.php?page=rendezvous&action=selectTime&service_id=<?php echo $service['id']; ?>&date=<?php echo date('Y-m-d', strtotime($creneau['debut'])); ?>" class="btn btn-secondary">
                        <i class="fas fa-clock me-2"></i>
                        Modifier l'horaire
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../templates/footer.php'; ?>