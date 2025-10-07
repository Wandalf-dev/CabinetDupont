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
                        <section class="actu-section">
                                <h2>Actualités du cabinet</h2>
                                <div class="actu-container">
                                    <?php if (empty($actus)): ?>
                                        <p class="no-actus">Aucune actualité n'est disponible pour le moment.</p>
                                    <?php else: ?>
                                        <?php $latestActu = array_shift($actus); ?>
                                        <!-- Dernière actualité mise en avant -->
                                        <article class="actu-featured">
                                            <div class="actu-featured-content">
                                                <h3><?php echo htmlspecialchars($latestActu->getTitre()); ?></h3>
                                                <p><?php echo htmlspecialchars(substr(strip_tags($latestActu->getContenu()), 0, 400)) . '...'; ?></p>
                                                <div class="actu-footer">
                                                    <a href="index.php?page=actus&action=show&id=<?php echo $latestActu->getId(); ?>" class="btn btn-primary">Lire la suite</a>
                                                    <div class="actu-meta">
                                                        <span class="actu-date"><?php echo $latestActu->getDatePublication()->format('d/m/Y'); ?></span>
                                                        <span class="actu-author">Par <?php echo htmlspecialchars($latestActu->getAuteurPrenom() . ' ' . $latestActu->getAuteurNom()); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </article>
                                        <!-- Autres actualités -->
                                        <div class="actu-list">
                                            <?php foreach ($actus as $actu): ?>
                                                <article class="actu-card">
                                                    <h3><?php echo htmlspecialchars($actu->getTitre()); ?></h3>
                                                    <p><?php echo htmlspecialchars(substr(strip_tags($actu->getContenu()), 0, 200)) . '...'; ?></p>
                                                    <div class="actu-footer">
                                                        <a href="index.php?page=actus&action=show&id=<?php echo $actu->getId(); ?>" class="btn btn-primary">Lire la suite</a>
                                                        <div class="actu-meta">
                                                            <span class="actu-date"><?php echo $actu->getDatePublication()->format('d/m/Y'); ?></span>
                                                            <span class="actu-author">Par <?php echo htmlspecialchars($actu->getAuteurPrenom() . ' ' . $actu->getAuteurNom()); ?></span>
                                                        </div>
                                                    </div>
                                                </article>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
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
                                <td><?php echo htmlspecialchars($actu->getTitre()); ?></td>
                                <td><?php echo $actu->getDatePublication()->format('d/m/Y'); ?></td>
                                <td><?php echo htmlspecialchars($actu->getStatut()); ?></td>
                                <td>
                                    <a href="index.php?page=actus&action=edit&id=<?php echo $actu->getId(); ?>" class="btn-admin edit">Modifier</a>
                                    <a href="index.php?page=actus&action=delete&id=<?php echo $actu->getId(); ?>" 
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