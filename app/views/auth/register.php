<?php
include __DIR__ . '/../templates/header.php'; ?>

<!-- CSS spécifique à la page register -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/register.css">

<!-- Font Awesome pour les icônes -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Flatpickr pour le calendrier -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<main>
    <section class="register-section">
        <?php 
        // Récupérer les données du formulaire en cas d'erreur
        $form_data = $_SESSION['form_data'] ?? [];
        unset($_SESSION['form_data']);
        ?>

        <form class="register-form" method="post" action="index.php?page=auth&action=register">
            <!-- Affiche un message d'erreur si présent en session -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="register-alert register-alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div class="alert-content">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                </div>
            <?php endif; ?>
            <!-- Champ caché pour le token CSRF (sécurité) -->
            <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">
            <h2>Création de compte</h2>
            
            <div class="form-group">
                <label for="nom">NOM <span class="required-star">*</span></label>
                <input type="text" id="nom" name="nom" required 
                    value="<?php echo htmlspecialchars($form_data['nom'] ?? ''); ?>"
                    placeholder="Votre NOM"
                    oninput="this.value = this.value.toUpperCase();">
            </div>

            <div class="form-group">
                <label for="prenom">Prénom <span class="required-star">*</span></label>
                <input type="text" id="prenom" name="prenom" required 
                    value="<?php echo htmlspecialchars($form_data['prenom'] ?? ''); ?>"
                    placeholder="Votre prénom"
                    oninput="this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);">
            </div>

            <div class="form-group">
                <label for="email">Adresse e-mail <span class="required-star">*</span></label>
                <input type="email" id="email" name="email" required 
                    value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>"
                    placeholder="votre@email.com">
            </div>

            <!-- Groupe mot de passe avec icône d'affichage -->
            <div class="form-group password-group">
                <label for="password">Mot de passe <span class="required-star">*</span></label>
                <div class="password-input-container">
                    <input type="password" id="password" name="password" required 
                        placeholder="Votre mot de passe">
                    <span class="password-toggle" onclick="togglePassword('password', this)">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <div class="form-group password-group">
                <label for="password_confirm">Confirmer le mot de passe <span class="required-star">*</span></label>
                <div class="password-input-container">
                    <input type="password" id="password_confirm" name="password_confirm" required 
                        placeholder="Confirmez votre mot de passe">
                    <span class="password-toggle" onclick="togglePassword('password_confirm', this)">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <!-- Champ téléphone avec formatage français -->
            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="background: #f3f3f3; border: 1px solid #ccc; border-radius: 4px 0 0 4px; padding: 7px 10px; color: #888; font-size: 1em;">(+33)</span>
                    <input type="tel" id="telephone" name="telephone"
                        maxlength="14"
                        style="border-radius: 0 4px 4px 0;"
                        pattern="[1-9]-[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}"
                        value="<?php 
                            $phone = $form_data['telephone'] ?? '';
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
                        ?>"
                        placeholder="X-XX-XX-XX-XX">
                </div>
            </div>

            <!-- Champ date de naissance avec Flatpickr -->
            <div class="form-group">
                <label for="date_naissance">Date de naissance</label>
                <div class="date-input-container">
                    <input type="text" 
                           id="date_naissance" 
                           name="date_naissance" 
                           class="flatpickr" 
                           placeholder="JJ/MM/AAAA"
                           autocomplete="off"
                           value="<?php 
                               $date_value = $form_data['date_naissance'] ?? '';
                               if (!empty($date_value) && $date_value !== '0000-00-00') {
                                   $date = DateTime::createFromFormat('Y-m-d', $date_value);
                                   if ($date) {
                                       echo $date->format('d/m/Y');
                                   }
                               }
                           ?>">
                    <i class="fas fa-calendar-alt date-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn-register">Créer mon compte</button>
            </div>

            <p class="login-link">
                Déjà inscrit ? <a href="index.php?page=auth&action=login">Connectez-vous ici</a>
            </p>
        </form>
    </section>
</main>

<!-- Script pour forcer la saisie du nom en majuscules -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var nomInput = document.getElementById('nom');
    if (nomInput) {
        nomInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
});
</script>

<!-- Scripts externes -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>

<!-- Scripts personnalisés -->
<script src="<?php echo BASE_URL; ?>/js/components/password-toggle.js"></script>
<script src="<?php echo BASE_URL; ?>/js/utils/phone-formatter.js"></script>
<script src="<?php echo BASE_URL; ?>/js/utils/date-formatter.js"></script>

<!-- Initialisation Flatpickr -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initialisation de Flatpickr...');
    
    // Vérifier si Flatpickr est chargé
    if (typeof flatpickr === 'undefined') {
        console.error('Flatpickr n\'est pas chargé !');
        return;
    }
    
    // Vérifier si l'élément existe
    const dateInput = document.querySelector('.flatpickr');
    if (!dateInput) {
        console.error('Element .flatpickr non trouvé !');
        return;
    }
    
    console.log('Element trouvé:', dateInput);
    
    // Initialiser Flatpickr
    try {
        const fp = flatpickr(".flatpickr", {
            locale: "fr",
            dateFormat: "d/m/Y",
            allowInput: true,
            maxDate: "today",
            disableMobile: false,
            static: false,
            clickOpens: true,
            onChange: function(selectedDates, dateStr, instance) {
                console.log('Date sélectionnée:', dateStr);
            }
        });
        console.log('Flatpickr initialisé avec succès:', fp);
    } catch(error) {
        console.error('Erreur lors de l\'initialisation de Flatpickr:', error);
    }
});
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>