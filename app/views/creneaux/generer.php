<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/flash-messages.php';
?>

<main class="container">
    <section class="admin-section">
        <h2>Génération des créneaux de consultation</h2>
        
        <form method="post" class="form-standard">
            <div class="form-group">
                <label for="date_debut">Date de début</label>
                <input type="date" id="date_debut" name="date_debut" required
                       min="<?php echo date('Y-m-d'); ?>" 
                       value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
                <label for="date_fin">Date de fin</label>
                <input type="date" id="date_fin" name="date_fin" required
                       min="<?php echo date('Y-m-d'); ?>"
                       value="<?php echo date('Y-m-d', strtotime('+1 month')); ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-admin save">
                    <i class="fas fa-calendar-plus"></i> Générer les créneaux
                </button>
                <a href="index.php?page=admin" class="btn-admin">Annuler</a>
            </div>
        </form>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');

    // S'assurer que la date de fin n'est pas antérieure à la date de début
    dateDebut.addEventListener('change', function() {
        if (dateFin.value < dateDebut.value) {
            dateFin.value = dateDebut.value;
        }
        dateFin.min = dateDebut.value;
    });
});
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>