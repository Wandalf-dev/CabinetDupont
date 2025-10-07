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
            <?php if (!empty($latestActu['image'])): ?>
              <div class="actu-featured-image">
                <img src="public/uploads/<?php echo htmlspecialchars($latestActu['image']); ?>" alt="Image de l'actualité" style="max-width:100%;height:auto;margin-bottom:1rem;">
              </div>
            <?php endif; ?>
            <h3><?php echo htmlspecialchars($latestActu['titre']); ?></h3>
            <p><?php echo htmlspecialchars(substr(strip_tags($latestActu['contenu']), 0, 400)) . '...'; ?></p>
            <div class="actu-footer">
              <a href="index.php?page=actus&action=show&id=<?php echo $latestActu['id']; ?>" class="btn btn-primary">Lire la suite</a>
              <div class="actu-meta">
                <span class="actu-date"><?php echo date('d/m/Y', strtotime($latestActu['date_publication'])); ?></span>
                <span class="actu-author">Par <?php echo htmlspecialchars($latestActu['auteur_prenom'] . ' ' . $latestActu['auteur_nom']); ?></span>
              </div>
            </div>
          </div>
        </article>

        <!-- Autres actualités -->
        <div class="actu-list">
          <?php foreach ($actus as $actu): ?>
            <article class="actu-card">
              <h3><?php echo htmlspecialchars($actu['titre']); ?></h3>
              <p><?php echo htmlspecialchars(substr(strip_tags($actu['contenu']), 0, 200)) . '...'; ?></p>
              <div class="actu-footer">
                <a href="index.php?page=actus&action=show&id=<?php echo $actu['id']; ?>" class="btn btn-primary">Lire la suite</a>
                <div class="actu-meta">
                  <span class="actu-date"><?php echo date('d/m/Y', strtotime($actu['date_publication'])); ?></span>
                  <span class="actu-author">Par <?php echo htmlspecialchars($actu['auteur_prenom'] . ' ' . $actu['auteur_nom']); ?></span>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php include __DIR__ . '/templates/footer.php'; ?>
