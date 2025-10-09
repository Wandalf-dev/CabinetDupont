<?php 
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/flash-messages.php';
?>

<main class="container">
    <div class="tabs-container">
        <!-- Navigation des onglets -->
        <div class="tabs-nav">
            <button class="tab-button" data-tab="tab-services">Gestion des services</button>
            <button class="tab-button" data-tab="tab-actus">Gestion des actualités</button>
        </div>

        <!-- Contenu des onglets -->
        <div class="tab-content" id="tab-services">
            <div class="admin-section p-4">
                <div class="admin-toolbar mb-4">
                    <div class="admin-filter">
                        <input type="text" placeholder="Rechercher un service..." class="service-search form-control">
                    </div>
                    <div class="admin-actions">
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
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($servicesAdmin as $service): ?>
                            <tr data-id="<?= htmlspecialchars($service['id']) ?>" draggable="true">
                                <td class="grip-cell"><span class="grip-icon" style="cursor: grab;">⋮⋮</span></td>
                                <td><?= htmlspecialchars($service['titre']) ?></td>
                                <td><?= htmlspecialchars(substr($service['description'], 0, 100)) ?>...</td>
                                <td class="status-cell" data-status="<?= htmlspecialchars($service['statut']) ?>"><?= htmlspecialchars($service['statut']) ?></td>
                                <td class="actions-cell">
                                    <a href="index.php?page=services&action=edit&id=<?= $service['id'] ?>" class="btn-admin edit">
                                        <i class="fas fa-edit"></i>&nbsp;Modifier
                                    </a>
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

        <div class="tab-content" id="tab-actus">
            <div class="admin-section p-4">
                <div class="admin-toolbar mb-4">
                    <div class="admin-filter">
                        <input type="text" placeholder="Rechercher une actualité..." class="actu-search form-control">
                    </div>
                    <div class="admin-actions">
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($actusAdmin as $actu): ?>
                            <tr data-id="<?= htmlspecialchars($actu['id']) ?>">
                                <td><?= htmlspecialchars($actu['titre']) ?></td>
                                <td><?= htmlspecialchars(date('d/m/Y', strtotime($actu['date_publication']))) ?></td>
                                <td class="status-cell" data-status="<?= htmlspecialchars($actu['statut']) ?>"><?= htmlspecialchars($actu['statut']) ?></td>
                                <td class="actions-cell">
                                    <a href="index.php?page=actus&action=edit&id=<?= $actu['id'] ?>" class="btn-admin edit">
                                        <i class="fas fa-edit"></i>&nbsp;Modifier
                                    </a>
                                    <button onclick="deleteActu(<?= $actu['id'] ?>)" class="btn-admin delete">
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
    </div>
</main>

<!-- Styles -->
<link rel="stylesheet" href="css/admin.css">
<link rel="stylesheet" href="css/drag-drop.css">
<link rel="stylesheet" href="css/tabs.css">

<!-- Scripts -->
<script src="js/tabs.js"></script>
<script src="js/service-order.js"></script>
<script>
function deleteService(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce service ?')) {
        window.location.href = `index.php?page=services&action=delete&id=${id}`;
    }
}
function deleteActu(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette actualité ?')) {
        window.location.href = `index.php?page=actus&action=delete&id=${id}`;
    }
}
</script>


