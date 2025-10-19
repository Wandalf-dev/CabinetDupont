<?php
// Inclusion du header et des messages flash (succès/erreur)
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/flash-messages.php';
?>

<!-- CSS spécifiques à la page admin -->
<link rel="stylesheet" href="/cabinetdupont/css/pages/admin.css">
<link rel="stylesheet" href="/cabinetdupont/css/components/tabs.css">
<link rel="stylesheet" href="/cabinetdupont/css/components/table-sort.css">
<link rel="stylesheet" href="/cabinetdupont/css/components/table-actions.css">
<link rel="stylesheet" href="/cabinetdupont/css/modules/horaires/horaires.css">
<link rel="stylesheet" href="/cabinetdupont/css/modules/horaires/horaires-admin.css">

<main class="container">

    <div class="tabs-container">
        <!-- Navigation des onglets pour basculer entre les différentes gestions admin -->
        <div class="tabs-nav">
            <button class="tab-button" data-tab="tab-services">Services</button>
            <button class="tab-button" data-tab="tab-actus">Actualités</button>
            <button class="tab-button" data-tab="tab-horaires">Horaires</button>
            <button class="tab-button" data-tab="tab-patients">Patients</button>
            <button class="tab-button" data-tab="tab-creneaux">Créneaux</button>
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
                                <td class="grip-cell desktop-only" data-label=""><span class="grip-icon" title="Glisser pour réorganiser">⋮⋮</span></td>
                                <td class="title-cell" data-label="Titre"><?= htmlspecialchars($service['titre']) ?></td>
                                <td data-label="Description"><?= htmlspecialchars(substr($service['description'], 0, 100)) ?>...</td>
                                <td data-label="Durée"><?= htmlspecialchars($service['duree']) ?> min</td>
                                <td class="status-cell" data-label="Statut" data-status="<?= htmlspecialchars($service['statut']) ?>"><?= htmlspecialchars($service['statut']) ?></td>
                                <td class="actions-cell" data-label="Actions">
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
                                <td data-label="Titre"><?= htmlspecialchars($actu['titre']) ?></td>
                                <td data-label="Date"><?= htmlspecialchars(date('d/m/Y', strtotime($actu['date_publication']))) ?></td>
                                <td class="status-cell" data-label="Statut" data-status="<?= htmlspecialchars($actu['statut']) ?>"><?= htmlspecialchars($actu['statut']) ?></td>
                                <td class="actions-cell" data-label="Actions">
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
                                <td data-label="Nom"><?= htmlspecialchars($patient['nom']) ?></td>
                                <td data-label="Prénom"><?= htmlspecialchars($patient['prenom']) ?></td>
                                <td data-label="Email"><?= htmlspecialchars($patient['email']) ?></td>
                                <td data-label="Téléphone">
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
                                <td data-label="Date de naissance">
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
                                <td class="actions-cell" data-label="Actions">
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
            <?php require_once __DIR__ . '/admin/sections/creneaux-section.php'; ?>
        </div>

<!-- Styles spécifiques pour l'interface admin -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/admin.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/admin-cell-order.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/admin-grip-fix.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/admin-buttons-fix.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/admin-mobile-enhancements.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/components/confirmation-popup.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/modules/agenda/notifications.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/utils/drag-drop.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/patient.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/components/button-group.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/creneaux/creneaux.css">

<!-- Scripts spécifiques pour l'interface admin -->
<script src="<?php echo BASE_URL; ?>/js/components/confirmation-popup.js"></script>
<script src="<?php echo BASE_URL; ?>/js/components/tabs.js"></script>
<script src="<?php echo BASE_URL; ?>/js/modules/service/service-order.js"></script>
<script src="<?php echo BASE_URL; ?>/js/pages/admin-tables.js"></script>
<script src="<?php echo BASE_URL; ?>/js/pages/admin-mobile-enhancements.js"></script>
<script src="<?php echo BASE_URL; ?>/js/modules/creneaux/creneaux.js"></script>

    </div> <!-- Fermeture de tabs-container -->
</main>

<?php require_once __DIR__ . '/templates/footer.php'; ?>