    <?php
include __DIR__ . '/../templates/header.php';
include __DIR__ . '/../templates/flash-messages.php';
?>

<!-- CSS spécifique à la gestion des patients -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/patient.css">

<main class="container">
    <div class="admin-form-container">
        <h1>Ajouter un patient</h1>
        <form method="post" class="admin-form">
            <!-- Token CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" class="form-control" required value="<?= isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : '' ?>">
            </div>

            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" class="form-control" required value="<?= isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : '' ?>">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>

            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="background: #f3f3f3; border: 1px solid #ccc; border-radius: 4px 0 0 4px; padding: 7px 10px; color: #888; font-size: 1em;">(+33)</span>
                    <input type="tel" 
                           id="telephone" 
                           name="telephone" 
                           class="form-control" 
                           maxlength="13"
                           inputmode="numeric"
                           autocomplete="tel"
                           style="border-radius: 0 4px 4px 0;"
                           pattern="^[1-9]-[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}$"
                           placeholder="X-XX-XX-XX-XX"
                           value="<?php
                               $phone = isset($_POST['telephone']) ? $_POST['telephone'] : '';
                               $digits = preg_replace('/[^0-9]/', '', $phone);
                               if (strpos($digits, '33') === 0) {
                                   $digits = substr($digits, 2);
                               }
                               if (strpos($digits, '0') === 0) {
                                   $digits = substr($digits, 1);
                               }
                               if (strlen($digits) === 9) {
                                   $phone = sprintf('%s-%s-%s-%s-%s',
                                       substr($digits, 0, 1),
                                       substr($digits, 1, 2),
                                       substr($digits, 3, 2),
                                       substr($digits, 5, 2),
                                       substr($digits, 7, 2)
                                   );
                               } else {
                                   $phone = '';
                               }
                               echo htmlspecialchars($phone);
                           ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="date_naissance">Date de naissance</label>
                <input type="text" 
                       id="date_naissance" 
                       name="date_naissance" 
                       class="form-control flatpickr" 
                       required 
                       placeholder="Sélectionnez une date"
                       value="<?php 
                           $date_value = isset($_POST['date_naissance']) ? $_POST['date_naissance'] : '';
                           if (!empty($date_value) && $date_value !== '0000-00-00') {
                               $date = DateTime::createFromFormat('Y-m-d', $date_value);
                               if ($date) {
                                   echo $date->format('d/m/Y');
                               }
                           }
                       ?>">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <div class="password-input-group">
                    <input type="password" id="password" name="password" class="form-control" required minlength="6">
                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <small class="form-text text-muted">Le mot de passe doit contenir au moins 6 caractères.</small>
            </div>

            <div class="actu-btn-row">
                <button type="submit" class="btn-actu-create">Enregistrer</button>
                <a href="index.php?page=admin" class="btn-actu-cancel">Annuler</a>
            </div>
        </form>
    </div>
</main>

<!-- Style pour le formulaire -->
<link rel="stylesheet" href="/CabinetDupont/css/pages/admin.css">
<link rel="stylesheet" href="/CabinetDupont/css/modules/actu/actu-create.css">

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Inclusion de Cleave.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>

<!-- Script pour le toggle du mot de passe -->
<script src="<?php echo BASE_URL; ?>/js/components/password-toggle.js"></script>
<!-- Script pour formater le numéro de téléphone -->
<script src="<?php echo BASE_URL; ?>/js/utils/phone-formatter.js"></script>
<!-- Script pour formater la date -->
<script src="<?php echo BASE_URL; ?>/js/utils/date-formatter.js"></script>

<!-- JS: Flatpickr -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
<script>
flatpickr(".flatpickr", {
    locale: "fr",
    dateFormat: "d/m/Y",
    allowInput: true
});
</script>


<?php include __DIR__ . '/../templates/footer.php'; ?>