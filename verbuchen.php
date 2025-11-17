<?php
// * Hauptdatei - Verbindet alle Module

// 1. Konfiguration einbinden
require_once 'config/database.php';

// 2. Module einbinden
require_once 'modules/layout.php';
require_once 'modules/sidebar.php';
require_once 'modules/csv_parser.php'; // NEU - direkt den Parser

// 3. Templates einbinden  
require_once 'templates/header.php';

// 4. Datenbankverbindung herstellen
$pdo = getDatabaseConnection();
if (isset($_POST['import_now'])) {
    // echo "import_now erkannt database". $_POST['import_now'];
}
// 5. Aktuelles Modul aus URL-Parameter erkennen
$currentModule = $_GET['module'] ?? 'import';

// 6. Layout starten
startLayout();

// 7. Sidebar Daten vorbereiten und rendern
$currentSelection = [
    'quell_tabelle' => $_POST['quell_tabelle'] ?? '',
    'ziel_tabelle' => $_POST['ziel_tabelle'] ?? 'bankbuchungen', 
    'split_funktion' => $_POST['split_funktion'] ?? 'keiner'
];

$sidebarData = getSidebarData($pdo, $currentSelection);
renderSidebar($sidebarData, $currentModule);
if (isset($_POST['import_now'])) {
    // echo "import_now erkannt sidebardata". $_POST['import_now'];
}
// 8. Hauptbereich starten
echo '<div class="main-content">';
echo '<div class="container">';

// 9. Header rendern
$moduleTitles = [
    'import' => '📁 Bankdaten Import',
    'tabellen' => '🗃️ Tabellen-Verwaltung',
    'buchen' => '💳 Verbuchungs-Steuerung', 
    'berichte' => '📊 Berichte & Analysen',
    'sonstiges' => '⚙️ Einstellungen & Tools'
];

$title = $moduleTitles[$currentModule] ?? '🏦 Buchhaltungs-App';
renderHeader($title);
if (isset($_POST['import_now'])) {
    // echo "import_now erkannt titel". $_POST['import_now'];
}
// 10. EINFACHER UPLOAD & IMPORT HANDLER
if (isset($_FILES['bank_file']) && isset($_POST['bank_type'])) {
    handleFileUploadAndPreview();
}
if (isset($_POST['import_now'])) {
    // echo "<br> import_now erkannt handleFileUploadAndPreview". $_POST['import_now'];
}
if (isset($_POST['import_now'])) {
    // echo "<br><br><br><br><br><br> DEBUG: import_now erkannt xxxxxx";
    handleImport();
}
if (isset($_POST['import_now'])) {
   
}   
// 11. Modul-spezifischen Hauptinhalt anzeigen
echo '<div class="module-content">';
renderModuleContent($currentModule, $sidebarData);
echo '</div>';

// 12. Hauptbereich beenden
echo '</div></div>';

// 13. Layout beenden
endLayout();

// ===== NEUE EINFACHE FUNKTIONEN =====

function handleFileUploadAndPreview() {
    $bankType = $_POST['bank_type'];
    $pdo = getDatabaseConnection();
    
    $uploadResult = handleFileUpload($_FILES['bank_file'], $bankType, $pdo);
    
    if ($uploadResult['success']) {
        echo '<div style="background: #d4edda; color: #155724; padding: 15px; margin: 20px 0; border-radius: 4px;">';
        echo '✅ <strong>' . $uploadResult['stats']['valid_rows'] . ' Buchungen erkannt!</strong>';
        echo '</div>';
        
        showLivePreview($uploadResult['data']);
        showImportButton($uploadResult['data'], $bankType);
    } else {
        echo '<div style="background: #f8d7da; color: #721c24; padding: 15px; margin: 20px 0; border-radius: 4px;">';
        echo '❌ ' . $uploadResult['message'];
        echo '</div>';
    }
}
function handleImport() {
    // echo "DEBUG: handleImport() gestartet <br>";
    
    $bankType = $_POST['bank_type'] ?? 'UNBEKANNT';
    $jsonData = $_POST['import_data'] ?? 'KEINE DATEN';
    
    // echo "DEBUG: BankType: $bankType --><br>";
    // echo "DEBUG: JSON Data Length: " . strlen($jsonData) . " --><br>";
    
    // Prüfe ob JSON Daten vorhanden sind
    if (empty($jsonData) || $jsonData === 'KEINE DATEN') {
       
        return;
    }
    
    $data = json_decode($jsonData, true);
    // echo "DEBUG: Decoded Data Count: " . count($data) . " --><br>";
    
    if (empty($data)) {
        // echo "DEBUG: JSON DECODE FEHLGESCHLAGEN --><br>";
        return;
    }
    
    $pdo = getDatabaseConnection();
    // echo "DEBUG: Datenbankverbindung hergestellt --><br>";
    
    $imported = importDataToDatabase($data, $pdo, $bankType);
    // print_r($imported);
    echo '<div style="background: #d4edda; color: #155724; padding: 15px; margin: 20px 0; border-radius: 4px;">';
    echo '✅ <strong>FERTIG!</strong> ' . $imported . ' Buchungen in umsatz_' . $bankType . '2025 importiert!';
    echo '</div>';
}
// function handleImport() {
//     $bankType = $_POST['bank_type'];
//     $jsonData = $_POST['import_data'];
//     $data = json_decode($jsonData, true);
    
//     $pdo = getDatabaseConnection();
//     $imported = importDataToDatabase($data, $pdo, $bankType);
    
//     echo '<div style="background: #d4edda; color: #155724; padding: 15px; margin: 20px 0; border-radius: 4px;">';
//     echo '✅ <strong>FERTIG!</strong> ' . $imported . ' Buchungen in umsatz_' . $bankType . '2025 importiert!';
//     echo '</div>';
// }

function showLivePreview($data) {
    echo '<table style="width: 100%; font-size: 12px; border-collapse: collapse; background: white;">';
    echo '<tr style="background: #007bff; color: white;">';
    echo '<th>#</th><th>Roh-Datum</th><th>Konvertiert</th><th>Betrag</th><th>Text</th>';
    echo '</tr>';
    
    foreach (array_slice($data, 0, 10) as $index => $row) {
        echo '<tr>';
        echo '<td>' . ($index + 1) . '</td>';
        echo '<td>' . ($row['rohdatum_buchung'] ?? '❌') . '</td>';
        echo '<td>' . ($row['buchungsdatum'] ?? '❌') . '</td>';
        echo '<td>' . ($row['betrag'] ?? '0') . ' €</td>';
        echo '<td>' . ($row['buchungstext'] ?? 'KEIN TEXT') . '</td>';
        echo '</tr>';
    }
    echo '</table>';
}
// function showLivePreview($data) {
//     echo '<div style="background: #fff3cd; color: #856404; padding: 15px; margin: 20px 0; border-radius: 4px;">';
//     echo '<strong>📊 LIVE-VORSCHAU (erste 10 Zeilen):</strong>';
//     echo '<div style="max-height: 400px; overflow-y: auto; margin: 10px 0;">';
//     echo '<table style="width: 100%; font-size: 12px; border-collapse: collapse; background: white;">';
//     echo '<tr style="background: #007bff; color: white;">';
//     echo '<th style="padding: 8px; border: 1px solid #ccc;">#</th>';
//     echo '<th style="padding: 8px; border: 1px solid #ccc;">Datum</th>';
//     echo '<th style="padding: 8px; border: 1px solid #ccc;">Betrag</th>';
//     echo '<th style="padding: 8px; border: 1px solid #ccc;">Buchungstext</th>';
//     echo '</tr>';
    
//     foreach (array_slice($data, 0, 10) as $index => $row) {
//         $bgColor = $index % 2 ? '#f8f9fa' : '#ffffff';
//         echo '<tr style="background: ' . $bgColor . ';">';
//         echo '<td style="padding: 8px; border: 1px solid #ccc;">' . ($index + 1) . '</td>';
//         echo '<td style="padding: 8px; border: 1px solid #ccc;">' . ($row['buchungsdatum'] ?? '❌ FEHLT') . '</td>';
//         echo '<td style="padding: 8px; border: 1px solid #ccc;">' . ($row['betrag'] ?? '0') . ' €</td>';
//         echo '<td style="padding: 8px; border: 1px solid #ccc;">' . ($row['buchungstext'] ?? 'KEIN TEXT') . '</td>';
//         echo '</tr>';
//     }
//     echo '</table>';
//     echo '</div>';
//     echo '<em>Wenn die Daten korrekt aussehen → Importieren klicken</em>';
//     echo '</div>';
// }
function showImportButton($data, $bankType) {
    $jsonData = htmlspecialchars(json_encode($data));
    echo "<!-- DEBUG: showImportButton - Data Count: " . count($data) . " -->";
    echo "<!-- DEBUG: showImportButton - BankType: $bankType -->";
    
    echo '<form method="post" style="text-align: center; margin: 20px 0;">';
    echo '<input type="hidden" name="import_now" value="1">';
    echo '<input type="hidden" name="bank_type" value="' . htmlspecialchars($bankType) . '">';
    echo '<input type="hidden" name="import_data" value=\'' . $jsonData . '\'>';
    echo '<button type="submit" style="background: #28a745; color: white; border: none; padding: 15px 30px; border-radius: 8px; cursor: pointer; font-size: 16px;">';
    echo '🚀 JETZT IN DATENBANK IMPORTIEREN';
    echo '</button>';
    echo '</form>';
}
// function showImportButton($data, $bankType) {
//     echo '<form method="post" style="text-align: center; margin: 20px 0;">';
//     echo '<input type="hidden" name="import_now" value="1">';
//     echo '<input type="hidden" name="bank_type" value="' . htmlspecialchars($bankType) . '">';
//     echo '<input type="hidden" name="import_data" value=\'' . htmlspecialchars(json_encode($data)) . '\'>';
//     echo '<button type="submit" style="background: #28a745; color: white; border: none; padding: 15px 30px; border-radius: 8px; cursor: pointer; font-size: 16px;">';
//     echo '🚀 JETZT IN DATENBANK IMPORTIEREN';
//     echo '</button>';
//     echo '</form>';
// }

function renderModuleContent($currentModule, $sidebarData) {
    switch($currentModule) {
        case 'import':
            // Standard-Import Ansicht
            echo '<div style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 8px; margin: 20px 0;">';
            echo '<h3>📁 Bankdaten Import</h3>';
            echo '<p>Lade deine Bank-CSV-Dateien in der Sidebar hoch</p>';
            echo '</div>';
            break;
            
        case 'tabellen':
            echo '<div style="background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 4px; margin: 20px 0;">';
            echo '🗃️ <strong>Tabellen-Verwaltung aktiv</strong>';
            echo '</div>';
            break;
            
        // ... andere Module
    }
}
?>