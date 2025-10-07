<?php 
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/flash-messages.php';
?>
<main>
  <section class="actu-create-section">
    <form class="actu-create-form" method="post" action="index.php?page=actus&action=create">
      <h2>Nouvelle actualité</h2>
      <div class="actu-create-field">
        <label for="titre">Titre de l'actualité</label>
        <input type="text" id="titre" name="titre" required placeholder="Titre de l'article" />
      </div>
      <div class="actu-create-field">
        <label for="contenu">Contenu</label>
        <textarea id="contenu" name="contenu" rows="6" required placeholder="Rédigez votre actualité..."></textarea>
      </div>
      <button type="submit" class="btn-actu-create">Publier l'actualité</button>
    </form>
  </section>
</main>
<?php include 'footer.php'; ?>
