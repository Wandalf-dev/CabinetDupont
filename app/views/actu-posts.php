<?php 
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/flash-messages.php';
?>
<main>
  <section class="admin-section">
    <h2 class="section-title">Gestion des actualités</h2>
    <div class="admin-toolbar">
      <div class="admin-filter">
        <input type="text" id="filter-input" placeholder="Filtrer par titre ou date..." />
      </div>
      <div class="admin-actions">
        <a href="index.php?page=actus&action=create" class="btn-admin add">+ Ajouter un article</a>
      </div>
    </div>
    <table class="admin-table" id="admin-table">
      <thead>
        <tr>
          <th class="sortable" data-sort="titre">Titre <span class="sort-icon">↕</span></th>
          <th class="sortable" data-sort="date">Date <span class="sort-icon">↕</span></th>
          <th class="sortable" data-sort="statut">État <span class="sort-icon">↕</span></th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($actus)): ?>
          <tr>
            <td colspan="4">Aucune actualité n'est disponible</td>
          </tr>
        <?php else: ?>
          <?php foreach ($actus as $actu): ?>
            <tr>
              <td><?php echo htmlspecialchars($actu['titre']); ?></td>
              <td><?php echo date('d/m/Y', strtotime($actu['date_publication'])); ?></td>
              <td><?php echo htmlspecialchars($actu['statut']); ?></td>
              <td>
                <a href="index.php?page=actus&action=edit&id=<?php echo $actu['id']; ?>" class="btn-admin edit">Modifier</a>
                <a href="index.php?page=actus&action=delete&id=<?php echo $actu['id']; ?>" class="btn-admin delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette actualité ?');">Supprimer</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </section>
</main>
<script src="<?php echo BASE_URL; ?>/js/actu-posts.js"></script>
<?php include __DIR__ . '/templates/footer.php'; ?>
