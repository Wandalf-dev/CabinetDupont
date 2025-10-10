<?php

namespace App\Controllers;

class AboutController extends \App\Core\Controller {
    public function index() {
        error_log("Démarrage de AboutController::index()");
        // Données du Dr. Dupont
        $docteur = [
            'nom' => 'Dr. Christian Dupont',
            'titre' => 'Chirurgien-Dentiste',
            'qualifications' => [
                'Docteur en chirurgie dentaire - Université de Paris',
                'Spécialisation en implantologie',
                'Certificat en orthodontie invisible'
            ],
            'parcours' => [
                [
                    'annee' => '2015',
                    'description' => 'Diplôme de Docteur en Chirurgie Dentaire'
                ],
                [
                    'annee' => '2016',
                    'description' => 'Formation spécialisée en implantologie'
                ],
                [
                    'annee' => '2018',
                    'description' => 'Certification en orthodontie invisible'
                ],
                [
                    'annee' => '2019',
                    'description' => 'Ouverture du cabinet dentaire à Annecy'
                ]
            ]
        ];

        // Données de l'équipe
        $equipe = [
            [
                'nom' => 'Sophie Martin',
                'poste' => 'Assistante dentaire',
                'description' => 'Diplômée d\'État, Sophie accompagne le Dr. Dupont depuis l\'ouverture du cabinet.'
            ],
            [
                'nom' => 'Laura Bernard',
                'poste' => 'Secrétaire médicale',
                'description' => 'En charge de l\'accueil et de la gestion administrative, Laura veille au bon déroulement de votre prise en charge.'
            ],
            [
                'nom' => 'Thomas Dubois',
                'poste' => 'Prothésiste dentaire',
                'description' => 'Spécialiste des prothèses sur-mesure, Thomas travaille en étroite collaboration avec le cabinet.'
            ]
        ];

        error_log("Données préparées, appel de la vue about");
        error_log("BASE_URL = " . BASE_URL);
        $this->view('about', compact('docteur', 'equipe'));
        error_log("Vue about rendue");
    }
}