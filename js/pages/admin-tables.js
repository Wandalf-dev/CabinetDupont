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
    window.deleteService = async function(id) {
        const confirmed = await showConfirmationPopup('Êtes-vous sûr de vouloir supprimer ce service ?');
        if (confirmed) {
            window.location.href = `index.php?page=services&action=delete&id=${id}`;
        }
    }

    window.deleteActu = async function(id) {
        const confirmed = await showConfirmationPopup('Êtes-vous sûr de vouloir supprimer cette actualité ?');
        if (confirmed) {
            window.location.href = `index.php?page=actus&action=delete&id=${id}`;
        }
    }

    window.deletePatient = async function(id) {
        const confirmed = await showConfirmationPopup('Êtes-vous sûr de vouloir supprimer ce patient ?');
        if (confirmed) {
            window.location.href = `index.php?page=admin&action=deletePatient&id=${id}`;
        }
    }

    // La fonction deleteCreneau a été déplacée vers creneaux-delete.js

    // Fonctions de filtrage
    window.filterServices = function() {
        const serviceInput = document.getElementById('service-filter-input');
        const serviceTable = document.getElementById('services-table');
        
        if (serviceInput && serviceTable) {
            const filter = serviceInput.value.toLowerCase();
            const tbody = serviceTable.querySelector('tbody');
            const rows = tbody ? tbody.querySelectorAll('tr') : serviceTable.querySelectorAll('tr');
            
            rows.forEach((row) => {
                // Chercher dans la cellule titre (class="title-cell") et description
                const titleCell = row.querySelector('.title-cell, td[data-label="Titre"]');
                const descCell = row.querySelector('td[data-label="Description"]');
                
                let shouldShow = false;
                
                if (titleCell && descCell) {
                    const titre = titleCell.textContent.toLowerCase();
                    const desc = descCell.textContent.toLowerCase();
                    shouldShow = (filter === '' || titre.includes(filter) || desc.includes(filter));
                } else {
                    // Fallback: chercher dans tout le texte de la ligne (sauf les actions)
                    const actionsCell = row.querySelector('.actions-cell');
                    let searchText = row.textContent.toLowerCase();
                    if (actionsCell) {
                        searchText = searchText.replace(actionsCell.textContent.toLowerCase(), '');
                    }
                    shouldShow = (filter === '' || searchText.includes(filter));
                }
                
                // Utiliser une classe au lieu de style.display
                if (shouldShow) {
                    row.classList.remove('filtered-hidden');
                } else {
                    row.classList.add('filtered-hidden');
                }
            });
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
            const tbody = patientsTable.querySelector('tbody');
            const rows = tbody ? tbody.querySelectorAll('tr') : patientsTable.querySelectorAll('tr');
            
            rows.forEach(row => {
                // Utiliser data-label pour trouver les bonnes cellules
                const nomCell = row.querySelector('td[data-label="Nom"]');
                const prenomCell = row.querySelector('td[data-label="Prénom"]');
                const emailCell = row.querySelector('td[data-label="Email"]');
                const telCell = row.querySelector('td[data-label="Téléphone"]');
                
                let shouldShow = false;
                
                if (nomCell && prenomCell && emailCell && telCell) {
                    const nom = nomCell.textContent.toLowerCase();
                    const prenom = prenomCell.textContent.toLowerCase();
                    const email = emailCell.textContent.toLowerCase();
                    const tel = telCell.textContent.toLowerCase();
                    
                    shouldShow = (filter === '' || 
                        nom.includes(filter) || 
                        prenom.includes(filter) || 
                        email.includes(filter) || 
                        tel.includes(filter));
                } else {
                    // Fallback: chercher dans tout le texte de la ligne (sauf les actions)
                    const actionsCell = row.querySelector('.actions-cell');
                    let searchText = row.textContent.toLowerCase();
                    if (actionsCell) {
                        searchText = searchText.replace(actionsCell.textContent.toLowerCase(), '');
                    }
                    shouldShow = (filter === '' || searchText.includes(filter));
                }
                
                // Utiliser une classe au lieu de style.display
                if (shouldShow) {
                    row.classList.remove('filtered-hidden');
                } else {
                    row.classList.add('filtered-hidden');
                }
            });
        });
    }

    // Filtre actualités
    const actusInput = document.getElementById('actus-filter-input');
    const actusTable = document.getElementById('actus-table');
    if (actusInput && actusTable) {
        actusInput.addEventListener('input', function() {
            const filter = actusInput.value.toLowerCase();
            const tbody = actusTable.querySelector('tbody');
            const rows = tbody ? tbody.querySelectorAll('tr') : actusTable.querySelectorAll('tr');
            
            rows.forEach(row => {
                // Chercher dans la cellule titre
                const titreCell = row.querySelector('td[data-label="Titre"]');
                
                let shouldShow = false;
                
                if (titreCell) {
                    const text = titreCell.textContent.toLowerCase();
                    shouldShow = (filter === '' || text.includes(filter));
                } else {
                    // Fallback: chercher dans tout le texte de la ligne (sauf les actions)
                    const actionsCell = row.querySelector('.actions-cell');
                    let searchText = row.textContent.toLowerCase();
                    if (actionsCell) {
                        searchText = searchText.replace(actionsCell.textContent.toLowerCase(), '');
                    }
                    shouldShow = (filter === '' || searchText.includes(filter));
                }
                
                // Utiliser une classe au lieu de style.display
                if (shouldShow) {
                    row.classList.remove('filtered-hidden');
                } else {
                    row.classList.add('filtered-hidden');
                }
            });
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