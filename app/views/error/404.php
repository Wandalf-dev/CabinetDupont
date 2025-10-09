<?php
// Cette page s'affiche quand l'utilisateur demande une URL qui n'existe pas dans l'application.
// Elle permet d'éviter une erreur technique et propose un retour vers l'accueil.
// C'est une bonne pratique pour l'expérience utilisateur et la sécurité.
include __DIR__ . '/../templates/header.php'; ?>

<main>
    <section class="error-section">
        <div class="container">
            <!-- Titre de la page d'erreur -->
            <h1>Erreur 404</h1>
            <!-- Message d'information pour l'utilisateur -->
            <p>La page que vous recherchez n'existe pas.</p>
            <!-- Lien pour retourner à la page d'accueil -->
            <a href="index.php?page=home" class="btn btn-primary">Retour à l'accueil</a>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../templates/footer.php'; ?>