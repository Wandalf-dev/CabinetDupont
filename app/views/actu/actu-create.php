<?php
// Inclusion du header et des messages flash (succès/erreur)
include __DIR__ . '/../templates/header.php';
include __DIR__ . '/../templates/flash-messages.php';
?>
<main>
  <section class="actu-create-section">
    <!-- Formulaire pour créer une nouvelle actualité -->
    <form class="actu-create-form" method="post" action="index.php?page=actus&action=create" enctype="multipart/form-data">
      <!-- Champ caché pour le token CSRF (sécurité) -->
      <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">
      <h2>Nouvelle actualité</h2>
      <div class="actu-create-field">
        <label for="titre">Titre de l'actualité <span class="required-star">*</span></label>
        <input type="text" id="titre" name="titre" required placeholder="Titre de l'article" />
      </div>
      <div class="actu-create-field">
        <label for="contenu">Contenu <span class="required-star">*</span></label>
        <textarea id="contenu" name="contenu" rows="6" required placeholder="Rédigez votre actualité..."></textarea>
      </div>
      <div class="actu-create-field">
        <label for="image">Image (optionnelle)</label>
        <input type="file" id="image" name="image" accept="image/*">
      </div>
      <div class="actu-btn-row">
        <button type="submit" class="btn-actu-create">Publier l'actualité</button>
        <a href="index.php?page=admin" class="btn-actu-cancel">Annuler</a>
      </div>
    </form>
  </section>
</main>
<?php 
// Inclusion du footer du site
include __DIR__ . '/../templates/footer.php'; 
?>