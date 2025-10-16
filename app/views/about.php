
<?php 
error_log("Début du rendu de la vue about.php");
include __DIR__ . '/templates/header.php'; 
error_log("Header inclus dans about.php");

$equipe = [
    [
        'nom' => 'Sophie Martin',
        'poste' => 'Assistante dentaire',
        'description' => "Diplômée d'État, Sophie accompagne le Dr. Dupont depuis l'ouverture du cabinet.",
        'image' => 'sophie.jpg'
    ],
    [
        'nom' => 'Laura Bernard',
        'poste' => 'Secrétaire médicale',
        'description' => "En charge de l'accueil et de la gestion administrative, Laura veille au bon déroulement de votre prise en charge.",
        'image' => 'laura.jpg'
    ],
    [
        'nom' => 'Thomas Dubois',
        'poste' => 'Prothésiste dentaire',
        'description' => "Spécialiste des prothèses sur-mesure, Thomas travaille en étroite collaboration avec le cabinet.",
        'image' => 'thomas.jpg'
    ]
];
?>

<main class="about-container">
    <!-- Section Docteur -->
    <section class="doctor-section">
        <div class="doctor-header">
            <div class="doctor-info">
                <h1><?= htmlspecialchars($docteur['nom']) ?></h1>
                <h2><?= htmlspecialchars($docteur['titre']) ?></h2>
            </div>
            <div class="doctor-image">
                <img src="<?php echo BASE_URL; ?>/assets/fzeWvCJcOm.png" alt="Dr. Dupont" class="profile-image">
            </div>
        </div>

        <!-- Qualifications -->
        <div class="qualifications-section">
            <h3>Qualifications</h3>
            <ul class="qualifications-list">
                <?php foreach ($docteur['qualifications'] as $qualification): ?>
                    <li><?= htmlspecialchars($qualification) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Parcours -->
        <div class="parcours-section">
            <h3>Parcours professionnel</h3>
            <div class="timeline">
                <?php foreach ($docteur['parcours'] as $etape): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <span class="year"><?= htmlspecialchars($etape['annee']) ?></span>
                            <p><?= htmlspecialchars($etape['description']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Section Équipe -->
    <section class="team-section">
        <h2>Notre Équipe</h2>
        <div class="team-grid">
            <div class="team-member">
                <div class="member-image">
                    <img src="<?php echo BASE_URL; ?>/assets/2148396205.jpg" alt="Sophie Martin" onerror="this.src='<?php echo BASE_URL; ?>/public/uploads/team/default-profile.jpg'">
                </div>
                <div class="member-info">
                    <h3>Sophie Martin</h3>
                    <h4>Assistante dentaire</h4>
                    <p>Diplômée d'État, Sophie accompagne le Dr. Dupont depuis l'ouverture du cabinet.</p>
                </div>
            </div>
            <div class="team-member">
                <div class="member-image">
                    <img src="<?php echo BASE_URL; ?>/assets/20753.jpg" alt="Laura Bernard" onerror="this.src='<?php echo BASE_URL; ?>/public/uploads/team/default-profile.jpg'">
                </div>
                <div class="member-info">
                    <h3>Laura Bernard</h3>
                    <h4>Secrétaire médicale</h4>
                    <p>En charge de l'accueil et de la gestion administrative, Laura veille au bon déroulement de votre prise en charge.</p>
                </div>
            </div>
            <div class="team-member">
                <div class="member-image">
                    <img src="<?php echo BASE_URL; ?>/assets/7678.jpg" alt="Thomas Dubois" onerror="this.src='<?php echo BASE_URL; ?>/public/uploads/team/default-profile.jpg'">
                </div>
                <div class="member-info">
                    <h3>Thomas Dubois</h3>
                    <h4>Prothésiste dentaire</h4>
                    <p>Spécialiste des prothèses sur-mesure, Thomas travaille en étroite collaboration avec le cabinet.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/templates/footer.php'; ?>
<script src="<?php echo BASE_URL; ?>/js/pages/about-animations.js"></script>