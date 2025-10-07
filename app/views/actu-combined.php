<?php 
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/flash-messages.php';
?>

<main class="container">
    <div class="tabs-container">
        <!-- Navigation des onglets -->
        <div class="tabs-nav">
            <button class="tab-button" data-tab="tab-public">Actualités</button>
            <button class="tab-button" data-tab="tab-admin">Gestion des actualités</button>
        </div>

        <!-- Contenu de l'onglet "Actualités" -->
        <div id="tab-public" class="tab-content">
            <section class="actus-list">
                <?php if (empty($actus)): ?>
                    <p>Aucune actualité n'est disponible pour le moment.</p>
                <?php else: ?>
                    <?php foreach ($actus as $actu): ?>
                        <article class="actu-card">
                            <div class="actu-content">
                                <h2><?php echo htmlspecialchars($actu['titre']); ?></h2>
                                <div class="actu-meta">
                                    <span class="actu-date">
                                        Publié le <?php echo date('d/m/Y', strtotime($actu['date_publication'])); ?>
                                    </span>
                                    <span class="actu-author">
                                        par <?php echo htmlspecialchars($actu['auteur_prenom'] . ' ' . $actu['auteur_nom']); ?>
                                    </span>
                                </div>
                                <p><?php echo nl2br(htmlspecialchars($actu['contenu'])); ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </div>

        <!-- Contenu de l'onglet "Gestion des actualités" -->
        <div id="tab-admin" class="tab-content">
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
                    <?php if (empty($actusAdmin)): ?>
                        <tr>
                            <td colspan="4">Aucune actualité n'est disponible</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($actusAdmin as $actu): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($actu['titre']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($actu['date_publication'])); ?></td>
                                <td><?php echo htmlspecialchars($actu['statut']); ?></td>
                                <td>
                                    <a href="index.php?page=actus&action=edit&id=<?php echo $actu['id']; ?>" class="btn-admin edit">Modifier</a>
                                    <a href="index.php?page=actus&action=delete&id=<?php echo $actu['id']; ?>" 
                                       class="btn-admin delete" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette actualité ?');">
                                        Supprimer
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="<?php echo BASE_URL; ?>/js/tabs.js"></script>
<script src="<?php echo BASE_URL; ?>/js/actu-posts.js"></script>

<?php include __DIR__ . '/templates/footer.php'; ?>