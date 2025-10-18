<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Heure de début</th>
                <th>Heure de fin</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($creneauxSection as $creneau): 
                // Calculer la vraie fin du créneau en prenant en compte la durée du RDV
                $debut = strtotime($creneau['debut']);
                if (!empty($creneau['rdv_duree']) && $creneau['rdv_duree'] > 0) {
                    // Si c'est un RDV, calculer la fin avec la durée réelle du RDV
                    $fin = $debut + ($creneau['rdv_duree'] * 60);
                } else {
                    // Sinon, utiliser la fin du créneau par défaut
                    $fin = strtotime($creneau['fin']);
                }
            ?>
                <tr>
                    <td><?php echo date('H:i', $debut); ?></td>
                    <td><?php echo date('H:i', $fin); ?></td>
                    <td class="statut-creneau">
                        <?php 
                        // Debug info
                        error_log("Créneau ID: " . $creneau['id']);
                        error_log("est_reserve: " . ($creneau['est_reserve'] ? 'true' : 'false'));
                        error_log("statut: " . ($creneau['statut'] ?? 'non défini'));
                        ?>
                        <!-- Debug visible -->
                        <div style="font-size: 10px; color: #666;">
                            Debug: est_reserve=<?php echo $creneau['est_reserve'] ? 'true' : 'false'; ?>, 
                            statut=<?php echo $creneau['statut'] ?? 'non défini'; ?>
                        </div>

                        <?php if (!$creneau['est_reserve']): ?>
                            <?php if ($creneau['statut'] === 'indisponible'): ?>
                                <span class="badge indisponible">Indisponible</span>
                            <?php else: ?>
                                <span class="badge disponible">Disponible</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="badge reserve">Réservé</span>
                        <?php endif; ?>
                    </td>
                    <td class="actions">
                        <?php if (!$creneau['est_reserve']): ?>
                            <div class="btn-group">
                                <button type="button" 
                                        class="btn <?php echo $creneau['statut'] === 'indisponible' ? 'btn-success' : 'btn-warning'; ?> btn-toggle-dispo" 
                                        data-creneau-id="<?php echo $creneau['id']; ?>">
                                    <?php echo $creneau['statut'] === 'indisponible' ? 'Rendre disponible' : 'Marquer indisponible'; ?>
                                </button>
                                <form method="post" action="index.php?page=creneaux&action=supprimer" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce créneau ?');"
                                      style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $creneau['id']; ?>">
                                    <button type="submit" class="btn-action delete" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>