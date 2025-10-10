<?php 
// Inclusion du header et des messages flash (succès/erreur)
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/flash-messages.php';
?>
<link rel="stylesheet" href="/cabinetdupont/css/service-update.css">

<section class="service-update-section">
    <!-- Formulaire pour modifier un service existant -->
    <form class="service-update-form" method="post" enctype="multipart/form-data" action="index.php?page=services&action=edit&id=<?php echo htmlspecialchars($service['id']); ?>">
        <!-- Champ caché pour le token CSRF (sécurité) -->
        <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">
        <h2>Modifier le service</h2>
        <div class="form-group">
            <label for="titre">Nom du service</label>
            <input type="text" id="titre" name="titre" required value="<?php echo htmlspecialchars($service['titre'] ?? ''); ?>" />
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($service['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" id="image" name="image" accept="image/*" onchange="previewServiceImage(event)">
            <small class="form-text text-muted">Laissez vide pour conserver l'image actuelle</small>
            <?php if (!empty($service['image'])): ?>
                <!-- Affichage de l'image actuelle du service -->
                <div style="margin-top:1em;">
                    <img src="/cabinetdupont/public/uploads/<?php echo htmlspecialchars($service['image']); ?>" alt="Image actuelle" style="max-width:320px;max-height:240px;border-radius:8px;box-shadow:0 2px 8px #0001;">
                    <br><small>Image actuelle</small>
                </div>
                <!-- Champ caché pour conserver l'image actuelle -->
                <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($service['image']); ?>">
            <?php endif; ?>
            <!-- Zone d'aperçu de la nouvelle image sélectionnée -->
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
            <a href="index.php?page=admin" class="btn-admin">Annuler</a>
        </div>
    </form>
</section>

<script>
// Fonction pour afficher un aperçu de la nouvelle image sélectionnée
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