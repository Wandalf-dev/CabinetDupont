<?php
include __DIR__ . '/templates/header.php'; ?>

<main>

  <!-- ================= HERO ================= -->
  <section class="hero">
    <div class="container">
      <!-- Bloc 1 : Animation Lottie et texte d'accueil -->
      <div class="hero-row">
        <div class="hero-media">
          <!-- Animation Lottie pour illustrer l'accueil -->
          <div id="lottie-animation"></div>
        </div>

        <div class="hero-text">
          <h1>Bienvenue chez DupontCare</h1>
          <p>Un cabinet dentaire moderne, humain et à votre écoute.</p>
          <!-- Bouton pour prendre rendez-vous -->
          <a href="index.php?page=<?= isset($_SESSION['user_id']) ? 'rendezvous&action=selectConsultation' : 'auth&action=login' ?>" 
             class="btn btn-primary">
             Prendre rendez-vous
          </a>
        </div>
      </div>

      <!-- Bloc 2 : Présentation du docteur -->
      <div class="hero-row reverse">
        <div class="hero-text doctor-card">
          <p class="doctor-presentation">
            Le Dr Dupont, chirurgien-dentiste diplômé, vous accueille dans un
            <span class="doctor-highlight">environnement chaleureux et professionnel</span>.<br><br>
            Son expertise et son écoute garantissent une prise en charge adaptée à chaque patient.
          </p>
        </div>

        <div class="hero-media doctor-photo-halo">
          <!-- Photo du docteur -->
          <img
            src="/cabinetdupont/assets/pharmacien-au-travail.jpg"
            alt="Dr Dupont au cabinet"
            class="doctor-photo"
            loading="lazy"
          />
        </div>
      </div>
    </div>
  </section>

  <!-- ================= SERVICES ================= -->
  <section class="services" id="services">
    <div class="container">
      <h2>Nos services</h2>
      <p class="services-intro">
        Découvrez les soins proposés par le Dr Dupont pour votre santé bucco-dentaire.
      </p>

      <div class="services-carousel">
        <div class="services-track">
          <?php if (empty($services)): ?>
            <!-- Message si aucun service n'est disponible -->
            <p class="services-empty">Aucun service n'est disponible pour le moment.</p>
          <?php else: ?>
            <?php foreach ($services as $service): ?>
              <article class="service-card">
                <?php if (!empty($service['image'])): ?>
                  <!-- Affiche l'image du service si elle existe -->
                  <img
                    src="<?php echo BASE_URL; ?>/public/uploads/<?php echo htmlspecialchars($service['image']); ?>"
                    alt="<?php echo htmlspecialchars($service['titre'] ?? 'Service'); ?>"
                    class="service-icon"
                    loading="lazy"
                  />
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($service['titre'] ?? ''); ?></h3>
                <p><?php echo htmlspecialchars($service['description'] ?? ''); ?></p>
              </article>
            <?php endforeach; ?>
            <!-- Duplication des cartes pour effet infini -->
            <?php foreach ($services as $service): ?>
              <article class="service-card" aria-hidden="true">
                <?php if (!empty($service['image'])): ?>
                  <img
                    src="<?php echo BASE_URL; ?>/public/uploads/<?php echo htmlspecialchars($service['image']); ?>"
                    alt="<?php echo htmlspecialchars($service['titre'] ?? 'Service'); ?>"
                    class="service-icon"
                    loading="lazy"
                  />
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($service['titre'] ?? ''); ?></h3>
                <p><?php echo htmlspecialchars($service['description'] ?? ''); ?></p>
              </article>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- ================= HORAIRES ================= -->
  <section class="hours" id="horaires">
    <div class="container">
      <h2>Horaires du cabinet</h2>
      <p class="hours-intro">
        Nous vous accueillons sur rendez-vous. Urgences selon disponibilité.
      </p>

      <div class="hours-grid">
        <!-- Carte horaires -->
        <div class="hours-card" role="group" aria-labelledby="hours-title">
          <h3 id="hours-title" class="sr-only">Tableau des horaires</h3>

          <table class="hours-table" aria-describedby="hours-note">
            <tbody>
              <?php
              // Tableau des jours en français pour affichage
              $joursFr = [
                'lundi' => ['label' => 'Lundi', 'data-day' => '1'],
                'mardi' => ['label' => 'Mardi', 'data-day' => '2'],
                'mercredi' => ['label' => 'Mercredi', 'data-day' => '3'],
                'jeudi' => ['label' => 'Jeudi', 'data-day' => '4'],
                'vendredi' => ['label' => 'Vendredi', 'data-day' => '5'],
                'samedi' => ['label' => 'Samedi', 'data-day' => '6'],
                'dimanche' => ['label' => 'Dimanche', 'data-day' => '0']
              ];

              foreach ($horaires as $horaire):
                $jourInfo = $joursFr[$horaire['jour']];
              ?>
                <tr data-day="<?php echo $jourInfo['data-day']; ?>">
                  <th><?php echo $jourInfo['label']; ?></th>
                  <?php if ($horaire['ouverture_matin'] === '00:00:00' && $horaire['fermeture_matin'] === '00:00:00' &&
                           $horaire['ouverture_apresmidi'] === '00:00:00' && $horaire['fermeture_apresmidi'] === '00:00:00'): ?>
                    <!-- Affiche "Fermé" si le cabinet est fermé toute la journée -->
                    <td colspan="2">Fermé</td>
                  <?php else: ?>
                    <!-- Affiche les horaires du matin et de l'après-midi -->
                    <td><?php echo ($horaire['ouverture_matin'] === '00:00:00' ? 'Fermé' : substr($horaire['ouverture_matin'], 0, 5) . ' – ' . substr($horaire['fermeture_matin'], 0, 5)); ?></td>
                    <td><?php echo ($horaire['ouverture_apresmidi'] === '00:00:00' ? 'Fermé' : substr($horaire['ouverture_apresmidi'], 0, 5) . ' – ' . substr($horaire['fermeture_apresmidi'], 0, 5)); ?></td>
                  <?php endif; ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <p id="hours-note" class="hours-note">
            Dernier rendez-vous 30 min avant la fermeture.
          </p>
        </div>

        <!-- Carte contact -->
        <aside class="hours-aside">
          <h3>Contact & accès</h3>

          <div class="hours-cta">
            <!-- Bouton pour prendre rendez-vous -->
            <a href="#" class="btn btn-small">Prendre rendez-vous</a>
            <!-- Lien vers Google Maps pour l'itinéraire -->
            <a
              href="https://maps.google.com"
              target="_blank"
              rel="noopener"
              class="btn-outline"
            >Itinéraire</a>
          </div>

          <div class="footer-map-block">
            <div class="footer-contact-card">
              <div class="footer-contact-icon">📍</div>
              <div class="footer-contact-info">
                <strong>Adresse :</strong><br>
                12 rue du Sourire, 34000 Montpellier<br>
                <strong>Tél :</strong> <a href="tel:+33400000000">04 00 00 00 00</a>
              </div>
            </div>
          </div>

          <p class="hours-badge">Urgences selon disponibilité</p>
        </aside>
      </div> <!-- /.hours-grid -->
    </div> <!-- /.container -->
  </section>

  <!-- ================= FOOTER ================= -->
</main>

<?php include __DIR__ . '/templates/footer.php'; ?>

<!-- ================= SCRIPTS ================= -->
<script src="<?php echo BASE_URL; ?>/js/utils/scroll.js"></script>
<script src="<?php echo BASE_URL; ?>/js/pages/home.js"></script>
<script src="<?php echo BASE_URL; ?>/js/modules/service/services-carousel.js"></script>
</body>
</html>