<?php
include __DIR__ . '/../templates/header.php';
?>

<main class="confirmation-page">
    <div class="container">
        <h1 class="section-title">Confirmation du rendez-vous</h1>

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

<style>
    .confirmation-page {
        padding: 2rem 0;
    }

    .confirmation-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        margin-top: 2rem;
    }

    .info-section {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #eee;
    }

    .info-section:last-child {
        border-bottom: none;
    }

    .info-section h2 {
        color: #333;
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .label {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }

    .value {
        color: #333;
        font-weight: 600;
    }

    .service-info {
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 4px;
    }

    .service-info .label {
        display: block;
        margin-bottom: 0.5rem;
    }

    .service-info .description {
        margin: 0;
        line-height: 1.5;
        color: #666;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        justify-content: center;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
    }

    .btn-success:hover {
        background-color: #218838;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    @media (max-width: 768px) {
        .confirmation-card {
            padding: 1rem;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }
    }
</style>

<?php include __DIR__ . '/../templates/footer.php'; ?>