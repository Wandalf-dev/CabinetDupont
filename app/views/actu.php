<?php 
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/flash-messages.php';
?>

<main>
  <section class="actu-section">
    <h2>Actualités du cabinet</h2>
    <div class="actu-container">
      <?php if (empty($actus)): ?>
        <p class="no-actus">Aucune actualité n'est disponible pour le moment.</p>
      <?php else: ?>
        <?php $latestActu = array_shift($actus); // Récupère la première actualité ?>
        <!-- Dernière actualité mise en avant -->

        <article class="actu-featured">
          <div class="actu-featured-content">
            <h3><?php echo htmlspecialchars($latestActu->getTitre()); ?></h3>
            <p><?php echo htmlspecialchars(substr(strip_tags($latestActu->getContenu()), 0, 400)) . '...'; ?></p>
            <div class="actu-footer">
              <a href="index.php?page=actus&action=show&id=<?php echo $latestActu->getId(); ?>" class="btn btn-primary">Lire la suite</a>
              <div class="actu-meta">
                <span class="actu-date"><?php echo $latestActu->getDatePublication()->format('d/m/Y'); ?></span>
                <span class="actu-author">Par <?php echo htmlspecialchars($latestActu->getAuteurPrenom() . ' ' . $latestActu->getAuteurNom()); ?></span>
              </div>
            </div>
          </div>
        </article>

        <!-- Autres actualités -->
        <div class="actu-list">
          <?php foreach ($actus as $actu): ?>
            <article class="actu-card">
              <h3><?php echo htmlspecialchars($actu->getTitre()); ?></h3>
              <p><?php echo htmlspecialchars(substr(strip_tags($actu->getContenu()), 0, 200)) . '...'; ?></p>
              <div class="actu-footer">
                <a href="index.php?page=actus&action=show&id=<?php echo $actu->getId(); ?>" class="btn btn-primary">Lire la suite</a>
                <div class="actu-meta">
                  <span class="actu-date"><?php echo $actu->getDatePublication()->format('d/m/Y'); ?></span>
                  <span class="actu-author">Par <?php echo htmlspecialchars($actu->getAuteurPrenom() . ' ' . $actu->getAuteurNom()); ?></span>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php include __DIR__ . '/templates/footer.php'; ?>
