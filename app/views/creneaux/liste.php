<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/flash-messages.php';
?>

<main class="container">
    <section class="admin-section">
        <h2>Liste des créneaux disponibles</h2>

        <div class="filters">
            <form method="get" class="form-inline">
                <input type="hidden" name="page" value="creneaux">
                <input type="hidden" name="action" value="liste">
                
                <div class="form-group">
                    <label for="date">Date :</label>
                    <input type="date" id="date" name="date" 
                           value="<?php echo isset($_GET['date']) ? htmlspecialchars($_GET['date']) : date('Y-m-d'); ?>"
                           min="<?php echo date('Y-m-d'); ?>">
                </div>

                <button type="submit" class="btn-filter">Filtrer</button>
            </form>
        </div>

        <?php if (!empty($creneaux)): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Heure de début</th>
                            <th>Heure de fin</th>
                            <th>Statut</th>
                            <th>Actions</th>
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
                                <td class="actions">
                                    <?php if ($creneau['disponible']): ?>
                                        <form method="post" action="index.php?page=creneaux&action=supprimer"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce créneau ?');">
                                            <input type="hidden" name="id" value="<?php echo $creneau['id']; ?>">
                                            <button type="submit" class="btn-action delete" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="no-results">Aucun créneau disponible pour cette date.</p>
        <?php endif; ?>

        <div class="admin-actions">
            <a href="index.php?page=creneaux&action=generer" class="btn-admin">
                <i class="fas fa-calendar-plus"></i> Générer des créneaux
            </a>
            <a href="index.php?page=admin" class="btn-admin">Retour</a>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>