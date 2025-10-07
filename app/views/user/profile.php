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
            <h2>Profil de <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h2>
            
            <div class="profile-info">
                <div class="info-group">
                    <label>Nom :</label>
                    <span><?php echo htmlspecialchars($user['nom']); ?></span>
                </div>
                
                <div class="info-group">
                    <label>Prénom :</label>
                    <span><?php echo htmlspecialchars($user['prenom']); ?></span>
                </div>
                
                <div class="info-group">
                    <label>Email :</label>
                    <span><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                
                <div class="info-group">
                    <label>Téléphone :</label>
                    <span><?php 
                        if (!empty($user['telephone'])) {
                            echo htmlspecialchars($user['telephone']);
                            // Debug
                            echo "<!-- Debug: phone in DB = " . htmlspecialchars($user['telephone']) . " -->";
                        } else {
                            echo "Non renseigné";
                        }
                    ?></span>
                </div>

                <div class="info-group">
                    <label>Date de naissance :</label>
                    <span><?php 
                        if (!empty($user['date_naissance']) && $user['date_naissance'] !== '0000-00-00') {
                            $date = DateTime::createFromFormat('Y-m-d', $user['date_naissance']);
                            if ($date) {
                                echo $date->format('d/m/Y');
                            } else {
                                echo "Non renseignée";
                            }
                        } else {
                            echo "Non renseignée";
                        }
                    ?></span>
                </div>
            </div>

            <div class="profile-actions">
                <a href="index.php?page=user&action=edit" class="btn btn-primary">Modifier mon profil</a>
            </div>
        </section>
    </div>
</main>

<?php include __DIR__ . '/../templates/footer.php'; ?>