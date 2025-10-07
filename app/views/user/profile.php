<?php include __DIR__ . '/../templates/header.php'; ?>

<main class="profile-page">
    <div class="profile-container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <section class="profile-section">
            <h2>Profil de <?php echo htmlspecialchars($user->getPrenom() . ' ' . $user->getNom()); ?></h2>
            
            <div class="profile-info">
                <div class="info-group">
                    <label>Nom :</label>
                    <span><?php echo htmlspecialchars($user->getNom()); ?></span>
                </div>
                
                <div class="info-group">
                    <label>Prénom :</label>
                    <span><?php echo htmlspecialchars($user->getPrenom()); ?></span>
                </div>
                
                <div class="info-group">
                    <label>Email :</label>
                    <span><?php echo htmlspecialchars($user->getEmail()); ?></span>
                </div>
                
                <div class="info-group">
                    <label>Téléphone :</label>
                    <span><?php echo htmlspecialchars($user->getTelephone()); ?></span>
                </div>
            </div>

            <div class="profile-actions">
                <a href="index.php?page=user&action=edit" class="btn btn-primary">Modifier mon profil</a>
            </div>
        </section>
    </div>
</main>

<?php include __DIR__ . '/../templates/footer.php'; ?>