<?php include __DIR__ . '/../templates/header.php'; ?>
<link rel="stylesheet" href="css/edit-profile.css">

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

        <form class="profile-form" method="post" action="index.php?page=profile/edit">
            <div class="info-block">
                <h3>Informations personnelles</h3>
                
                <div class="form-group">
                    <label for="nom">Nom *</label>
                    <input type="text" id="nom" name="nom" required 
                           value="<?php echo htmlspecialchars($formData['nom'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="prenom">Prénom *</label>
                    <input type="text" id="prenom" name="prenom" required 
                           value="<?php echo htmlspecialchars($formData['prenom'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" 
                           value="<?php echo htmlspecialchars($formData['telephone'] ?? ''); ?>">
                </div>
            </div>

            <div class="info-block">
                <h3>Modification du mot de passe</h3>
                <p class="form-info">Laissez les champs vides si vous ne souhaitez pas modifier votre mot de passe.</p>

                <div class="form-group">
                    <label for="current_password">Mot de passe actuel</label>
                    <input type="password" id="current_password" name="current_password">
                </div>

                <div class="form-group">
                    <label for="new_password">Nouveau mot de passe</label>
                    <input type="password" id="new_password" name="new_password">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="index.php?page=user&action=profile" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </section>
</main>

<?php include __DIR__ . '/../templates/footer.php'; ?>