<?php
// Inclusion du header du site
include __DIR__ . '/templates/header.php'; 
?>
<main>
  <section class="actu-create-section">
    <!-- Formulaire pour modifier une actualité existante -->
    <form class="actu-create-form" method="post" enctype="multipart/form-data" action="index.php?page=actus&action=edit&id=<?php echo htmlspecialchars($actu['id']); ?>">
      <!-- Champ caché pour le token CSRF (sécurité) -->
      <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">
      <h2>Modifier l'actualité</h2>
      <div class="actu-create-field">
        <label for="titre">Titre de l'actualité <span class="required-star">*</span></label>
        <input type="text" id="titre" name="titre" required value="<?php echo htmlspecialchars($actu['titre'] ?? ''); ?>" />
      </div>
      <div class="actu-create-field">
        <label for="contenu">Contenu <span class="required-star">*</span></label>
        <textarea id="contenu" name="contenu" rows="6" required><?php echo htmlspecialchars($actu['contenu'] ?? ''); ?></textarea>
      </div>
      <div class="actu-create-field">
        <label for="statut">Statut <span class="required-star">*</span></label>
        <select id="statut" name="statut" required>
          <option value="BROUILLON" <?php echo ($actu['statut'] === 'BROUILLON') ? 'selected' : ''; ?>>Brouillon</option>
          <option value="PUBLIE" <?php echo ($actu['statut'] === 'PUBLIE') ? 'selected' : ''; ?>>Publié</option>
          <option value="ARCHIVE" <?php echo ($actu['statut'] === 'ARCHIVE') ? 'selected' : ''; ?>>Archivé</option>
        </select>
      </div>
      <!-- Champ caché pour l'id de l'actualité -->
      <input type="hidden" name="id" value="<?php echo htmlspecialchars($actu['id'] ?? ''); ?>" />
      <div class="actu-create-field">
        <label for="image">Image d'illustration</label>
        <input type="file" id="image" name="image" accept="image/*">
        <?php if (!empty($actu['image'])): ?>
          <!-- Affichage de l'image actuelle de l'actualité -->
          <div style="margin-top:0.5em;">
            <img src="/cabinetdupont/public/uploads/<?php echo htmlspecialchars($actu['image']); ?>" alt="Image actuelle" style="max-width:180px;max-height:120px;border-radius:8px;box-shadow:0 2px 8px #0001;">
            <br><small>Image actuelle</small>
          </div>
        <?php endif; ?>
      </div>
      <div class="actu-btn-row">
        <button type="submit" class="btn-actu-create">Enregistrer les modifications</button>
        <a href="index.php?page=admin" class="btn-actu-cancel">Annuler</a>
      </div>
    </form>
  </section>
</main>
<?php 
// Inclusion du footer du site
include __DIR__ . '/templates/footer.php'; 
?>