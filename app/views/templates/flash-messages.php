<?php
// Prépare les messages flash pour l'AlertManager
if (isset($_SESSION['success']) || isset($_SESSION['error'])) {
    $messages = [];
    
    if (isset($_SESSION['success'])) {
        $messages[] = [
            'type' => 'success',
            'message' => $_SESSION['success']
        ];
        unset($_SESSION['success']);
    }
    
    if (isset($_SESSION['error'])) {
        $messages[] = [
            'type' => 'error',
            'message' => $_SESSION['error']
        ];
        unset($_SESSION['error']);
    }
    
    if (!empty($messages)) {
        echo '<script>';
        echo "document.addEventListener('DOMContentLoaded', function() {
            if (typeof AlertManager !== 'undefined') {";
        foreach ($messages as $msg) {
            echo "AlertManager.{$msg['type']}(" . json_encode($msg['message']) . ");";
        }
        echo "}});";
        echo '</script>';
    }
}
?>