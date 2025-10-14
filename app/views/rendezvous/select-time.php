<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/flash-messages.php';
?>

<main class="container">
    <section class="select-time">
        <h2 class="section-title">Choisissez un horaire</h2>
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
                        <?php foreach ($availableSlots as $slot): ?>
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
    margin-top: 2rem;
    text-align: center;
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
    background: #f5f5f5;
    color: #333;
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