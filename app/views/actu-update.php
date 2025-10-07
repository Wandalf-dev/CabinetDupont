<?php 
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/flash-messages.php';
?>

<main class="container">
  <section class="actu-create-section">
    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <form class="actu-create-form" method="post" action="index.php?page=actus&action=edit&id=<?php echo $actu->getId(); ?>">
      <input type="hidden" name="_token" value="<?php echo $_SESSION['_token'] ?? ''; ?>">
      
      <div class="section-header">
        <h2>Modifier l'actualité</h2>
      </div>

      <div class="actu-create-field">
        <label for="titre">Titre de l'actualité</label>
        <input type="text" id="titre" name="titre" required 
               value="<?php echo htmlspecialchars($actu->getTitre()); ?>" />
      </div>

      <div class="actu-create-field">
        <label for="contenu">Contenu</label>
        <textarea id="contenu" name="contenu" rows="6" required><?php echo htmlspecialchars($actu->getContenu()); ?></textarea>
      </div>

      <div class="actu-create-field">
        <label for="statut">Statut</label>
        <select id="statut" name="statut" required>
          <option value="BROUILLON" <?php echo ($actu->getStatut() === 'BROUILLON') ? 'selected' : ''; ?>>Brouillon</option>
          <option value="PUBLIE" <?php echo ($actu->getStatut() === 'PUBLIE') ? 'selected' : ''; ?>>Publié</option>
          <option value="ARCHIVE" <?php echo ($actu->getStatut() === 'ARCHIVE') ? 'selected' : ''; ?>>Archivé</option>
        </select>
      </div>

      <input type="hidden" name="id" value="<?php echo $actu->getId(); ?>" />
      
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        <a href="index.php?page=actus" class="btn btn-secondary">Annuler</a>
      </div>
    </form>
  </section>
</main>

<?php include __DIR__ . '/templates/footer.php'; ?>
