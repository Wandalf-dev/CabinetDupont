<?php 
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/flash-messages.php';
?>

<main>
  <section class="actu-section">
    <h2>Actualités du cabinet</h2>
    <div class="actu-list">
      <?php if (empty($actus)): ?>
        <p class="no-actus">Aucune actualité n'est disponible pour le moment.</p>
      <?php else: ?>
        <?php foreach ($actus as $actu): ?>
          <article class="actu-card">
            <h3><?php echo htmlspecialchars($actu['titre']); ?></h3>
            <p><?php echo htmlspecialchars(substr(strip_tags($actu['contenu']), 0, 200)) . '...'; ?></p>
            <div class="actu-meta">
              <span class="actu-date"><?php echo date('d/m/Y', strtotime($actu['date_publication'])); ?></span>
              <span class="actu-author">Par <?php echo htmlspecialchars($actu['auteur_prenom'] . ' ' . $actu['auteur_nom']); ?></span>
            </div>
            <a href="index.php?page=actus&action=show&id=<?php echo $actu['id']; ?>" class="btn btn-primary">Lire la suite</a>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php include __DIR__ . '/templates/footer.php'; ?>
