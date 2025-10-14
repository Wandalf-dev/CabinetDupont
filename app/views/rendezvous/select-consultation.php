<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/flash-messages.php';
?>

<style>
.select-consultation {
    padding: 2rem 0;
}

.services-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    padding: 1rem;
    max-width: 1200px;
    margin: 0 auto;
}

.service-card-wrapper {
    flex: 1 1 250px;
    min-width: 250px;
    max-width: calc(33.333% - 1rem);
    cursor: move;
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

.service-card {
    position: relative;
    min-height: 400px;
    padding-bottom: 60px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.service-card.dragging {
    transform: scale(1.02);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    opacity: 0.9;
}

.service-image {
    width: 100%;
    height: 150px;
    flex-shrink: 0;
}

.service-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.service-content {
    padding: 1rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
}

.service-content h3 {
    color: #1976d2;
    margin-bottom: 0.75rem;
    font-size: 1.25rem;
}

.service-content p {
    margin-bottom: 0.5rem;
    color: #333;
    font-size: 0.95rem;
    flex: 1;
    overflow-y: auto;
}

.select-service-btn {
    position: absolute;
    bottom: 1rem;
    left: 1rem;
    right: 1rem;
    margin-top: auto;
    width: calc(100% - 3rem);
}
</style>

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



<script>
// Récupérer le BASE_URL PHP dans une variable JavaScript
const baseUrl = '<?php echo BASE_URL; ?>';
console.log('Base URL:', baseUrl);

document.querySelectorAll('.select-service-btn').forEach(button => {
    button.addEventListener('click', function() {
        const serviceId = this.closest('.service-card').dataset.serviceId;
        const url = `${baseUrl}/index.php?page=rendezvous&action=selectDate&service_id=${serviceId}`;
        console.log('URL générée:', url);
        console.log('Service ID:', serviceId);
        window.location.href = url;
    });
});
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>