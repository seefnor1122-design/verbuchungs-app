<?php
/**
 * Hauptdatei - Verbindet alle Module
 */

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
renderHeader();

// 10. Hier kommt später die Haupt-Logik...
echo '<div class="info">';
echo '🚀 <strong>Modulare Verbuchungs-App</strong><br>';
echo 'Grundgerüst erfolgreich geladen! Nächste Schritte:';
echo '<ul>';
echo '<li>✅ Basis-Layout mit Sidebar</li>';
echo '<li>✅ Getrennte Logik/Design</li>';
echo '<li>🔲 Tabellen-Statistik</li>';
echo '<li>🔲 Simulations-Modus</li>';
echo '<li>🔲 Echte Verbuchung</li>';
echo '</ul>';
echo '</div>';

// 11. Hauptbereich beenden
echo '</div></div>';

// 12. Layout beenden
endLayout();
?>