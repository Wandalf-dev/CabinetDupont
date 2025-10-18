<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/flash-messages.php';
?>

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

<style>
.select-time {
    padding: 2rem 0;
    max-width: 800px;
    margin: 0 auto;
}

.selected-date {
    text-align: center;
    font-size: 1.2rem;
    color: #666;
    margin: 1.5rem 0;
}

.time-slots-wrapper {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    margin-top: 2rem;
}

.time-slots {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 1rem;
}

.time-slot {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    border: 2px solid #1976d2;
    border-radius: 8px;
    background: white;
    color: #1976d2;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
}

.time-slot:hover {
    background: #1976d2;
    color: white;
    transform: translateY(-2px);
    text-decoration: none;
}

.error-message {
    text-align: center;
    padding: 2rem;
    background: #fff3f3;
    border-radius: 8px;
    margin: 2rem 0;
}

.error-message p {
    color: #d32f2f;
    margin-bottom: 1rem;
}

.navigation-buttons {
    text-align: center;
    margin-top: 2.5rem;
}

.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #1976d2;
    color: white;
}

.btn-secondary {
    background: #f3f4f6;
    color: #0d47a1;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    box-shadow: 0 2px 8px rgba(13, 71, 161, 0.07);
    padding: 0.85rem 2rem;
    transition: all 0.2s cubic-bezier(.4,0,.2,1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-secondary:hover {
    background: #e3eafc;
    color: #0d47a1;
    border-color: #0d47a1;
    box-shadow: 0 4px 16px rgba(13, 71, 161, 0.12);
    transform: translateY(-2px) scale(1.04);
    text-decoration: none;
}

.no-slots {
    text-align: center;
    color: #666;
    padding: 2rem;
}

@media (max-width: 768px) {
    .time-slots {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 0.8rem;
    }
    
    .navigation-buttons {
        padding: 0 1rem;
    }
    
    .btn {
        width: 100%;
        margin: 0.5rem 0;
        text-align: center;
    }
    
    .btn-secondary {
        width: 100%;
        font-size: 0.95rem;
        padding: 0.75rem 1rem;
    }
}
</style>

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