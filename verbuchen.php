<?php
 
// * Hauptdatei - Verbindet alle Module

// 1. Konfiguration einbinden
require_once 'config/database.php';

// 2. Module einbinden
require_once 'modules/layout.php';
require_once 'modules/sidebar.php';

// 3. Templates einbinden  
require_once 'templates/header.php';
require_once 'templates/sidebar.php';

// 4. Datenbankverbindung herstellen
$pdo = getDatabaseConnection();

// 5. Layout starten
startLayout();

// 6. Sidebar Daten vorbereiten
$currentSelection = [
    'quell_tabelle' => $_POST['quell_tabelle'] ?? '',
    'ziel_tabelle' => $_POST['ziel_tabelle'] ?? 'bankbuchungen',
    'split_funktion' => $_POST['split_funktion'] ?? 'keiner'
];

$sidebarData = getSidebarData($pdo, $currentSelection);

// 7. Sidebar rendern
renderSidebar($sidebarData);

// 8. Hauptbereich starten
echo '<div class="main-content">';
echo '<div class="container">';

// 9. Header rendern
$infoData = [
    'syntax' => 'bank2024mm.csv',
    'status' => 'Bereit für Import', 
    'next_steps' => ['Datei hochladen', 'Mapping prüfen']
];

renderHeader("💾 Verbuchungs-App", $infoData);
// renderHeader();

// 10. Erfolgsmeldung
echo '<div class="success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 4px;">';
echo '✅ <strong>Modulare Verbuchungs-App erfolgreich geladen!</strong><br>';
echo 'Gefundene Tabellen: ' . count($sidebarData['tables']) . '<br>';
echo 'Nächster Schritt: Tabellen-Statistik Modul';
echo '</div>';

// 11. Hauptbereich beenden
echo '</div></div>';

// 12. Layout beenden
endLayout();

///debug
// echo '<!DOCTYPE html><html><head><title>Debug</title><style>body{font-family:Arial; margin:20px;} .error{background:#f8d7da; padding:10px; border-radius:4px;} .success{background:#d4edda; padding:10px; border-radius:4px;}</style></head><body>';
// echo '<h1>🔍 Debug-Modus</h1>';

// try {
//     // 1. Datenbankverbindung
//     $host = '192.168.3.167';
//     $dbname = 'bhokt25';
//     $username = 'dbuser';
//     $password = 'geheim';
    
//     $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
//     echo '<div class="success">✅ Datenbankverbindung erfolgreich</div>';
    
//     // 2. Tabellen abrufen
//     $stmt = $pdo->query("SHOW TABLES");
//     $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
//     echo '<div class="success">✅ Tabellen gefunden: ' . count($tables) . '</div>';
//     echo '<ul>';
//     foreach ($tables as $table) {
//         echo '<li>' . htmlspecialchars($table) . '</li>';
//     }
//     echo '</ul>';
    
// } catch (Exception $e) {
//     echo '<div class="error">❌ Fehler: ' . htmlspecialchars($e->getMessage()) . '</div>';
// }

// echo '</body></html>';
?>