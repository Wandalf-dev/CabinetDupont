<?php 
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/flash-messages.php';
?>
<link rel="stylesheet" href="/cabinetdupont/css/service-create.css">
<style>
.actu-create-section {
  max-width: 1100px !important;
}
</style>

<section class="actu-create-section">
    <form class="actu-create-form" method="post" enctype="multipart/form-data" action="index.php?page=services&action=create">
    <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">
        <h2>Ajouter un service</h2>
        <div class="form-group">
            <label for="titre">Nom du service <span class="required-star">*</span></label>
            <input type="text" id="titre" name="titre" required value="<?php echo htmlspecialchars($formData['titre'] ?? ''); ?>" />
        </div>

        <div class="form-group">
            <label for="description">Description <span class="required-star">*</span></label>
            <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($formData['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="prix">Prix (€) <span class="required-star">*</span></label>
            <input type="number" id="prix" name="prix" step="0.01" required value="<?php echo htmlspecialchars($formData['prix'] ?? ''); ?>" />
        </div>

        <div class="form-group">
            <label for="duree">Durée (minutes) <span class="required-star">*</span></label>
            <input type="number" id="duree" name="duree" required value="<?php echo htmlspecialchars($formData['duree'] ?? ''); ?>" />
        </div>

        <div class="form-group">
            <label for="image">Image <span class="required-star">*</span></label>
            <input type="file" id="image" name="image" accept="image/*" onchange="previewServiceImage(event)" required>
            <div id="service-image-preview" style="margin-top:1em;"></div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-admin add">Ajouter le service</button>
            <a href="index.php?page=services" class="btn-admin">Annuler</a>
        </div>
    </form>
</section>

<script>
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