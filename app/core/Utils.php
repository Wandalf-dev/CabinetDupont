<?php

namespace App\Core;

class Utils {
    /**
     * Formate un numéro de téléphone pour l'affichage
     * @param string $telephone Le numéro de téléphone à formater
     * @param bool $withPrefix Si true, garde le préfixe international
     * @return string Le numéro formaté
     */
    public static function formatTelephone($telephone, $withPrefix = true) {
        // Si le numéro est vide, retourner une chaîne vide
        if (empty($telephone)) {
            return '';
        }
        
        // Si c'est déjà au format souhaité, le retourner tel quel
        if (preg_match('/^\+33-\d-\d{2}-\d{2}-\d{2}-\d{2}$/', $telephone)) {
            return $telephone;
        }
        
        // Nettoyer le numéro pour ne garder que les chiffres
        $digits = preg_replace('/[^0-9]/', '', $telephone);
        
        // Si le numéro commence par 33, le retirer
        if (strpos($digits, '33') === 0) {
            $digits = substr($digits, 2);
        }
        
        // Si le numéro commence par 0, le retirer
        if (strpos($digits, '0') === 0) {
            $digits = substr($digits, 1);
        }
        
        // Si on n'a pas 9 chiffres, retourner le numéro original
        if (strlen($digits) !== 9) {
            return $telephone;
        }
        
        // Formater le numéro
        $formatted = sprintf(
            '%s%s-%s-%s-%s-%s',
            $withPrefix ? '+33-' : '',
            substr($digits, 0, 1),
            substr($digits, 1, 2),
            substr($digits, 3, 2),
            substr($digits, 5, 2),
            substr($digits, 7, 2)
        );
        
        return $formatted;
    }
}