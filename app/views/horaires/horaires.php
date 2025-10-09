<?php include __DIR__ . '/../templates/header.php'; ?>

<main>
    <section class="horaires-section">
        <div class="container">
            <h2>Horaires d'ouverture</h2>
            
            <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'MEDECIN' || $_SESSION['user_role'] === 'SECRETAIRE')): ?>
                <div class="admin-actions">
                    <a href="index.php?page=horaires&action=edit" class="btn-admin">Modifier les horaires</a>
                </div>
            <?php endif; ?>

            <div class="horaires-grid">
                <?php 
                $joursFr = [
                    'lundi' => 'Lundi',
                    'mardi' => 'Mardi',
                    'mercredi' => 'Mercredi',
                    'jeudi' => 'Jeudi',
                    'vendredi' => 'Vendredi',
                    'samedi' => 'Samedi',
                    'dimanche' => 'Dimanche'
                ];

                foreach ($joursFr as $jourEn => $jourFr):
                    $horaireDuJour = null;
                    foreach ($horaires as $horaire) {
                        if ($horaire['jour'] === $jourEn) {
                            $horaireDuJour = $horaire;
                            break;
                        }
                    }
                ?>
                    <div class="horaire-card">
                        <h3><?php echo $jourFr; ?></h3>
                        <?php if ($horaireDuJour): ?>
                            <p>
                                <?php 
                                    echo date('H:i', strtotime($horaireDuJour['ouverture'])) . 
                                         ' - ' . 
                                         date('H:i', strtotime($horaireDuJour['fermeture']));
                                ?>
                            </p>
                        <?php else: ?>
                            <p>Fermé</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../templates/footer.php'; ?>