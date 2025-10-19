<?php

namespace App\Core;

/**
 * Classe utilitaire pour gérer les métadonnées SEO
 */
class Seo {
    private static $seoConfig = null;
    private static $currentMeta = [];
    
    /**
     * Charge la configuration SEO
     */
    private static function loadConfig() {
        if (self::$seoConfig === null) {
            $configPath = __DIR__ . '/../config/seo.php';
            if (file_exists($configPath)) {
                self::$seoConfig = require $configPath;
            } else {
                self::$seoConfig = [];
            }
        }
    }
    
    /**
     * Récupère les métadonnées pour une page donnée
     * 
     * @param string $page Nom de la page (ex: 'home', 'actus')
     * @param string $action Action de la page (ex: 'login', 'register')
     * @return array Métadonnées de la page
     */
    public static function getMeta($page = 'home', $action = null) {
        self::loadConfig();
        
        // Si une action est spécifiée, chercher dans page->action
        if ($action && isset(self::$seoConfig[$page][$action])) {
            $meta = self::$seoConfig[$page][$action];
        }
        // Sinon chercher directement la page
        elseif (isset(self::$seoConfig[$page])) {
            $meta = self::$seoConfig[$page];
            // Si c'est un tableau d'actions, prendre la première ou default
            if (is_array($meta) && !isset($meta['title'])) {
                $meta = self::$seoConfig['default'];
            }
        }
        // Valeurs par défaut
        else {
            $meta = self::$seoConfig['default'];
        }
        
        // Ajouter BASE_URL aux images si nécessaire
        if (isset($meta['og_image']) && !str_starts_with($meta['og_image'], 'http')) {
            $meta['og_image'] = BASE_URL . $meta['og_image'];
        }
        
        // Ajouter l'URL canonique
        $meta['canonical'] = self::getCurrentUrl();
        
        self::$currentMeta = $meta;
        return $meta;
    }
    
    /**
     * Définit des métadonnées personnalisées
     * 
     * @param array $meta Métadonnées à définir
     */
    public static function setMeta($meta) {
        self::$currentMeta = array_merge(self::$currentMeta, $meta);
    }
    
    /**
     * Génère les balises meta HTML
     * 
     * @param string $page Page courante
     * @param string $action Action courante
     * @return string HTML des balises meta
     */
    public static function renderMetaTags($page = 'home', $action = null) {
        $meta = self::getMeta($page, $action);
        $html = '';
        
        // Title (sera utilisé dans le <title>)
        // Les autres balises meta
        
        // Description
        if (isset($meta['description'])) {
            $html .= '<meta name="description" content="' . htmlspecialchars($meta['description']) . '" />' . "\n    ";
        }
        
        // Keywords
        if (isset($meta['keywords'])) {
            $html .= '<meta name="keywords" content="' . htmlspecialchars($meta['keywords']) . '" />' . "\n    ";
        }
        
        // Robots
        if (isset($meta['robots'])) {
            $html .= '<meta name="robots" content="' . htmlspecialchars($meta['robots']) . '" />' . "\n    ";
        } else {
            $html .= '<meta name="robots" content="index, follow" />' . "\n    ";
        }
        
        // Canonical URL
        if (isset($meta['canonical'])) {
            $html .= '<link rel="canonical" href="' . htmlspecialchars($meta['canonical']) . '" />' . "\n    ";
        }
        
        // Open Graph
        $html .= '<meta property="og:site_name" content="Cabinet Dentaire Dr Dupont" />' . "\n    ";
        
        if (isset($meta['og_title'])) {
            $html .= '<meta property="og:title" content="' . htmlspecialchars($meta['og_title']) . '" />' . "\n    ";
        }
        
        if (isset($meta['og_description'])) {
            $html .= '<meta property="og:description" content="' . htmlspecialchars($meta['og_description']) . '" />' . "\n    ";
        }
        
        if (isset($meta['og_type'])) {
            $html .= '<meta property="og:type" content="' . htmlspecialchars($meta['og_type']) . '" />' . "\n    ";
        }
        
        if (isset($meta['og_image'])) {
            $html .= '<meta property="og:image" content="' . htmlspecialchars($meta['og_image']) . '" />' . "\n    ";
        }
        
        $html .= '<meta property="og:url" content="' . htmlspecialchars(self::getCurrentUrl()) . '" />' . "\n    ";
        
        // Twitter Cards
        $html .= '<meta name="twitter:card" content="summary_large_image" />' . "\n    ";
        
        if (isset($meta['og_title'])) {
            $html .= '<meta name="twitter:title" content="' . htmlspecialchars($meta['og_title']) . '" />' . "\n    ";
        }
        
        if (isset($meta['og_description'])) {
            $html .= '<meta name="twitter:description" content="' . htmlspecialchars($meta['og_description']) . '" />' . "\n    ";
        }
        
        if (isset($meta['og_image'])) {
            $html .= '<meta name="twitter:image" content="' . htmlspecialchars($meta['og_image']) . '" />' . "\n    ";
        }
        
        // Meta supplémentaires utiles
        $html .= '<meta name="author" content="Cabinet Dentaire Dr Dupont" />' . "\n    ";
        $html .= '<meta name="viewport" content="width=device-width, initial-scale=1.0" />' . "\n    ";
        
        return $html;
    }
    
    /**
     * Récupère le titre de la page
     * 
     * @param string $page Page courante
     * @param string $action Action courante
     * @return string Titre de la page
     */
    public static function getTitle($page = 'home', $action = null) {
        $meta = self::getMeta($page, $action);
        return $meta['title'] ?? 'Cabinet Dentaire Dr Dupont';
    }
    
    /**
     * Récupère l'URL courante
     * 
     * @return string URL complète
     */
    private static function getCurrentUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        return $protocol . '://' . $host . $uri;
    }
    
    /**
     * Génère les données structurées JSON-LD pour Schema.org
     * 
     * @param string $type Type de données structurées
     * @return string JSON-LD
     */
    public static function renderStructuredData($type = 'organization') {
        $html = '<script type="application/ld+json">' . "\n";
        
        switch ($type) {
            case 'organization':
                $data = [
                    "@context" => "https://schema.org",
                    "@type" => "Dentist",
                    "name" => "Cabinet Dentaire Dr Dupont",
                    "description" => "Cabinet dentaire professionnel offrant des soins dentaires de qualité",
                    "url" => BASE_URL,
                    "logo" => BASE_URL . "/assets/dupontcare-logo-horizontal-DUPONT-white.svg",
                    "image" => BASE_URL . "/assets/dupontcare-logo-horizontal-DUPONT-white.svg",
                    "priceRange" => "$$",
                    "address" => [
                        "@type" => "PostalAddress",
                        "addressCountry" => "FR",
                        "addressLocality" => "Paris"
                    ],
                    "contactPoint" => [
                        "@type" => "ContactPoint",
                        "contactType" => "Reception",
                        "availableLanguage" => ["French"]
                    ]
                ];
                break;
                
            case 'medicalBusiness':
                $data = [
                    "@context" => "https://schema.org",
                    "@type" => "MedicalBusiness",
                    "name" => "Cabinet Dentaire Dr Dupont",
                    "medicalSpecialty" => "Dentistry",
                    "url" => BASE_URL
                ];
                break;
                
            default:
                $data = [];
        }
        
        if (!empty($data)) {
            $html .= json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        
        $html .= "\n</script>";
        
        return $html;
    }
}
