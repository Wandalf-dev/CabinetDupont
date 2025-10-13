<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/flash-messages.php';
?>

<main class="container">
    <section class="select-time">
        <h2 class="section-title">Choisissez un horaire</h2>
        <p class="selected-date">
            Horaires disponibles pour le <?= (new DateTime($_GET['date']))->format('d/m/Y') ?>
        </p>
        
        <div class="time-slots-wrapper">
            <div class="time-slots">
                <!-- Les créneaux seront affichés ici -->
                <?php foreach ($availableSlots as $slot): ?>
                <button class="time-slot" data-time="<?= htmlspecialchars($slot) ?>">
                    <?= htmlspecialchars($slot) ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
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
    padding: 1rem;
    border: 2px solid #1976d2;
    border-radius: 8px;
    background: white;
    color: #1976d2;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.time-slot:hover {
    background: #1976d2;
    color: white;
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .time-slots {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 0.8rem;
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
        window.location.href = `index.php?page=rendezvous&action=confirm&service_id=${serviceId}&date=${date}&time=${time}`;
    });
});
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>