<style>
/* Tri visuel pour la table des patients */
/* Tri visuel pour la table des patients */
#patients-table th.sortable {
    cursor: pointer;
    user-select: none;
    position: relative;
    padding: 12px 25px 12px 12px;
    background-color: #f8f9fa;
    transition: background-color 0.2s ease;
}

#patients-table th.sortable:hover {
    background-color: #e9ecef;
}

#patients-table th .sort-icon {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    width: 12px;
    height: 12px;
    line-height: 12px;
    text-align: center;
    font-size: 12px;
    color: #6c757d;
    transition: all 0.2s ease;
    opacity: 0.5;
}

#patients-table th.sortable:hover .sort-icon {
    opacity: 1;
}

#patients-table th.sorted-asc, #patients-table th.sorted-desc {
    background-color: #e3f2fd;
    color: #0d6efd;
    font-weight: 600;
}

#patients-table th.sorted-asc .sort-icon i::before {
    content: "\f0de"; /* fa-sort-up */
    color: #0d6efd;
    opacity: 1;
}

#patients-table th.sorted-desc .sort-icon i::before {
    content: "\f0dd"; /* fa-sort-down */
    color: #0d6efd;
    opacity: 1;
}

#patients-table th.sortable:not(.sorted-asc):not(.sorted-desc) .sort-icon i::before {
    content: "\f0dc"; /* fa-sort */
    opacity: 0.5;
}
</style>
<?php
// Inclusion du header et des messages flash (succès/erreur)
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/flash-messages.php';
?>

<main class="container">

    <div class="tabs-container">
        <!-- Navigation des onglets pour basculer entre les différentes gestions admin -->
        <div class="tabs-nav">
            <button class="tab-button" data-tab="tab-services">Gestion des services</button>
            <button class="tab-button" data-tab="tab-actus">Gestion des actualités</button>
            <button class="tab-button" data-tab="tab-horaires">Gestion des horaires</button>
            <button class="tab-button" data-tab="tab-patients">Gestion des patients</button>
            <button class="tab-button" data-tab="tab-creneaux">Gestion des créneaux</button>
        </div>

        <!-- Onglet "Services" : gestion des services du cabinet -->
        <div class="tab-content" id="tab-services">
            <div class="admin-section p-4">
                <div class="admin-toolbar mb-4">
                    <div class="admin-filter">
                        <!-- Champ de recherche pour filtrer les services -->
                        <input type="text" id="service-filter-input" placeholder="Rechercher un service..." class="service-search form-control">
                    </div>
                    <div class="admin-actions">
                        <!-- Bouton pour ajouter un nouveau service -->
                        <a href="index.php?page=services&action=create" class="btn-admin add">
                            <i class="fas fa-plus"></i>&nbsp;Ajouter un service
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="services-table" class="admin-table table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 50px"></th>
                                <th>Titre</th>
                                <th>Description</th>
                                <th>Durée</th>
                                <th>Statut</th>
                                <th class="actions-header">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($servicesAdmin as $service): ?>
                            <tr data-id="<?= htmlspecialchars($service['id']) ?>" draggable="true">
                                <td class="grip-cell"><span class="grip-icon" title="Glisser pour réorganiser">⋮⋮</span></td>
                                <td><?= htmlspecialchars($service['titre']) ?></td>
                                <td><?= htmlspecialchars(substr($service['description'], 0, 100)) ?>...</td>
                                <td><?= htmlspecialchars($service['duree']) ?> min</td>
                                <td class="status-cell" data-status="<?= htmlspecialchars($service['statut']) ?>"><?= htmlspecialchars($service['statut']) ?></td>
                                <td class="actions-cell">
                                    <!-- Bouton pour modifier le service -->
                                    <a href="index.php?page=services&action=edit&id=<?= $service['id'] ?>" class="btn-admin edit">
                                        <i class="fas fa-edit"></i>&nbsp;Modifier
                                    </a>
                                    <!-- Bouton pour supprimer le service -->
                                    <button onclick="deleteService(<?= $service['id'] ?>)" class="btn-admin delete">
                                        <i class="fas fa-trash"></i>&nbsp;Supprimer
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Onglet "Actualités" : gestion des actualités du cabinet -->
        <div class="tab-content" id="tab-actus">
            <div class="admin-section p-4">
                <div class="admin-toolbar mb-4">
                    <div class="admin-filter">
                        <!-- Champ de recherche pour filtrer les actualités -->
                        <input type="text" id="actus-filter-input" placeholder="Rechercher une actualité..." class="actu-search form-control">
                    </div>
                    <div class="admin-actions">
                        <!-- Bouton pour ajouter une nouvelle actualité -->
                        <a href="index.php?page=actus&action=create" class="btn-admin add">
                            <i class="fas fa-plus"></i>&nbsp;Ajouter une actualité
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="actus-table" class="admin-table table table-hover">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th class="actions-header">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($actusAdmin as $actu): ?>
                            <tr data-id="<?= htmlspecialchars($actu['id']) ?>">
                                <td><?= htmlspecialchars($actu['titre']) ?></td>
                                <td><?= htmlspecialchars(date('d/m/Y', strtotime($actu['date_publication']))) ?></td>
                                <td class="status-cell" data-status="<?= htmlspecialchars($actu['statut']) ?>"><?= htmlspecialchars($actu['statut']) ?></td>
                                <td class="actions-cell">
                                    <!-- Bouton pour modifier l'actualité -->
                                    <a href="index.php?page=actus&action=edit&id=<?= $actu['id'] ?>" class="btn-admin edit">
                                        <i class="fas fa-edit"></i>&nbsp;Modifier
                                    </a>
                                    <!-- Formulaire pour supprimer l'actualité avec protection CSRF -->
                                    <form method="post" action="index.php?page=actus&action=delete&id=<?= $actu['id'] ?>" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">
                                        <button type="submit" class="btn-admin delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette actualité ?');">
                                            <i class="fas fa-trash"></i>&nbsp;Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Onglet "Horaires" : gestion des horaires -->
        <div class="tab-content" id="tab-horaires">
            <div class="admin-section">
                <form class="horaires-form" method="post" action="index.php?page=horaires&action=edit">
                    <!-- Champ caché pour le token CSRF (sécurité) -->
                    <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">
                    <?php 
                    $joursFr = [
                        ['lundi' => 'Lundi', 'mardi' => 'Mardi'],
                        ['mercredi' => 'Mercredi', 'jeudi' => 'Jeudi'],
                        ['vendredi' => 'Vendredi', 'samedi' => 'Samedi'],
                        ['dimanche' => 'Dimanche']
                    ];

                    foreach ($joursFr as $paire):
                        foreach ($paire as $jourEn => $jourFr): ?>
                        <div class="horaire-edit-card">
                            <h3><?php echo htmlspecialchars($jourFr); ?></h3>
                            <div class="horaire-section">
                                <h4>Matin</h4>
                                <div class="horaire-inputs">
                                    <div class="time-input">
                                        <label for="ouverture_matin_<?php echo htmlspecialchars($jourEn); ?>">Ouverture</label>
                                        <input type="time" 
                                               id="ouverture_matin_<?php echo htmlspecialchars($jourEn); ?>" 
                                               name="horaires[<?php echo htmlspecialchars($jourEn); ?>][matin][ouverture]" 
                                               value="<?php 
                                                    if (isset($horaires)) {
                                                        foreach ($horaires as $horaire) {
                                                            if ($horaire['jour'] === $jourEn) {
                                                                echo htmlspecialchars($horaire['ouverture_matin'] ?? '');
                                                                break;
                                                            }
                                                        }
                                                    }
                                               ?>">
                                    </div>
                                    <div class="time-input">
                                        <label for="fermeture_matin_<?php echo htmlspecialchars($jourEn); ?>">Fermeture</label>
                                        <input type="time" 
                                               id="fermeture_matin_<?php echo htmlspecialchars($jourEn); ?>" 
                                               name="horaires[<?php echo htmlspecialchars($jourEn); ?>][matin][fermeture]" 
                                               value="<?php 
                                                    if (isset($horaires)) {
                                                        foreach ($horaires as $horaire) {
                                                            if ($horaire['jour'] === $jourEn) {
                                                                echo htmlspecialchars($horaire['fermeture_matin'] ?? '');
                                                                break;
                                                            }
                                                        }
                                                    }
                                               ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="horaire-section">
                                <h4>Après-midi</h4>
                                <div class="horaire-inputs">
                                    <div class="time-input">
                                        <label for="ouverture_apresmidi_<?php echo htmlspecialchars($jourEn); ?>">Ouverture</label>
                                        <input type="time" 
                                               id="ouverture_apresmidi_<?php echo htmlspecialchars($jourEn); ?>" 
                                               name="horaires[<?php echo htmlspecialchars($jourEn); ?>][apresmidi][ouverture]" 
                                               value="<?php 
                                                    if (isset($horaires)) {
                                                        foreach ($horaires as $horaire) {
                                                            if ($horaire['jour'] === $jourEn) {
                                                                echo htmlspecialchars($horaire['ouverture_apresmidi'] ?? '');
                                                                break;
                                                            }
                                                        }
                                                    }
                                               ?>">
                                    </div>
                                    <div class="time-input">
                                        <label for="fermeture_apresmidi_<?php echo htmlspecialchars($jourEn); ?>">Fermeture</label>
                                        <input type="time" 
                                               id="fermeture_apresmidi_<?php echo htmlspecialchars($jourEn); ?>" 
                                               name="horaires[<?php echo htmlspecialchars($jourEn); ?>][apresmidi][fermeture]" 
                                               value="<?php 
                                                    if (isset($horaires)) {
                                                        foreach ($horaires as $horaire) {
                                                            if ($horaire['jour'] === $jourEn) {
                                                                echo htmlspecialchars($horaire['fermeture_apresmidi'] ?? '');
                                                                break;
                                                            }
                                                        }
                                                    }
                                               ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endforeach;
                    endforeach; 
                    ?>

                    <div class="form-actions mt-4">
                        <button type="submit" class="btn-admin save">
                            <i class="fas fa-save"></i>&nbsp;Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Onglet "Patients" : gestion des patients -->
        <div class="tab-content" id="tab-patients">
            <div class="admin-section p-4">
                <div class="admin-toolbar mb-4">
                    <div class="admin-filter">
                        <!-- Champ de recherche pour filtrer les patients -->
                        <input type="text" id="patient-filter-input" placeholder="Rechercher un patient..." class="patient-search form-control">
                    </div>
                    <div class="admin-actions">
                        <!-- Bouton pour ajouter un nouveau patient -->
                        <a href="index.php?page=admin&action=addPatient" class="btn-admin add">
                            <i class="fas fa-user-plus"></i>&nbsp;Ajouter un patient
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="patients-table" class="admin-table table table-hover">
                        <thead>
                            <tr>
                                <th class="sortable" data-sort="nom"><span>Nom</span><i class="fas fa-sort"></i></th>
                                <th class="sortable" data-sort="prenom"><span>Prénom</span><i class="fas fa-sort"></i></th>
                                <th class="sortable" data-sort="email"><span>Email</span><i class="fas fa-sort"></i></th>
                                <th class="sortable" data-sort="telephone"><span>Téléphone</span><i class="fas fa-sort"></i></th>
                                <th class="sortable" data-sort="date_naissance"><span>Date de naissance</span><i class="fas fa-sort"></i></th>
                                <th class="actions-header">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($patientsAdmin as $patient): ?>
                            <tr data-id="<?= htmlspecialchars($patient['id']) ?>" data-creation="<?= htmlspecialchars($patient['date_creation']) ?>">
                                <td><?= htmlspecialchars($patient['nom']) ?></td>
                                <td><?= htmlspecialchars($patient['prenom']) ?></td>
                                <td><?= htmlspecialchars($patient['email']) ?></td>
                                <td>
                                    <?php
                                    $tel = $patient['telephone'];
                                    if ($tel && strpos($tel, '+33') !== 0) {
                                        // Ajoute le préfixe +33 si absent et le numéro commence par 0
                                        if (substr($tel, 0, 1) === '0') {
                                            $tel = '+33-' . substr($tel, 1);
                                        } else {
                                            $tel = '+33-' . $tel;
                                        }
                                    }
                                    echo htmlspecialchars($tel);
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $date = $patient['date_naissance'];
                                    $timestamp = strtotime($date);
                                    // Affiche vide si date invalide ou nulle
                                    if ($date && $timestamp && $timestamp > 0) {
                                        echo htmlspecialchars(date('d/m/Y', $timestamp));
                                    } else {
                                        echo '';
                                    }
                                    ?>
                                </td>
                                <td class="actions-cell">
                                    <!-- Bouton pour modifier le patient -->
                                    <a href="index.php?page=admin&action=editPatient&id=<?= $patient['id'] ?>" class="btn-admin edit">
                                        <i class="fas fa-edit"></i>&nbsp;Modifier
                                    </a>
                                    <!-- Bouton pour supprimer le patient -->
                                    <button onclick="deletePatient(<?= $patient['id'] ?>)" class="btn-admin delete">
                                        <i class="fas fa-trash"></i>&nbsp;Supprimer
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Onglet "Créneaux" : gestion des créneaux de consultation -->
        <div class="tab-content" id="tab-creneaux">
            <div class="admin-section p-4">
                <div class="admin-actions mb-4">
                    <!-- Bouton pour générer de nouveaux créneaux -->
                    <a href="index.php?page=creneaux&action=generer" class="btn-admin add">
                        <i class="fas fa-calendar-plus"></i>&nbsp;Générer des créneaux
                    </a>
                </div>

                <!-- Section pour afficher les créneaux existants -->
                <div class="table-responsive">
                    <?php if (!empty($creneaux)): ?>
                        <table class="admin-table table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Heure de début</th>
                                    <th>Heure de fin</th>
                                    <th>Statut</th>
                                    <th class="actions-header">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($creneaux as $creneau): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($creneau['date'])); ?></td>
                                        <td><?php echo date('H:i', strtotime($creneau['heure_debut'])); ?></td>
                                        <td><?php echo date('H:i', strtotime($creneau['heure_fin'])); ?></td>
                                        <td>
                                            <?php if ($creneau['disponible']): ?>
                                                <span class="badge disponible">Disponible</span>
                                            <?php else: ?>
                                                <span class="badge reserve">Réservé</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="actions-cell">
                                            <?php if ($creneau['disponible']): ?>
                                                <form method="post" action="index.php?page=creneaux&action=supprimer" style="display:inline;">
                                                    <input type="hidden" name="id" value="<?php echo $creneau['id']; ?>">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                    <button type="submit" class="btn-admin delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce créneau ?');">
                                                        <i class="fas fa-trash"></i>&nbsp;Supprimer
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-center">Aucun créneau disponible. Cliquez sur "Générer des créneaux" pour en créer.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Styles pour l'interface admin -->
<link rel="stylesheet" href="css/admin.css">
<link rel="stylesheet" href="css/drag-drop.css">
<link rel="stylesheet" href="css/tabs.css">
<link rel="stylesheet" href="css/table-actions.css">

<!-- Scripts pour la gestion des onglets, du drag & drop et des filtres -->
<script src="js/tabs.js"></script>
<script src="js/service-order.js"></script>
<script>
// Tri des colonnes de la table patients
document.addEventListener('DOMContentLoaded', function() {
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
});
function deleteService(id) {
    // Confirmation avant suppression d'un service
    if (confirm('Êtes-vous sûr de vouloir supprimer ce service ?')) {
        window.location.href = `index.php?page=services&action=delete&id=${id}`;
    }
}
function deleteActu(id) {
    // Confirmation avant suppression d'une actualité
    if (confirm('Êtes-vous sûr de vouloir supprimer cette actualité ?')) {
        window.location.href = `index.php?page=actus&action=delete&id=${id}`;
    }
}
function deletePatient(id) {
    // Confirmation avant suppression d'un patient
    if (confirm('Êtes-vous sûr de vouloir supprimer ce patient ?')) {
        window.location.href = `index.php?page=admin&action=deletePatient&id=${id}`;
    }
}
// Recherche services et actualités
function filterServices() {
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
function initActusFilter() {
    const actusInput = document.getElementById('actus-filter-input');
    const actusTable = document.getElementById('actus-table');
    if (actusInput && actusTable) {
        actusInput.addEventListener('input', function() {
            const filter = actusInput.value.toLowerCase();
            for (const row of actusTable.tBodies[0].rows) {
                const text = row.cells[0].textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            }
        });
    }
}
document.addEventListener('DOMContentLoaded', function() {
    // Filtre patients à chaque input
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

    // Filtre services à chaque input
    const serviceInput = document.getElementById('service-filter-input');
    if (serviceInput) {
        serviceInput.addEventListener('input', filterServices);
    }
    // Filtre services à chaque affichage d'onglet
    document.querySelector('[data-tab="tab-services"]').addEventListener('click', function() {
        setTimeout(filterServices, 100);
    });
    // Filtre actualités à chaque input
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
    document.querySelector('[data-tab="tab-actus"]').addEventListener('click', function() {
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
});
</script>