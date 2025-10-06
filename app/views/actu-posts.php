<?php include __DIR__ . '/templates/header.php'; ?>
<main>
  <section class="admin-section">
    <h2>Gestion des actualités</h2>
    <div class="admin-toolbar">
      <div class="admin-filter">
        <input type="text" id="filter-input" placeholder="Filtrer par titre ou date..." />
      </div>
      <div class="admin-actions">
        <a href="#" class="btn-admin add">+ Ajouter un article</a>
      </div>
    </div>
    <table class="admin-table" id="admin-table">
      <thead>
        <tr>
          <th>Titre</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Ouverture d’un nouveau fauteuil</td>
          <td>05/10/2025</td>
          <td>
            <a href="#" class="btn-admin edit">Modifier</a>
            <a href="#" class="btn-admin delete">Supprimer</a>
          </td>
        </tr>
        <tr>
          <td>Congés annuels</td>
          <td>01/10/2025</td>
          <td>
            <a href="#" class="btn-admin edit">Modifier</a>
            <a href="#" class="btn-admin delete">Supprimer</a>
          </td>
        </tr>
        <tr>
          <td>Nouveau service : blanchiment dentaire</td>
          <td>15/09/2025</td>
          <td>
            <a href="#" class="btn-admin edit">Modifier</a>
            <a href="#" class="btn-admin delete">Supprimer</a>
          </td>
        </tr>
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
<?php include 'footer.php'; ?>
