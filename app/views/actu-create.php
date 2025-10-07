<?php 
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/flash-messages.php';
?>
<main>
  <section class="actu-create-section">
    <form class="actu-create-form" method="post" action="index.php?page=actus&action=create">
      <input type="hidden" name="_token" value="<?php echo $_SESSION['_token'] ?? ''; ?>">
      <h2>Nouvelle actualité</h2>
      <div class="actu-create-field">
        <label for="titre">Titre de l'actualité</label>
        <input type="text" id="titre" name="titre" required 
               value="<?php echo htmlspecialchars($formData['titre'] ?? ''); ?>"
               placeholder="Titre de l'article" />
      </div>
      <div class="actu-create-field">
        <label for="contenu">Contenu</label>
        <textarea id="contenu" name="contenu" rows="6" required 
                  placeholder="Rédigez votre actualité..."><?php echo htmlspecialchars($formData['contenu'] ?? ''); ?></textarea>
      </div>
      <div class="actu-create-field">
        <label for="statut">Statut</label>
        <select id="statut" name="statut">
          <option value="PUBLIE" <?php echo ($formData['statut'] ?? '') === 'PUBLIE' ? 'selected' : ''; ?>>Publié</option>
          <option value="BROUILLON" <?php echo ($formData['statut'] ?? '') === 'BROUILLON' ? 'selected' : ''; ?>>Brouillon</option>
        </select>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Publier l'actualité</button>
        <a href="index.php?page=actus" class="btn btn-secondary">Annuler</a>
      </div>
    </form>
  </section>
</main>
<?php include __DIR__ . '/templates/footer.php'; ?>
