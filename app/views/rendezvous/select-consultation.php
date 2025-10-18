<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/flash-messages.php';
?>

<!-- CSS spécifiques aux rendez-vous -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/modules/rendez-vous/select-consultation.css">

<main class="container">
    <section class="select-consultation">
        <h1 class="page-title">
            <i class="fas fa-stethoscope"></i>
            Choisissez votre motif de consultation
        </h1>
        
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