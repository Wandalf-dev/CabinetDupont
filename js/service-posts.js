document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('filter-input');
    const table = document.getElementById('admin-table');
    let currentSort = { column: null, asc: true };

    // Fonction de filtrage
    input.addEventListener('input', function() {
      const filter = input.value.toLowerCase();
      for (const row of table.tBodies[0].rows) {
        const text = row.cells[0].textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
      }
    });

    // Fonction de tri
    function sortTable(column) {
      const tbody = table.tBodies[0];
      const rows = Array.from(tbody.rows);
      
      // Réinitialiser les icônes de tri
      document.querySelectorAll('.sort-icon').forEach(icon => {
        icon.textContent = '↕';
      });
      
      // Mettre à jour l'icône de la colonne triée
      const th = document.querySelector(`[data-sort="${column}"]`);
      const icon = th.querySelector('.sort-icon');
      
      // Inverser le tri si on clique sur la même colonne
      if (currentSort.column === column) {
        currentSort.asc = !currentSort.asc;
      } else {
        currentSort.column = column;
        currentSort.asc = true;
      }
      
      // Mettre à jour l'icône
      icon.textContent = currentSort.asc ? '↓' : '↑';

      // Trier les lignes
      rows.sort((a, b) => {
        let valA, valB;
        
        switch(column) {
          case 'titre':
            valA = a.cells[0].textContent.toLowerCase();
            valB = b.cells[0].textContent.toLowerCase();
            break;
          case 'statut':
            valA = a.cells[1].textContent.toLowerCase();
            valB = b.cells[1].textContent.toLowerCase();
            break;
        }
        
        if (valA < valB) return currentSort.asc ? -1 : 1;
        if (valA > valB) return currentSort.asc ? 1 : -1;
        return 0;
      });

      // Réorganiser le tableau
      rows.forEach(row => tbody.appendChild(row));
    }

    // Ajouter les écouteurs d'événements pour le tri
    document.querySelectorAll('.sortable').forEach(th => {
      th.addEventListener('click', () => {
        const column = th.getAttribute('data-sort');
        sortTable(column);
      });
    });
});