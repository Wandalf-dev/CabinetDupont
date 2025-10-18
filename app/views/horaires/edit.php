<?php
include __DIR__ . '/../templates/header.php'; ?>

<!-- CSS spécifiques aux horaires -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/modules/horaires/horaires.css">

<main>
    <section class="horaires-edit-section">
        <div class="container">
            <h2>Modifier les horaires</h2>

            <!-- Inclusion des messages flash (succès/erreur) -->
            <?php include __DIR__ . '/../templates/flash-messages.php'; ?>

            <!-- Formulaire de modification des horaires -->
            <form class="horaires-form" method="post" action="index.php?page=horaires&action=edit">
                <!-- Champ caché pour le token CSRF (sécurité) -->
                <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">
                
                <?php 
                // Tableau des jours en français
                $joursFr = [
                    'lundi' => 'Lundi',
                    'mardi' => 'Mardi',
                    'mercredi' => 'Mercredi',
                    'jeudi' => 'Jeudi',
                    'vendredi' => 'Vendredi',
                    'samedi' => 'Samedi',
                    'dimanche' => 'Dimanche'
                ];

                // Boucle sur chaque jour pour afficher les champs horaires
                foreach ($joursFr as $jourEn => $jourFr):
                    $horaireDuJour = null;
                    foreach ($horaires as $horaire) {
                        if ($horaire['jour'] === $jourEn) {
                            $horaireDuJour = $horaire;
                            break;
                        }
                    }
                ?>
                    <div class="horaire-edit-card">
                        <h3><?php echo $jourFr; ?></h3>
                        <div class="horaire-inputs">
                            <div class="time-input">
                                <label for="ouverture_<?php echo $jourEn; ?>">Ouverture</label>
                                <input type="time" 
                                       id="ouverture_<?php echo $jourEn; ?>" 
                                       name="horaires[<?php echo $jourEn; ?>][ouverture]" 
                                       value="<?php echo $horaireDuJour ? $horaireDuJour['ouverture'] : ''; ?>">
                            </div>
                            <div class="time-input">
                                <label for="fermeture_<?php echo $jourEn; ?>">Fermeture</label>
                                <input type="time" 
                                       id="fermeture_<?php echo $jourEn; ?>" 
                                       name="horaires[<?php echo $jourEn; ?>][fermeture]" 
                                       value="<?php echo $horaireDuJour ? $horaireDuJour['fermeture'] : ''; ?>">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="form-actions">
                    <button type="submit" class="btn-save">Enregistrer les modifications</button>
                    <a href="index.php?page=horaires" class="btn-cancel">Annuler</a>
                </div>
            </form>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../templates/footer.php'; ?>