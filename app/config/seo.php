<?php
/**
 * Configuration SEO pour chaque page du site
 * Définit les meta tags, Open Graph et Twitter Cards
 */

return [
    // Page d'accueil
    'home' => [
        'title' => 'Cabinet Dentaire Dr Dupont - Soins dentaires professionnels',
        'description' => 'Cabinet dentaire moderne à votre service. Consultations, soins dentaires, orthodontie et implantologie. Prenez rendez-vous en ligne facilement.',
        'keywords' => 'dentiste, cabinet dentaire, soins dentaires, orthodontie, implantologie, rendez-vous dentiste, Dr Dupont',
        'og_title' => 'Cabinet Dentaire Dr Dupont',
        'og_description' => 'Votre santé bucco-dentaire entre de bonnes mains. Cabinet moderne équipé des dernières technologies.',
        'og_image' => '/assets/dupontcare-logo-horizontal-DUPONT-white.svg',
        'og_type' => 'website'
    ],
    
    // Page actualités
    'actus' => [
        'title' => 'Actualités - Cabinet Dentaire Dr Dupont',
        'description' => 'Découvrez les dernières actualités de notre cabinet dentaire : nouveaux services, conseils santé bucco-dentaire et informations pratiques.',
        'keywords' => 'actualités dentaire, conseils dentiste, nouveautés cabinet dentaire, santé bucco-dentaire',
        'og_title' => 'Actualités du Cabinet',
        'og_description' => 'Restez informé des dernières actualités de notre cabinet dentaire.',
        'og_type' => 'website'
    ],
    
    // Page à propos
    'about' => [
        'title' => 'À propos - Cabinet Dentaire Dr Dupont',
        'description' => 'Découvrez notre cabinet dentaire, notre équipe de professionnels et nos valeurs. Un cabinet moderne au service de votre sourire.',
        'keywords' => 'dentiste qualifié, équipe dentaire, cabinet moderne, présentation cabinet dentaire, Dr Dupont',
        'og_title' => 'À propos du Cabinet Dr Dupont',
        'og_description' => 'Une équipe de professionnels dédiée à votre santé dentaire.',
        'og_type' => 'website'
    ],
    
    // Page de connexion
    'auth' => [
        'login' => [
            'title' => 'Connexion - Cabinet Dentaire Dr Dupont',
            'description' => 'Connectez-vous à votre espace patient pour gérer vos rendez-vous et accéder à votre dossier médical.',
            'keywords' => 'connexion patient, espace patient, accès compte dentiste',
            'og_title' => 'Connexion patient',
            'og_type' => 'website'
        ],
        'register' => [
            'title' => 'Inscription - Cabinet Dentaire Dr Dupont',
            'description' => 'Créez votre compte patient pour prendre rendez-vous en ligne et accéder à vos informations médicales.',
            'keywords' => 'inscription patient, nouveau patient, créer compte dentiste',
            'og_title' => 'Créer un compte patient',
            'og_type' => 'website'
        ]
    ],
    
    // Page rendez-vous
    'rendezvous' => [
        'select-consultation' => [
            'title' => 'Prendre rendez-vous - Cabinet Dentaire Dr Dupont',
            'description' => 'Prenez rendez-vous en ligne facilement. Choisissez votre type de consultation et réservez un créneau disponible.',
            'keywords' => 'rendez-vous dentiste, prendre rendez-vous en ligne, consultation dentaire, réserver dentiste',
            'og_title' => 'Réserver un rendez-vous',
            'og_description' => 'Réservez votre consultation dentaire en quelques clics.',
            'og_type' => 'website'
        ],
        'list' => [
            'title' => 'Mes rendez-vous - Cabinet Dentaire Dr Dupont',
            'description' => 'Consultez et gérez vos rendez-vous chez le Dr Dupont.',
            'keywords' => 'mes rendez-vous, gérer rendez-vous dentiste',
            'og_title' => 'Mes rendez-vous',
            'og_type' => 'website'
        ]
    ],
    
    // Page administration
    'admin' => [
        'title' => 'Administration - Cabinet Dentaire Dr Dupont',
        'description' => 'Interface d\'administration du cabinet dentaire.',
        'keywords' => 'administration cabinet dentaire',
        'og_type' => 'website',
        'robots' => 'noindex, nofollow' // Ne pas indexer les pages admin
    ],
    
    // Page agenda médecin
    'agenda' => [
        'title' => 'Agenda - Cabinet Dentaire Dr Dupont',
        'description' => 'Gestion de l\'agenda des consultations.',
        'keywords' => 'agenda médecin, planning consultations',
        'og_type' => 'website',
        'robots' => 'noindex, nofollow'
    ],
    
    // Page profil utilisateur
    'user' => [
        'profile' => [
            'title' => 'Mon profil - Cabinet Dentaire Dr Dupont',
            'description' => 'Gérez vos informations personnelles et vos préférences.',
            'keywords' => 'profil patient, informations personnelles',
            'og_title' => 'Mon profil patient',
            'og_type' => 'website',
            'robots' => 'noindex, nofollow'
        ],
        'edit' => [
            'title' => 'Modifier mon profil - Cabinet Dentaire Dr Dupont',
            'description' => 'Modifiez vos informations personnelles.',
            'keywords' => 'modifier profil, éditer informations',
            'og_type' => 'website',
            'robots' => 'noindex, nofollow'
        ]
    ],
    
    // Valeurs par défaut
    'default' => [
        'title' => 'Cabinet Dentaire Dr Dupont',
        'description' => 'Cabinet dentaire professionnel à votre service.',
        'keywords' => 'dentiste, cabinet dentaire, soins dentaires',
        'og_title' => 'Cabinet Dentaire Dr Dupont',
        'og_description' => 'Votre santé bucco-dentaire entre de bonnes mains.',
        'og_type' => 'website'
    ]
];
