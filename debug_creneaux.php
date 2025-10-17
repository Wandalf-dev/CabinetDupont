<?php
// Script de debug pour vérifier les créneaux du 31/10
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/app/models/database.php';

use App\Models\Database;

try {
    /** @var PDO $db */
    $db = Database::getInstance()->getConnection();
    
    echo "<h2>Debug - Créneaux du 31/10/2025</h2>";
    
    // Tous les créneaux du 31/10
    $sql = "SELECT 
                c.id,
                c.debut,
                c.fin,
                c.statut,
                c.est_reserve,
                c.service_id,
                r.id as rdv_id,
                r.statut as rdv_statut
            FROM creneau c
            LEFT JOIN rendezvous r ON c.id = r.creneau_id
            WHERE DATE(c.debut) = '2025-10-31'
            ORDER BY c.debut ASC";
    
    $stmt = $db->query($sql);
    $creneaux = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Total créneaux trouvés : " . count($creneaux) . "</h3>";
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr>
            <th>ID</th>
            <th>Heure début</th>
            <th>Statut</th>
            <th>Est réservé</th>
            <th>Service ID</th>
            <th>RDV ID</th>
            <th>RDV Statut</th>
            <th>Disponible?</th>
          </tr>";
    
    $disponibles = 0;
    foreach ($creneaux as $c) {
        $estDispo = (
            $c['est_reserve'] == 0 &&
            $c['statut'] != 'indisponible' &&
            ($c['rdv_id'] === null || $c['rdv_statut'] == 'ANNULE')
        );
        
        if ($estDispo) $disponibles++;
        
        echo "<tr style='background-color: " . ($estDispo ? '#90EE90' : '#FFB6C1') . "'>";
        echo "<td>{$c['id']}</td>";
        echo "<td>" . date('H:i', strtotime($c['debut'])) . "</td>";
        echo "<td>{$c['statut']}</td>";
        echo "<td>" . ($c['est_reserve'] ? 'OUI' : 'NON') . "</td>";
        echo "<td>{$c['service_id']}</td>";
        echo "<td>" . ($c['rdv_id'] ?? '-') . "</td>";
        echo "<td>" . ($c['rdv_statut'] ?? '-') . "</td>";
        echo "<td>" . ($estDispo ? '✅ OUI' : '❌ NON') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p><strong>Créneaux disponibles : $disponibles</strong></p>";
    
    // Test de la requête getDatesDisponibles pour le 31/10
    echo "<h3>Test de getDatesDisponibles() - VERSION CORRIGÉE</h3>";
    $sql2 = "SELECT DISTINCT DATE(c.debut) AS date
            FROM creneau c
            WHERE DATE(c.debut) >= CURRENT_DATE()
              AND DATE(c.debut) <= DATE_ADD(CURRENT_DATE(), INTERVAL 14 DAY)
              AND c.est_reserve = 0
              AND c.statut != 'indisponible'
              AND NOT EXISTS (
                  SELECT 1 FROM rendezvous r
                  WHERE r.creneau_id = c.id AND r.statut != 'ANNULE'
              )
              AND DATE(c.debut) = '2025-10-31'
            ORDER BY date ASC";
    
    $stmt2 = $db->query($sql2);
    $dates = $stmt2->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<p><strong>Requête SQL testée :</strong></p>";
    echo "<pre>" . htmlspecialchars($sql2) . "</pre>";
    
    if (!empty($dates)) {
        echo "<p style='color: green; font-weight: bold;'>✅ Le 31/10 DEVRAIT être disponible</p>";
        echo "<p>Date retournée : " . implode(', ', $dates) . "</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Le 31/10 N'EST PAS retourné par getDatesDisponibles()</p>";
        
        // Debug : vérifier chaque condition
        echo "<h4>Debug des conditions :</h4>";
        
        $debug1 = $db->query("SELECT COUNT(*) FROM creneau c WHERE DATE(c.debut) = '2025-10-31'")->fetchColumn();
        echo "<p>1. Créneaux le 31/10 : <strong>$debug1</strong></p>";
        
        $debug2 = $db->query("SELECT COUNT(*) FROM creneau c WHERE DATE(c.debut) = '2025-10-31' AND c.debut >= CURRENT_DATE()")->fetchColumn();
        echo "<p>2. Après CURRENT_DATE : <strong>$debug2</strong></p>";
        
        $debug3 = $db->query("SELECT COUNT(*) FROM creneau c WHERE DATE(c.debut) = '2025-10-31' AND c.debut >= CURRENT_DATE() AND c.debut <= DATE_ADD(CURRENT_DATE(), INTERVAL 14 DAY)")->fetchColumn();
        echo "<p>3. Dans les 14 jours : <strong>$debug3</strong></p>";
        
        $debug4 = $db->query("SELECT COUNT(*) FROM creneau c WHERE DATE(c.debut) = '2025-10-31' AND c.debut >= CURRENT_DATE() AND c.debut <= DATE_ADD(CURRENT_DATE(), INTERVAL 14 DAY) AND c.est_reserve = 0")->fetchColumn();
        echo "<p>4. Non réservés : <strong>$debug4</strong></p>";
        
        $debug5 = $db->query("SELECT COUNT(*) FROM creneau c WHERE DATE(c.debut) = '2025-10-31' AND c.debut >= CURRENT_DATE() AND c.debut <= DATE_ADD(CURRENT_DATE(), INTERVAL 14 DAY) AND c.est_reserve = 0 AND c.statut != 'indisponible'")->fetchColumn();
        echo "<p>5. Statut disponible : <strong>$debug5</strong></p>";
        
        // Vérifier la date actuelle MySQL
        $currentDate = $db->query("SELECT CURRENT_DATE()")->fetchColumn();
        echo "<p><strong>Date actuelle MySQL :</strong> $currentDate</p>";
        
        $dateAdd = $db->query("SELECT DATE_ADD(CURRENT_DATE(), INTERVAL 14 DAY)")->fetchColumn();
        echo "<p><strong>Date + 14 jours :</strong> $dateAdd</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>";
}
?>
