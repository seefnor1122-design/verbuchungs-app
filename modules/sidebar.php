<?php
// modules/sidebar.php

function getSidebarData($pdo, $currentSelection = []) {
    // Deine bestehende Logik für Tabellen etc.
    $tables = [];
    try {
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e) {
        $tables = [];
    }
    
    return [
        'tables' => $tables,
        'current_selection' => $currentSelection
    ];
}

function getSidebarContent($currentModule) {
    $content = [
        'title' => '',
        'controls' => [],
        'info' => []
    ];
    
    switch($currentModule) {
        case 'import':
            $content['title'] = '🗃️ Bankdaten-Import';
            $content['controls'] = [
                'type' => 'file_upload',
                'banks' => ['Sparkasse', 'PayPal', 'Postbank', 'Volksbank', 'Commerzbank']
            ];
            $content['info'] = [
                'Sparkasse' => '📋 Format: CSV | Encoding: ANSI | Trennzeichen: Semikolon',
                'PayPal' => '📋 Format: CSV | Encoding: UTF-8 | Trennzeichen: Komma',
                'Postbank' => '📋 Format: CSV | Encoding: ISO-8859-1 | Spalten: Datum/Betrag/Text',
                'Volksbank' => '📋 Format: CSV | Encoding: UTF-8 | Trennzeichen: Semikolon',
                'Commerzbank' => '📋 Format: CSV | Encoding: ANSI | Trennzeichen: Semikolon'
            ];
            break;
            
        case 'tabellen':
            $content['title'] = '🗃️ Tabellen-Verwaltung';
            $content['controls'] = [
                'type' => 'table_management',
                'actions' => ['Tabelle anzeigen', 'Struktur prüfen', 'Daten exportieren']
            ];
            $content['info'] = [
                'bankbuchungen' => 'Haupttabelle für Banktransaktionen',
                'import_log' => 'Protokoll aller Import-Vorgänge',
                'kategorien' => 'Buchungskategorien und Mapping'
            ];
            break;
            
        case 'buchen':
            $content['title'] = '💳 Verbuchungs-Steuerung';
            $content['controls'] = [
                'type' => 'booking_controls',
                'actions' => ['Automatisch buchen', 'Manuell buchen', 'Buchungen prüfen']
            ];
            $content['info'] = [
                'Automatisch' => 'Buchungen werden automatisch kategorisiert',
                'Manuell' => 'Einzelne Buchungen manuell zuordnen',
                'Prüfen' => 'Buchungen vor Verbuchung kontrollieren'
            ];
            break;
            
        case 'berichte':
            $content['title'] = '📊 Berichte & Analysen';
            $content['controls'] = [
                'type' => 'reports',
                'reports' => ['Monatsübersicht', 'Kategorie-Analyse', 'Umsatzentwicklung']
            ];
            $content['info'] = [
                'Monatsübersicht' => 'Einnahmen/Ausgaben pro Monat',
                'Kategorie-Analyse' => 'Auswertung nach Kategorien',
                'Umsatzentwicklung' => 'Trends über Zeiträume'
            ];
            break;
            
        case 'sonstiges':
            $content['title'] = '⚙️ Einstellungen & Tools';
            $content['controls'] = [
                'type' => 'tools',
                'tools' => ['Datenbank bereinigen', 'Backup erstellen', 'Einstellungen']
            ];
            $content['info'] = [
                'Datenbank' => 'Bereinigung alter Import-Daten',
                'Backup' => 'Sicherung der Buchhaltungsdaten',
                'Einstellungen' => 'App-Konfiguration anpassen'
            ];
            break;
            
        default:
            $content['title'] = '🏦 Verbuchungs-Steuerung';
            $content['controls'] = [
                'type' => 'default',
                'actions' => ['Modul auswählen']
            ];
    }
    
    return $content;
}

function renderSidebar($sidebarData, $currentModule = 'import') {
    $sidebarContent = getSidebarContent($currentModule);
    
    echo '<div class="sidebar">';
    echo '<div class="sidebar-header">';
    echo '<h3>' . $sidebarContent['title'] . '</h3>';
    echo '</div>';
    
    // Steuerungs-Elemente
    echo '<div class="sidebar-controls">';
    renderSidebarControls($sidebarContent['controls']);
    echo '</div>';
    
    // Info-Bereich
    echo '<div class="sidebar-info">';
    renderSidebarInfo($sidebarContent['info']);
    echo '</div>';
    
    echo '</div>';
}

function renderSidebarControls($controls) {
    echo '<div class="control-section">';
    
    switch($controls['type']) {
        case 'file_upload':
            echo '<h4>📁 Datei hochladen</h4>';
            echo '<form method="post" enctype="multipart/form-data" id="uploadForm">';
            echo '<input type="file" name="bank_file" accept=".csv,.txt" style="margin: 10px 0; width: 100%;" required>';
            echo '<select name="bank_type" style="margin: 10px 0; padding: 8px; width: 100%;" required>';
            echo '<option value="">-- Bank auswählen --</option>';
            
            // EINFACH: Immer alle Banken anzeigen, keine Session-Logik
            foreach ($controls['banks'] as $bank) {
                echo '<option value="' . strtolower($bank) . '">' . $bank . '</option>';
            }
            echo '</select>';
            echo '<button type="submit" name="upload_file" style="background: #28a745; color: white; border: none; padding: 10px; width: 100%; border-radius: 4px; cursor: pointer;">Hochladen & Vorschau</button>';
            echo '</form>';
            break;
    //     case 'file_upload':
    //          // DEBUG HINZUFÜGEN
    //         echo "<!-- DEBUG: LastBankType: " . getLastBankType() . " -->";
    //         echo "<!-- DEBUG: UploadResult: " . (getUploadResult() ? 'JA' : 'NEIN') . " -->";
            
    //         $uploadResult = getUploadResult();
    //         if ($uploadResult) {
    //             echo "<!-- DEBUG: UploadSuccess: " . ($uploadResult['success'] ? 'true' : 'false') . " -->";
    //             echo "<!-- DEBUG: DataCount: " . count($uploadResult['data'] ?? []) . " -->";
    // }
    //         echo '<h4>📁 Datei hochladen</h4>';
    //         echo '<form method="post" enctype="multipart/form-data" id="uploadForm">';
    //         echo '<input type="file" name="bank_file" accept=".csv,.txt" style="margin: 10px 0; width: 100%;" required>';
    //         echo '<select name="bank_type" style="margin: 10px 0; padding: 8px; width: 100%;" required>';
    //         echo '<option value="">-- Bank auswählen --</option>';
            
    //         $lastBankType = getLastBankType();
    //         foreach ($controls['banks'] as $bank) {
    //             $bankKey = strtolower($bank);
    //             $selected = ($lastBankType === $bankKey) ? 'selected' : '';
    //             echo '<option value="' . $bankKey . '" ' . $selected . '>' . $bank . '</option>';
    //         }
    //         echo '</select>';
    //         echo '<button type="submit" name="upload_file" style="background: #28a745; color: white; border: none; padding: 10px; width: 100%; border-radius: 4px; cursor: pointer;">Hochladen & Vorschau</button>';
    //         echo '</form>';
            
    //         // Upload-Ergebnis anzeigen
    //         $uploadResult = getUploadResult();
    //         if ($uploadResult) {
    //             $bgColor = $uploadResult['success'] ? '#d4edda' : '#f8d7da';
    //             $textColor = $uploadResult['success'] ? '#155724' : '#721c24';
                
    //             echo '<div style="margin-top: 10px; padding: 10px; background: ' . $bgColor . '; color: ' . $textColor . '; border-radius: 4px;">';
    //             echo $uploadResult['message'];
    //             echo '</div>';
                
    //             // Vorschau der Daten anzeigen falls erfolgreich
    //             if ($uploadResult['success'] && !empty($uploadResult['data'])) {
    //                 echo '<div style="margin-top: 15px; padding: 10px; background: #e9ecef; border-radius: 4px;">';
    //                 echo '<strong>📊 Vorschau (erste 3 Zeilen):</strong><br>';
                    
    //                 foreach (array_slice($uploadResult['data'], 0, 3) as $index => $row) {
    //                     echo '<div style="font-size: 12px; margin: 5px 0; padding: 5px; background: white; border-radius: 3px;">';
    //                     echo ($index + 1) . '. ' . ($row['buchungsdatum'] ?? 'NULL') . ' | ' . 
    //                         ($row['betrag'] ?? '0') . ' | ' . 
    //                         substr($row['buchungstext'] ?? 'KEIN TEXT', 0, 30) . '...';
    //                     echo '</div>';
    //                 }
    //                 // Import-Button
    //                 echo '<form method="post" style="margin-top: 10px;">';
    //                 echo '<input type="hidden" name="action" value="import_data">';
    //                 echo '<input type="hidden" name="bank_type" value="' . htmlspecialchars($lastBankType) . '">';
    //                 echo '<button type="submit" style="background: #007bff; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer;">';
    //                 echo '📥 In Datenbank importieren';
    //                 echo '</button>';
    //                 echo '</form>';
    //                 // // Import-Button
    //                 // echo '<form method="post" style="margin-top: 10px;">';
    //                 // echo '<input type="hidden" name="import_data" value="1">';
    //                 // echo '<button type="submit" style="background: #007bff; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer;">';
    //                 // echo '📥 In Datenbank importieren';
    //                 // echo '</button>';
    //                 // echo '</form>';
                    
    //                 echo '</div>';
    //             }
                
                
    //         }
            
    //         // Import-Ergebnis anzeigen
    //         $importResult = getImportResult();
    //         if ($importResult) {
    //             $bgColor = $importResult['success'] ? '#d4edda' : '#f8d7da';
    //             $textColor = $importResult['success'] ? '#155724' : '#721c24';
                
    //             echo '<div style="margin-top: 15px; padding: 10px; background: ' . $bgColor . '; color: ' . $textColor . '; border-radius: 4px;">';
    //             echo '📥 <strong>Import-Ergebnis:</strong><br>' . $importResult['message'];
    //             echo '</div>';
    //             clearUploadSession();
    //         }
    //         break;
        // case 'file_upload':
        //     echo '<h4>📁 Datei hochladen</h4>';
        //     echo '<form method="post" enctype="multipart/form-data" id="uploadForm">';
        //     echo '<input type="file" name="bank_file" accept=".csv,.txt" style="margin: 10px 0; width: 100%;" required>';
        //     echo '<select name="bank_type" style="margin: 10px 0; padding: 8px; width: 100%;" required>';
        //     echo '<option value="">-- Bank auswählen --</option>';
            
        //     // Bank-Type aus Session holen
        //     $lastBankType = getLastBankType();
        //     foreach ($controls['banks'] as $bank) {
        //         $bankKey = strtolower($bank);
        //         $selected = ($lastBankType === $bankKey) ? 'selected' : '';
        //         echo '<option value="' . $bankKey . '" ' . $selected . '>' . $bank . '</option>';
        //     }
        //     echo '</select>';
        //     echo '<button type="submit" name="upload_file" style="background: #28a745; color: white; border: none; padding: 10px; width: 100%; border-radius: 4px; cursor: pointer;">Hochladen & Vorschau</button>';
        //     echo '</form>';
            
        //     // Upload-Ergebnis anzeigen
        //     $uploadResult = getUploadResult();
        //     if ($uploadResult) {
        //         $bgColor = $uploadResult['success'] ? '#d4edda' : '#f8d7da';
        //         $textColor = $uploadResult['success'] ? '#155724' : '#721c24';
                
        //         echo '<div style="margin-top: 10px; padding: 10px; background: ' . $bgColor . '; color: ' . $textColor . '; border-radius: 4px;">';
        //         echo $uploadResult['message'];
        //         echo '</div>';
                
        //         // Vorschau der Daten anzeigen falls erfolgreich
        //         if ($uploadResult['success'] && !empty($uploadResult['data'])) {
        //             echo '<div style="margin-top: 15px; padding: 10px; background: #e9ecef; border-radius: 4px;">';
        //             echo '<strong>📊 Vorschau (erste 3 Zeilen):</strong><br>';
        //             foreach (array_slice($uploadResult['data'], 0, 3) as $index => $row) {
        //                 echo '<div style="font-size: 12px; margin: 5px 0; padding: 5px; background: white; border-radius: 3px;">';
        //                 echo ($index + 1) . '. ' . ($row['buchungsdatum'] ?? '') . ' | ' . ($row['betrag'] ?? 0) . ' | ' . substr($row['buchungstext'] ?? '', 0, 30) . '...';
        //                 echo '</div>';
        //             }
                    
        //             // Import-Button falls Daten gut aussehen
        //             echo '<form method="post" style="margin-top: 10px;">';
        //             echo '<input type="hidden" name="import_data" value="1">';
        //             echo '<button type="submit" style="background: #007bff; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer;">📥 In Datenbank importieren</button>';
        //             echo '</form>';
                    
        //             echo '</div>';
        //         }
        //         // Import-Ergebnis anzeigen
        //         $importResult = getImportResult();
        //         if ($importResult) {
        //             $bgColor = $importResult['success'] ? '#d4edda' : '#f8d7da';
        //             $textColor = $importResult['success'] ? '#155724' : '#721c24';
                    
        //             echo '<div style="margin-top: 15px; padding: 10px; background: ' . $bgColor . '; color: ' . $textColor . '; border-radius: 4px;">';
        //             echo '📥 <strong>Import-Ergebnis:</strong><br>' . $importResult['message'];
        //             echo '</div>';
        //         }
        //         // Session nach Anzeige löschen
        //         clearUploadSession();
        //     }
        //     break;
        // case 'file_upload':
        //     echo '<h4>📁 Datei hochladen</h4>';
        //     echo '<form method="post" enctype="multipart/form-data" id="uploadForm">';
        //     echo '<input type="file" name="bank_file" accept=".csv,.txt" style="margin: 10px 0; width: 100%;" required>';
        //     echo '<select name="bank_type" style="margin: 10px 0; padding: 8px; width: 100%;" required>';
        //     echo '<option value="">-- Bank auswählen --</option>';
            
        //     $lastBankType = getLastBankType();
        //     foreach ($controls['banks'] as $bank) {
        //         $bankKey = strtolower($bank);
        //         $selected = ($lastBankType === $bankKey) ? 'selected' : '';
        //         echo '<option value="' . $bankKey . '" ' . $selected . '>' . $bank . '</option>';
        //     }
        //     echo '</select>';
        //     echo '<button type="submit" name="upload_file" style="background: #28a745; color: white; border: none; padding: 10px; width: 100%; border-radius: 4px; cursor: pointer;">Hochladen & Vorschau</button>';
        //     echo '</form>';
            
        //     // Upload-Ergebnis anzeigen
        //     $uploadResult = getUploadResult();
        //     if ($uploadResult) {
        //         $bgColor = $uploadResult['success'] ? '#d4edda' : '#f8d7da';
        //         $textColor = $uploadResult['success'] ? '#155724' : '#721c24';
                
        //         echo '<div style="margin-top: 10px; padding: 10px; background: ' . $bgColor . '; color: ' . $textColor . '; border-radius: 4px;">';
        //         echo $uploadResult['message'];
        //         echo '</div>';
                
        //         // Vorschau der Daten anzeigen falls erfolgreich
        //         if ($uploadResult['success'] && !empty($uploadResult['data'])) {
        //             echo '<div style="margin-top: 15px; padding: 10px; background: #e9ecef; border-radius: 4px;">';
        //             echo '<strong>📊 Vorschau (erste 3 Zeilen):</strong><br>';
        //             foreach (array_slice($uploadResult['data'], 0, 3) as $index => $row) {
        //                 echo '<div style="font-size: 12px; margin: 5px 0; padding: 5px; background: white; border-radius: 3px;">';
        //                 echo ($index + 1) . '. ' . ($row['buchungsdatum'] ?? '') . ' | ' . ($row['betrag'] ?? 0) . ' | ' . substr($row['buchungstext'] ?? '', 0, 30) . '...';
        //                 echo '</div>';
        //             }
        //             echo '</div>';
        //         }
        //     }
        //     break;
            
        // Weitere Cases für andere Module...
    }
    
    echo '</div>';
}

function renderSidebarInfo($info) {
    echo '<div class="info-section">';
    echo '<h4>ℹ️ Informationen</h4>';
    foreach ($info as $title => $description) {
        echo '<div class="info-item" style="margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 4px;">';
        echo '<strong>' . $title . ':</strong><br>';
        echo '<small style="color: #666;">' . $description . '</small>';
        echo '</div>';
    }
    echo '</div>';
}
?>