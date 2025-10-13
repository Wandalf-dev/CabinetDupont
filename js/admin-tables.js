document.addEventListener('DOMContentLoaded', function() {
    // Tri des colonnes de la table patients
    const table = document.getElementById('patients-table');
    if (!table) return;

    function getCellValue(row, index) {
        const cell = row.children[index];
        return cell ? cell.textContent.trim() : '';
    }

    function compareCells(a, b, isDate) {
        if (isDate) {
            const [aDay, aMonth, aYear] = a.split('/').map(Number);
            const [bDay, bMonth, bYear] = b.split('/').map(Number);
            return new Date(aYear, aMonth - 1, aDay) - new Date(bYear, bMonth - 1, bDay);
        }
        return isNaN(a) || isNaN(b) ? 
               a.localeCompare(b, undefined, {numeric: true, sensitivity: 'base'}) : 
               Number(a) - Number(b);
    }

    table.querySelectorAll('th.sortable').forEach((th, idx) => {
        let firstClick = true;

        th.addEventListener('click', () => {
            const tbody = table.tBodies[0];
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const isDate = th.dataset.sort === 'date_naissance';

            // Réinitialiser les autres en-têtes
            table.querySelectorAll('th.sortable').forEach(header => {
                if (header !== th) {
                    header.classList.remove('sorted-asc', 'sorted-desc');
                }
            });

            let isAsc;
            if (firstClick) {
                isAsc = true;
                firstClick = false;
            } else {
                isAsc = !th.classList.contains('sorted-asc');
            }

            // Trier les lignes
            rows.sort((a, b) => {
                const aValue = getCellValue(a, idx);
                const bValue = getCellValue(b, idx);
                const comparison = compareCells(aValue, bValue, isDate);
                return isAsc ? comparison : -comparison;
            });

            // Appliquer le tri
            tbody.append(...rows);

            // Mettre à jour l'état visuel
            th.classList.remove('sorted-asc', 'sorted-desc');
            th.classList.add(isAsc ? 'sorted-asc' : 'sorted-desc');
        });
    });

    // Fonctions de suppression
    window.deleteService = function(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce service ?')) {
            window.location.href = `index.php?page=services&action=delete&id=${id}`;
        }
    }

    window.deleteActu = function(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette actualité ?')) {
            window.location.href = `index.php?page=actus&action=delete&id=${id}`;
        }
    }

    window.deletePatient = function(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce patient ?')) {
            window.location.href = `index.php?page=admin&action=deletePatient&id=${id}`;
        }
    }

    window.deleteCreneau = function(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce créneau ?')) {
            window.location.href = `index.php?page=creneaux&action=delete&id=${id}`;
        }
    }

    // Fonctions de filtrage
    window.filterServices = function() {
        const serviceInput = document.getElementById('service-filter-input');
        const serviceTable = document.getElementById('services-table');
        if (serviceInput && serviceTable) {
            const filter = serviceInput.value.toLowerCase();
            const rows = serviceTable.tBodies[0] ? serviceTable.tBodies[0].rows : serviceTable.rows;
            for (const row of rows) {
                if (row.cells.length < 3) continue;
                const titre = row.cells[1].textContent.toLowerCase();
                const desc = row.cells[2].textContent.toLowerCase();
                if (filter === '' || titre.includes(filter) || desc.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        }
    }

    // Gestionnaire des filtres
    const serviceInput = document.getElementById('service-filter-input');
    if (serviceInput) {
        serviceInput.addEventListener('input', filterServices);
    }

    const tabServices = document.querySelector('[data-tab="tab-services"]');
    if (tabServices) {
        tabServices.addEventListener('click', () => setTimeout(filterServices, 100));
    }

    // Filtre patients
    const patientInput = document.getElementById('patient-filter-input');
    const patientsTable = document.getElementById('patients-table');
    if (patientInput && patientsTable) {
        patientInput.addEventListener('input', function() {
            const filter = patientInput.value.toLowerCase();
            const rows = patientsTable.tBodies[0].rows;
            for (const row of rows) {
                const nom = row.cells[0].textContent.toLowerCase();
                const prenom = row.cells[1].textContent.toLowerCase();
                const email = row.cells[2].textContent.toLowerCase();
                const tel = row.cells[3].textContent.toLowerCase();
                if (filter === '' || 
                    nom.includes(filter) || 
                    prenom.includes(filter) || 
                    email.includes(filter) || 
                    tel.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }

    // Filtre actualités
    const actusInput = document.getElementById('actus-filter-input');
    const actusTable = document.getElementById('actus-table');
    if (actusInput && actusTable) {
        actusInput.addEventListener('input', function() {
            const filter = actusInput.value.toLowerCase();
            const rows = actusTable.tBodies[0] ? actusTable.tBodies[0].rows : actusTable.rows;
            for (const row of rows) {
                if (row.cells.length < 1) continue;
                const text = row.cells[0].textContent.toLowerCase();
                if (filter === '' || text.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }

    const tabActus = document.querySelector('[data-tab="tab-actus"]');
    if (tabActus) {
        tabActus.addEventListener('click', function() {
            setTimeout(function() {
                if (actusInput && actusTable) {
                    const filter = actusInput.value.toLowerCase();
                    const rows = actusTable.tBodies[0] ? actusTable.tBodies[0].rows : actusTable.rows;
                    for (const row of rows) {
                        if (row.cells.length < 1) continue;
                        const text = row.cells[0].textContent.toLowerCase();
                        if (filter === '' || text.includes(filter)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                }
            }, 100);
        });
    }
});