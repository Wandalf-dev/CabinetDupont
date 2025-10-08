<?php include __DIR__ . '/../templates/header.php'; ?>
<link rel="stylesheet" href="css/edit-profile.css">
<!-- Font Awesome pour les icônes -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Cleave.js pour le formatage du téléphone -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
<script src="js/phone-formatter.js?v=<?php echo time(); ?>"></script>
<script>
// Vérification que les scripts sont bien chargés
console.log('Scripts chargés');
</script>

<style>
.password-group {
    position: relative;
}

.password-input-container {
    position: relative;
    display: flex;
    align-items: center;
}

.password-input-container input {
    width: 100%;
    padding-right: 40px;
}

.password-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #666;
    padding: 5px;
}

.password-toggle:hover {
    color: #333;
}

.password-toggle i {
    font-size: 1.1em;
}
</style>

<main class="profile-edit">
    <section class="form-section">
        <div class="section-header">
            <h2>Modifier mon profil</h2>
            <a href="index.php?page=user&action=profile" class="btn btn-secondary">Retour au profil</a>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <form class="profile-form" method="post" action="index.php?page=user&action=edit">
            <div class="info-block">
                <h3>Informations personnelles</h3>
                
                <div class="form-group">
                    <label for="nom">NOM <span class="required-star">*</span></label>
              <input type="text" id="nom" name="nom" required 
                  value="<?php echo htmlspecialchars($formData['nom'] ?? ''); ?>"
                  oninput="this.value = this.value.toUpperCase();">
                </div>

                <div class="form-group">
                    <label for="prenom">Prénom <span class="required-star">*</span></label>
              <input type="text" id="prenom" name="prenom" required 
                  value="<?php echo htmlspecialchars($formData['prenom'] ?? ''); ?>"
                  oninput="if(this.value.length > 0){this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);} ">
                </div>

                <div class="form-group">
                    <label for="email">Email <span class="required-star">*</span></label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <span style="background: #f3f3f3; border: 1px solid #ccc; border-radius: 4px 0 0 4px; padding: 7px 10px; color: #888; font-size: 1em;">(+33)</span>
                        <input type="tel" 
                               id="telephone" 
                               name="telephone" 
                               maxlength="14"
                               style="border-radius: 0 4px 4px 0;"
                               pattern="[1-9]-[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}"
                               value="<?php 
                               $phone = $formData['telephone'] ?? '';
                               $digits = preg_replace('/[^0-9]/', '', $phone);
                               // Si le numéro commence par 33, l'enlever
                               if (strpos($digits, '33') === 0) {
                                   $digits = substr($digits, 2);
                               }
                               // Si le numéro commence par 0, l'enlever
                               if (strpos($digits, '0') === 0) {
                                   $digits = substr($digits, 1);
                               }
                               // Formatter en X-XX-XX-XX-XX si assez de chiffres
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
                               ?>"
                               placeholder="X-XX-XX-XX-XX">
                    </div>
                </div>

                <div class="form-group">
                    <label for="date_naissance">Date de naissance <span class="required-star">*</span></label>
                    <input type="text" id="date_naissance" name="date_naissance" class="flatpickr" 
                           value="<?php 
                           $date_value = $formData['date_naissance'] ?? $user['date_naissance'] ?? '';
                           if (!empty($date_value) && $date_value !== '0000-00-00') {
                               $date = DateTime::createFromFormat('Y-m-d', $date_value);
                               if ($date) {
                                   echo $date->format('d/m/Y');
                               }
                           }
                           ?>"
                           placeholder="Sélectionnez votre date de naissance" required>
                </div>
            </div>

            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
            <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
            <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
            <script>
                flatpickr(".flatpickr", {
                    locale: "fr",
                    dateFormat: "d/m/Y",
                    allowInput: true
                });
            </script>

            <div class="info-block">
                <h3>Modification du mot de passe</h3>
                <p class="form-info">Laissez les champs vides si vous ne souhaitez pas modifier votre mot de passe.</p>

                <div class="form-group password-group">
                    <label for="current_password">Mot de passe actuel</label>
                    <div class="password-input-container">
                        <input type="password" id="current_password" name="current_password">
                        <span class="password-toggle" onclick="togglePassword('current_password', this)">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>

                <div class="form-group password-group">
                    <label for="new_password">Nouveau mot de passe</label>
                    <div class="password-input-container">
                        <input type="password" id="new_password" name="new_password">
                        <span class="password-toggle" onclick="togglePassword('new_password', this)">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>

                <div class="form-group password-group">
                    <label for="confirm_password">Confirmer le nouveau mot de passe</label>
                    <div class="password-input-container">
                        <input type="password" id="confirm_password" name="confirm_password">
                        <span class="password-toggle" onclick="togglePassword('confirm_password', this)">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="index.php?page=user&action=profile" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </section>
</main>

<!-- Inclusion de Cleave.js et des scripts personnalisés -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
<script src="js/password-toggle.js"></script>
<script src="js/phone-formatter.js"></script>
</script>


<script>
// Forcer la saisie du nom en majuscules (sécurité supplémentaire)
// et la première lettre du prénom en majuscule
document.addEventListener('DOMContentLoaded', function() {
    var nomInput = document.getElementById('nom');
    if (nomInput) {
        nomInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
    var prenomInput = document.getElementById('prenom');
    if (prenomInput) {
        prenomInput.addEventListener('input', function() {
            if(this.value.length > 0){
                this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
            }
        });
    }
});
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>