<?php 
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/flash-messages.php';
?>
<link rel="stylesheet" href="css/detail-actu.css?v=<?php echo time(); ?>">
<main>
  <section class="actu-section">
    <div class="actu-detail-container">
      <?php if (empty($actu)): ?>
        <p class="no-actus">Aucune actualité n'est disponible pour le moment.</p>
      <?php else: ?>
        <article class="actu-detail">
          <div class="actu-detail-flex<?php echo empty($actu['image']) ? ' no-image' : ''; ?>">
            <?php if (!empty($actu['image'])): ?>
              <div class="actu-detail-image">
                <img src="/cabinetdupont/public/uploads/<?php echo htmlspecialchars($actu['image']); ?>" alt="Image de l'actualité">
              </div>
            <?php endif; ?>
            <div class="actu-detail-content-block">
              <h3><?php echo htmlspecialchars($actu['titre']); ?></h3>
              <div class="actu-detail-content">
                <p><?php echo nl2br(htmlspecialchars($actu['contenu'])); ?></p>
              </div>
              <div class="actu-detail-meta">
                <span class="actu-date">Publié le <?php echo date('d/m/Y', strtotime($actu['date_publication'])); ?></span>
                <span class="actu-author meta-author">par <?php echo htmlspecialchars($actu['auteur_prenom'] . ' ' . $actu['auteur_nom']); ?></span>
              </div>
            </div>
          </div>
        </article>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php include __DIR__ . '/templates/footer.php'; ?>
