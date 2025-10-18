<?php
include __DIR__ . '/../templates/header.php'; ?>

<!-- CSS spécifique à la page login -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/login.css">

<!-- Inclusion de Font Awesome pour les icônes -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<main>
  <!-- Affiche un message de succès si présent en session -->
  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert-popup success">
        <i class="fas fa-check-circle"></i>
        <span class="message"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></span>
    </div>
  <?php endif; ?>
  
  <section class="login-section">
    <form class="login-form" method="post" action="index.php?page=auth&action=login">
      <!-- Affiche un message d'erreur si présent en session -->
      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger" style="margin-bottom: 18px; text-align: center;">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>
      <h2>Connexion à votre espace</h2>
      <div class="login-field">
        <label for="email">Adresse e-mail</label>
        <input type="email" id="email" name="email" required autocomplete="username" placeholder="Email" />
      </div>
      <!-- Champ caché pour le token CSRF (sécurité) -->
      <input type="hidden" name="csrf_token" value="<?php echo isset($csrf_token) ? $csrf_token : ''; ?>">
      <div class="login-field">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="Mot de passe" />
      </div>
      <button type="submit" class="btn-login">Se connecter</button>
      <div class="login-links">
        <a href="index.php?page=auth&action=forgot">Mot de passe oublié ?</a>
        <a href="index.php?page=auth&action=register">Créer un compte</a>
      </div>
    </form>
  </section>
</main>

<!-- Script pour gérer l'affichage des alertes -->
<script src="js/alerts.js"></script>

<?php include __DIR__ . '/../templates/footer.php'; ?>