<?php
// Inclusion du header et des messages flash (succès/erreur)
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/flash-messages.php';
?>
<link rel="stylesheet" href="/cabinetdupont/css/service-create.css">
<style>
/* Style pour limiter la largeur du formulaire */
.actu-create-section {
  max-width: 1100px !important;
}
</style>

<section class="actu-create-section">
    <!-- Formulaire pour ajouter un nouveau service -->
    <form class="actu-create-form" method="post" enctype="multipart/form-data" action="index.php?page=services&action=create">
        <!-- Champ caché pour le token CSRF (sécurité) -->
        <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">
        <h2>Ajouter un service</h2>
        <div class="form-group">
            <label for="titre">Nom du service</label>
            <input type="text" id="titre" name="titre" required value="<?php echo htmlspecialchars($formData['titre'] ?? ''); ?>" />
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($formData['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="duree">Durée de consultation (en minutes)</label>
            <input type="number" id="duree" name="duree" min="15" max="180" step="15" value="<?php echo htmlspecialchars($formData['duree'] ?? '30'); ?>" required>
            <small class="form-text text-muted">Durée minimale : 15 min, maximale : 3h, par paliers de 15 min</small>
        </div>

        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" id="image" name="image" accept="image/*" onchange="previewServiceImage(event)" required>
            <!-- Zone d'aperçu de l'image sélectionnée -->
            <div id="service-image-preview" style="margin-top:1em;"></div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-admin add">Ajouter le service</button>
            <a href="index.php?page=admin" class="btn-admin">Annuler</a>
        </div>
    </form>
</section>

<script>
// Fonction pour afficher un aperçu de l'image sélectionnée
function previewServiceImage(event) {
    const preview = document.getElementById('service-image-preview');
    preview.innerHTML = '';
    const file = event.target.files[0];
    if (file) {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.style.maxWidth = '160px';
        img.style.maxHeight = '120px';
        img.style.borderRadius = '8px';
        img.style.boxShadow = '0 2px 8px #0001';
        preview.appendChild(img);
    }
}
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>