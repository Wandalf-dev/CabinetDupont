<?php include __DIR__ . '/templates/header.php'; ?>
<main>
  <section class="actu-create-section">
    <form class="actu-create-form" method="post" action="index.php?page=actus&action=edit&id=<?php echo htmlspecialchars($actu['id']); ?>">
      <h2>Modifier l'actualité</h2>
      <div class="actu-create-field">
        <label for="titre">Titre de l'actualité</label>
        <input type="text" id="titre" name="titre" required value="<?php echo htmlspecialchars($actu['titre'] ?? ''); ?>" />
      </div>
      <div class="actu-create-field">
        <label for="contenu">Contenu</label>
        <textarea id="contenu" name="contenu" rows="6" required><?php echo htmlspecialchars($actu['contenu'] ?? ''); ?></textarea>
      </div>
      <input type="hidden" name="id" value="<?php echo htmlspecialchars($actu['id'] ?? ''); ?>" />
      <button type="submit" class="btn-actu-create">Enregistrer les modifications</button>
    </form>
  </section>
</main>
<?php include 'footer.php'; ?>
