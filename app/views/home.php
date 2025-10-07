<?php include __DIR__ . '/templates/header.php'; ?>

<main>

    <!-- ================= HERO ================= -->
    <section class="hero">
      <div class="container">
        <!-- Bloc 1 -->
        <div class="hero-row">
          <div class="hero-media">
            <div id="lottie-animation"></div>
          </div>

          <div class="hero-text">
            <h1>Bienvenue chez DupontCare</h1>
            <p>Un cabinet dentaire moderne, humain et à votre écoute.</p>
            <a href="#" class="btn">Prendre rendez-vous</a>
          </div>
        </div>

        <!-- Bloc 2 -->
        <div class="hero-row reverse">
          <div class="hero-text doctor-card">
            <p class="doctor-presentation">
              Le Dr Dupont, chirurgien-dentiste diplômé, vous accueille dans un
    			<span class="doctor-highlight">environnement chaleureux et professionnel</span>.</br>
				</br>
              Son expertise et son écoute garantissent une prise en charge adaptée à chaque patient.
            </p>
          </div>

          <div class="hero-media doctor-photo-halo">
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
          Découvrez les soins proposés par le Dr Dupont pour votre santé
          bucco-dentaire.
        </p>

        <div class="services-grid">
          <div class="service-card">
            <img
              src="assets/icon-detartrage.svg"
              alt="Détartrage"
              class="service-icon"
            />
            <h3>Détartrage</h3>
            <p>
              Nettoyage complet des dents pour éliminer le tartre et prévenir
              les maladies des gencives.
            </p>
          </div>

          <div class="service-card">
            <img
              src="assets/icon-blanchiment.svg"
              alt="Blanchiment"
              class="service-icon"
            />
            <h3>Blanchiment</h3>
            <p>
              Redonnez éclat et brillance à votre sourire grâce à nos techniques
              de blanchiment professionnel.
            </p>
          </div>

          <div class="service-card">
            <img
              src="assets/icon-implant.svg"
              alt="Implants dentaires"
              class="service-icon"
            />
            <h3>Implants dentaires</h3>
            <p>
              Remplacez vos dents manquantes durablement avec des implants
              modernes et confortables.
            </p>
          </div>

          <div class="service-card">
            <img
              src="assets/icon-urgence.svg"
              alt="Urgences dentaires"
              class="service-icon"
            />
            <h3>Urgences dentaires</h3>
            <p>
              Besoin d’une consultation rapide ? Le Dr Dupont vous reçoit sur
              rendez-vous en cas d’urgence.
            </p>
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
                <tr data-day="1">
                  <th>Lundi</th>
                  <td>08:30 – 12:30</td>
                  <td>14:00 – 18:30</td>
                </tr>
                <tr data-day="2">
                  <th>Mardi</th>
                  <td>08:30 – 12:30</td>
                  <td>14:00 – 18:30</td>
                </tr>
                <tr data-day="3">
                  <th>Mercredi</th>
                  <td>08:30 – 12:30</td>
                  <td>14:00 – 18:30</td>
                </tr>
                <tr data-day="4">
                  <th>Jeudi</th>
                  <td>08:30 – 12:30</td>
                  <td>14:00 – 18:30</td>
                </tr>
                <tr data-day="5">
                  <th>Vendredi</th>
                  <td>08:30 – 12:30</td>
                  <td>14:00 – 17:00</td>
                </tr>
                <tr data-day="6">
                  <th>Samedi</th>
                  <td>09:00 – 12:00</td>
                  <td>Fermé</td>
                </tr>
                <tr data-day="0">
                  <th>Dimanche</th>
                  <td colspan="2">Fermé</td>
                </tr>
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
              <a href="#" class="btn btn-small">Prendre rendez-vous</a>
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

            <p class="hours-badge">Urgences selon disponibilité</p>
          </aside>
        </div>
      </div>
    </section>

	<!-- ================= FOOTER ================= -->
</main>

<?php include __DIR__ . '/templates/footer.php'; ?>

    <!-- ================= SCRIPTS ================= -->
    <script src="<?php echo BASE_URL; ?>/js/home.js"></script>
  </body>
</html>
