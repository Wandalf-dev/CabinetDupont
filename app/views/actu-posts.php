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
          <th>Titre</th>
          <th>Date</th>
          <th>État</th>
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
<script>
  // Filtrage instantané du tableau
  document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('filter-input');
    const table = document.getElementById('admin-table');
    input.addEventListener('input', function() {
      const filter = input.value.toLowerCase();
      for (const row of table.tBodies[0].rows) {
        const text = (row.cells[0].textContent + ' ' + row.cells[1].textContent).toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
      }
    });
  });
</script>
<?php include __DIR__ . '/templates/footer.php'; ?>
