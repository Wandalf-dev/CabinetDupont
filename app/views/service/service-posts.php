<?php 
include __DIR__ . '/../templates/header.php';
include __DIR__ . '/../templates/flash-messages.php';
?>
<main>
  <section class="admin-section">
    <h2 class="section-title">Gestion des services</h2>
    <div class="admin-toolbar">
      <div class="admin-filter">
        <input type="text" id="filter-input" placeholder="Filtrer par titre..." />
      </div>
      <div class="admin-actions">
        <a href="index.php?page=services&action=create" class="btn-admin add">+ Ajouter un service</a>
      </div>
    </div>
    <table class="admin-table" id="admin-table">
      <thead>
        <tr>
          <th class="sortable" data-sort="titre">Titre <span class="sort-icon">↕</span></th>
          <th class="sortable" data-sort="statut">État <span class="sort-icon">↕</span></th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($services)): ?>
          <tr>
            <td colspan="3">Aucun service n'est disponible</td>
          </tr>
        <?php else: ?>
          <?php foreach ($services as $service): ?>
            <tr>
              <td><?php echo htmlspecialchars($service['titre']); ?></td>
              <td><?php echo htmlspecialchars($service['statut']); ?></td>
              <td>
                <a href="index.php?page=services&action=edit&id=<?php echo $service['id']; ?>" class="btn-admin edit">Modifier</a>
                <a href="index.php?page=services&action=delete&id=<?php echo $service['id']; ?>" class="btn-admin delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce service ?');">Supprimer</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </section>
</main>
<script src="<?php echo BASE_URL; ?>/js/service-posts.js"></script>
<?php include __DIR__ . '/../templates/footer.php'; ?>