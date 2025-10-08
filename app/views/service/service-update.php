<?php 
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/flash-messages.php';
?>
<link rel="stylesheet" href="/cabinetdupont/css/service-update.css">

<section class="service-update-section">
    <form class="service-update-form" method="post" enctype="multipart/form-data" action="index.php?page=services&action=edit&id=<?php echo htmlspecialchars($service['id']); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">
        <h2>Modifier le service</h2>
        <div class="form-group">
            <label for="titre">Nom du service <span class="required-star">*</span></label>
            <input type="text" id="titre" name="titre" required value="<?php echo htmlspecialchars($service['titre'] ?? ''); ?>" />
        </div>

        <div class="form-group">
            <label for="description">Description <span class="required-star">*</span></label>
            <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($service['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="prix">Prix (€) <span class="required-star">*</span></label>
            <input type="number" id="prix" name="prix" step="0.01" required value="<?php echo htmlspecialchars($service['prix'] ?? ''); ?>" />
        </div>

        <div class="form-group">
            <label for="duree">Durée (minutes) <span class="required-star">*</span></label>
            <input type="number" id="duree" name="duree" required value="<?php echo htmlspecialchars($service['duree'] ?? ''); ?>" />
        </div>

        <div class="form-group">
            <label for="image">Image <span class="required-star">*</span></label>
            <input type="file" id="image" name="image" accept="image/*" onchange="previewServiceImage(event)" required>
            <?php if (!empty($service['image'])): ?>
                <div style="margin-top:1em;">
                    <img src="/cabinetdupont/public/uploads/<?php echo htmlspecialchars($service['image']); ?>" alt="Image actuelle" style="max-width:320px;max-height:240px;border-radius:8px;box-shadow:0 2px 8px #0001;">
                    <br><small>Image actuelle</small>
                </div>
            <?php endif; ?>
            <div id="service-image-preview" style="margin-top:1em;"></div>
        </div>

        <div class="form-group">
            <label for="statut">Statut</label>
            <select id="statut" name="statut" required>
                <option value="PUBLIE" <?php echo ($service['statut'] === 'PUBLIE') ? 'selected' : ''; ?>>Publié</option>
                <option value="BROUILLON" <?php echo ($service['statut'] === 'BROUILLON') ? 'selected' : ''; ?>>Brouillon</option>
                <option value="ARCHIVE" <?php echo ($service['statut'] === 'ARCHIVE') ? 'selected' : ''; ?>>Archivé</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-admin add">Enregistrer les modifications</button>
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
    img.style.maxWidth = '320px';
    img.style.maxHeight = '240px';
        img.style.borderRadius = '8px';
        img.style.boxShadow = '0 2px 8px #0001';
        preview.appendChild(img);
    }
}
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>