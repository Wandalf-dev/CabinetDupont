<?php include __DIR__ . '/../templates/header.php'; ?>

<main>
  <section class="login-section">
    <form class="login-form" method="post" action="index.php?page=auth&action=login">
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
      <div class="login-field">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="Mot de passe" />
      </div>
      <button type="submit" class="btn-login">Se connecter</button>
      <div class="login-links">
        <a href="index.php?page=auth&action=forgot">Mot de passe oublié ?</a>
        <span>·</span>
        <a href="index.php?page=auth&action=register">Créer un compte</a>
      </div>
    </form>
  </section>
</main>

<?php include __DIR__ . '/../templates/footer.php'; ?>