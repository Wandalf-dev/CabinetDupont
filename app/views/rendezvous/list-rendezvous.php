<?php require_once 'app/views/templates/header.php'; ?>
<link rel="stylesheet" href="css/list-rendezvous.css">

<main>
    <div class="rdv-container">
        <div class="rdv-header">
            <h1 class="rdv-title">Mes rendez-vous</h1>
            <a href="index.php?page=rendezvous&action=selectConsultation" class="btn btn-primary btn-new-rdv">
                <i class="fas fa-plus me-2"></i>
                Nouveau rendez-vous
            </a>
        </div>
        
        <?php if (empty($rendezvous)): ?>
            <div class="rdv-empty">
                <i class="fas fa-calendar-times"></i>
                <p>Vous n'avez aucun rendez-vous programmé.</p>
                <p class="text-muted">Cliquez sur le bouton "Nouveau rendez-vous" pour en planifier un.</p>
            </div>
        <?php else: ?>
            <div class="rdv-table">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Heure</th>
                                <th>Type de consultation</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rendezvous as $rdv): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($rdv['debut'])); ?></td>
                                    <td><?php echo date('H:i', strtotime($rdv['debut'])); ?></td>
                                    <td><?php echo htmlspecialchars($rdv['service_titre']); ?></td>
                                    <td>
                                        <a href="index.php?page=rendezvous&action=cancel&id=<?php echo $rdv['rdv_id']; ?>" 
                                           class="btn btn-outline-danger btn-sm btn-cancel"
                                           onclick="return confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?');">
                                            <i class="fas fa-times me-1"></i>
                                            Annuler
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'app/views/templates/footer.php'; ?>