<?php include __DIR__ . '/../templates/header.php'; ?>

<main>
    <section class="register-section">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php 
        // Récupérer les données du formulaire en cas d'erreur
        $form_data = $_SESSION['form_data'] ?? [];
        unset($_SESSION['form_data']);
        ?>

        <form class="register-form" method="post" action="index.php?page=auth&action=register">
            <h2>Création de compte</h2>
            
            <div class="form-group">
                <label for="nom">NOM*</label>
                <input type="text" id="nom" name="nom" required 
                    value="<?php echo htmlspecialchars($form_data['nom'] ?? ''); ?>"
                    placeholder="Votre NOM"
                    oninput="this.value = this.value.toUpperCase();">
            </div>

            <div class="form-group">
                <label for="prenom">Prénom*</label>
                <input type="text" id="prenom" name="prenom" required 
                    value="<?php echo htmlspecialchars($form_data['prenom'] ?? ''); ?>"
                    placeholder="Votre prénom">
            </div>

            <div class="form-group">
                <label for="email">Adresse e-mail*</label>
                <input type="email" id="email" name="email" required 
                    value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>"
                    placeholder="votre@email.com">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe*</label>
                <input type="password" id="password" name="password" required 
                    placeholder="Votre mot de passe">
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirmer le mot de passe*</label>
                <input type="password" id="password_confirm" name="password_confirm" required 
                    placeholder="Confirmez votre mot de passe">
            </div>

            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" 
                    value="<?php echo htmlspecialchars($form_data['telephone'] ?? ''); ?>"
                    placeholder="Votre numéro de téléphone">
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

<script>
// Forcer la saisie du nom en majuscules
document.addEventListener('DOMContentLoaded', function() {
    var nomInput = document.getElementById('nom');
    if (nomInput) {
        nomInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
});
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>