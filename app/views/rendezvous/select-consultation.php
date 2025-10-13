<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/flash-messages.php';
?>

<main class="container">
    <section class="select-consultation">
        <h2 class="section-title">Veuillez choisir le motif de consultation</h2>
        
        <div class="services-grid">
            <?php foreach ($services as $service) : ?>
                <div class="service-card" data-service-id="<?= $service['id'] ?>">
                    <div class="service-image">
                        <img src="/CabinetDupont/public/uploads/<?= htmlspecialchars($service['image']) ?>" 
                             alt="<?= htmlspecialchars($service['titre']) ?>"
                             loading="lazy">
                    </div>
                    <div class="service-content">
                        <h3><?= htmlspecialchars($service['titre']) ?></h3>
                        <p><?= htmlspecialchars($service['description']) ?></p>
                        <button class="btn btn-primary select-service-btn">
                            Choisir ce motif
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<style>
/* On réutilise les styles des services de la page d'accueil */
.select-consultation {
    padding: 2rem 0;
}

.section-title {
    font-size: clamp(2rem, 3vw, 2.5rem);
    color: #1976d2;
    font-weight: 700;
    text-align: center;
    margin: 0 0 3rem 0;
    letter-spacing: 1px;
    position: relative;
}

.section-title::after {
    content: '';
    display: block;
    margin: 0.7rem auto 0 auto;
    width: 60px;
    height: 5px;
    background: linear-gradient(90deg, #1976d2 40%, #64b5f6 100%);
    border-radius: 3px;
    opacity: 0.8;
}

/* Animation au survol des cartes */
.service-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

/* Style du bouton */
.select-service-btn {
    width: 100%;
    margin-top: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.select-service-btn i {
    transition: transform 0.3s ease;
}

.select-service-btn:hover i {
    transform: translateX(5px);
}
</style>

<script>
document.querySelectorAll('.select-service-btn').forEach(button => {
    button.addEventListener('click', function() {
        const serviceId = this.closest('.service-card').dataset.serviceId;
        window.location.href = `index.php?page=rendezvous&action=selectDate&service_id=${serviceId}`;
    });
});
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>