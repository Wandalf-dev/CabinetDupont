<?php 
include __DIR__ . '/../templates/header.php';
include __DIR__ . '/../templates/flash-messages.php';
?>
<style>
.grip-cell, .grip-cell .grip-icon {
    cursor: move !important;
}
</style>
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
          <th style="width:40px;"></th>
          <th>Titre</th>
          <th>État</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($servicesAdmin)): ?>
          <tr>
            <td colspan="4">Aucun service n'est disponible</td>
          </tr>
        <?php else: ?>
          <?php foreach ($servicesAdmin as $service): ?>
            <tr draggable="true" data-id="<?php echo $service['id']; ?>">
              <td class="grip-cell"><span class="grip-icon">&#8942;&#8942;</span></td>
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
<script src="/cabinetdupont/js/service-order.js"></script>
<?php include __DIR__ . '/../templates/footer.php'; ?>