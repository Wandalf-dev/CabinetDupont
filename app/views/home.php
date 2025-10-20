<?php
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/flash-messages.php'; ?>

<!-- CSS spécifique à la page d'accueil -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/components/cabinet-gallery.css">

<main class="home-page">

  <!-- ================= HERO ================= -->
  <section class="hero">
    <div class="hero-container">
      
      <!-- Section principale avec animation et CTA -->
      <div class="hero-main">
        <div class="hero-content" style="margin-top: 6rem;">
          <h1 class="hero-title">
            Bienvenue chez <span class="hero-highlight">DupontCare</span>
          </h1>
          <p class="hero-subtitle">
            Un <strong>cabinet dentaire</strong> humain et à votre écoute, pour prendre soin de votre sourire avec expertise et bienveillance.
          </p>
          <a href="index.php?page=<?= isset($_SESSION['user_id']) ? 'rendezvous&action=selectConsultation' : 'auth&action=login' ?>" 
             class="btn btn-primary btn-hero">
             <i class="fas fa-calendar-check"></i>
             Prendre rendez-vous
          </a>
        </div>
        
        <div class="hero-visual">
          <div id="lottie-animation"></div>
        </div>
      </div>

      <!-- Section présentation docteur -->
      <div class="doctor-section">
        <div class="doctor-image">
          <img
            src="<?php echo BASE_URL; ?>/assets/pharmacien-au-travail.jpg"
            alt="Dr Dupont au cabinet"
            loading="lazy"
          />
        </div>
        
        <div class="doctor-info">
          <h2 class="doctor-name">Dr Dupont</h2>
          <p class="doctor-title">Chirurgien-dentiste diplômé</p>
          <div class="doctor-description">
            <p>
              <i class="fas fa-check-circle"></i>
              <span>Vous accueille dans un <strong>environnement chaleureux et professionnel</strong></span>
            </p>
            <p>
              <i class="fas fa-check-circle"></i>
              <span>Son <strong>expertise et son écoute</strong> garantissent une prise en charge adaptée à chaque patient</span>
            </p>
          </div>
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
          <div class="contact-header">
            <div class="contact-icon">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <h3>Nous contacter</h3>
          </div>

          <div class="contact-info-grid">
            <div class="contact-item">
              <div class="contact-item-icon">
                <i class="fas fa-map-pin"></i>
              </div>
              <div class="contact-item-content">
                <span class="contact-label">Adresse</span>
                <p>12 rue du Sourire<br>34000 Montpellier</p>
              </div>
            </div>

            <div class="contact-item">
              <div class="contact-item-icon">
                <i class="fas fa-phone"></i>
              </div>
              <div class="contact-item-content">
                <span class="contact-label">Téléphone</span>
                <p><a href="tel:+33400000000">04 00 00 00 00</a></p>
              </div>
            </div>
          </div>

          <div class="contact-actions">
            <a href="index.php?page=<?= isset($_SESSION['user_id']) ? 'rendezvous&action=selectConsultation' : 'auth&action=login' ?>" class="contact-btn contact-btn-primary">
              <i class="fas fa-calendar-check"></i>
              <span>Prendre rendez-vous</span>
            </a>
            <a href="https://maps.google.com" target="_blank" rel="noopener" class="contact-btn contact-btn-outline">
              <i class="fas fa-route"></i>
              <span>Itinéraire</span>
            </a>
          </div>

          <div class="contact-badge">
            <i class="fas fa-exclamation-circle"></i>
            <span>Urgences selon disponibilité</span>
          </div>
        </aside>
      </div> <!-- /.hours-grid -->
    </div> <!-- /.container -->
  </section>

  <!-- ================= GALERIE CABINET ================= -->
  <section class="cabinet-gallery">
    <div class="container">
      <h2>Notre cabinet</h2>
      <p class="cabinet-gallery-intro">
        Découvrez nos installations modernes et notre équipe dévouée à votre service.
      </p>

      <div class="gallery-grid">
        <div class="gallery-item">
          <img
            src="<?php echo BASE_URL; ?>/assets/bureau-de-stomatologie-avec-equipement-moderne-et-infirmiere-en-uniforme-bleu-travaillant-sur-ordinateur.jpg"
            alt="Cabinet dentaire moderne avec équipement professionnel"
            loading="lazy"
          />
          <div class="gallery-caption">
            <h3>Équipement moderne</h3>
            <p>Un cabinet équipé des dernières technologies pour votre confort</p>
          </div>
        </div>

        <div class="gallery-item">
          <img
            src="<?php echo BASE_URL; ?>/assets/zone-d-attente-de-stomatologie-bondee-avec-des-personnes-remplissant-un-formulaire-pour-une-consultation-dentaire.jpg"
            alt="Salle d'attente du cabinet dentaire"
            loading="lazy"
          />
          <div class="gallery-caption">
            <h3>Accueil chaleureux</h3>
            <p>Une équipe à votre écoute dans un environnement convivial</p>
          </div>
        </div>
      </div>
    </div>
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