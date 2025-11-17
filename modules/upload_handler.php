<?php
// modules/upload_handler.php - DEBUG VERSION

function handleFileUpload($uploadedFile, $bankType, $pdo) {
    $result = ['success' => false, 'message' => '', 'data' => []];
    
    error_log("DEBUG: handleFileUpload gestartet - BankType: $bankType");
    
    // Validierung
    if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
        $result['message'] = "❌ Upload-Fehler: " . $uploadedFile['error'];
        error_log("DEBUG: Upload-Fehler: " . $uploadedFile['error']);
        return $result;
    }
    
    try {
        require_once 'modules/csv_parser.php';
        error_log("DEBUG: CSV Parser eingebunden");
        
        if ($bankType === 'sparkasse') {
            $parsedData = parseSparkasseCSV($uploadedFile['tmp_name']);
            error_log("DEBUG: Sparkasse CSV geparsed - Success: " . ($parsedData['success'] ? 'true' : 'false'));
        } else {
            $result['message'] = "❌ Banktyp {$bankType} wird noch nicht unterstützt";
            return $result;
        }
        
        if (!$parsedData['success']) {
            $result['message'] = "❌ Fehler beim Parsen: " . implode(', ', $parsedData['errors']);
            return $result;
        }
        
        $result['success'] = true;
        $result['message'] = "✅ " . $parsedData['stats']['valid_rows'] . " Buchungen erkannt";
        $result['data'] = $parsedData['data'];
        $result['stats'] = $parsedData['stats'];
        
        error_log("DEBUG: " . $parsedData['stats']['valid_rows'] . " Zeilen erkannt");
        
    } catch (Exception $e) {
        $result['message'] = "❌ Fehler: " . $e->getMessage();
        error_log("DEBUG: Exception: " . $e->getMessage());
    }
    
    return $result;
}

function createBuchungenTableIfNotExists($pdo, $bankType) {
    $year = date('Y');
    // $tableName = "umsatz_" . substr($bankType, 0, 2) . $year;
    $tableName = "umsatz_sp2025_test";  // TEMPORÄR FEST

    error_log("DEBUG: Erstelle/Prüfe Tabelle: $tableName");
    
    $sql = "CREATE TABLE IF NOT EXISTS $tableName (
        id INT AUTO_INCREMENT PRIMARY KEY,
        buchungsdatum DATE NOT NULL,
        valuta DATE,
        buchungstext VARCHAR(255),
        verwendungszweck TEXT,
        betrag DECIMAL(10,2) NOT NULL,
        umsatzart VARCHAR(100),
        banktyp VARCHAR(20),
        import_datum TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        kategorie VARCHAR(50),
        INDEX idx_datum (buchungsdatum),
        INDEX idx_betrag (betrag)
    )";
    
    $pdo->exec($sql);
    error_log("DEBUG: Tabelle $tableName erstellt/geprüft");
    return $tableName;
}

function importBuchung($row, $pdo, $tableName, $bankType) {
    // DEFAULT-WERTE falls NULL
    $buchungsdatum = $row['buchungsdatum'] ?? date('Y-m-d'); // Heute als Fallback
    $betrag = $row['betrag'] ?? 0.01; // Kleiner Betrag als Fallback
    
    $sql = "INSERT INTO $tableName 
            (buchungsdatum, valuta, buchungstext, verwendungszweck, betrag, umsatzart, banktyp) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $buchungsdatum,
        $row['valuta'] ?? $buchungsdatum, // Valuta = Buchungsdatum falls nicht vorhanden
        $row['buchungstext'] ?? 'Unbekannt',
        $row['verwendungszweck'] ?? '',
        $betrag,
        $row['umsatzart'] ?? '',
        $bankType
    ]);
}
// function importBuchung($row, $pdo, $tableName, $bankType) {
//     $sql = "INSERT INTO $tableName 
//             (buchungsdatum, valuta, buchungstext, verwendungszweck, betrag, umsatzart, banktyp) 
//             VALUES (?, ?, ?, ?, ?, ?, ?)";
    
//     $stmt = $pdo->prepare($sql);
//     $success = $stmt->execute([
//         $row['buchungsdatum'] ?? null,
//         $row['valuta'] ?? null,
//         $row['buchungstext'] ?? '',
//         $row['verwendungszweck'] ?? '',
//         $row['betrag'] ?? 0,
//         $row['umsatzart'] ?? '',
//         $bankType
//     ]);
    
//     if (!$success) {
//         error_log("DEBUG: Fehler beim Import: " . implode(', ', $stmt->errorInfo()));
//     }
    
//     return $success;
// }

function importToDatabase($data, $pdo, $bankType) {
    $result = ['success' => false, 'message' => '', 'imported_rows' => 0, 'table_name' => ''];
    echo "DEBUG: Import start - $imported Zeilen importiert, $skipped übersprungen<br>";
    try {
        $tableName = createBuchungenTableIfNotExists($pdo, $bankType);
        $imported = 0;
        $errors = [];
        $skipped = 0;
        
        foreach ($data as $index => $row) {
            // DATEN VALIDIEREN vor dem Import
            if (!isValidRow($row)) {
                // $skipped++;
                // $errors[] = "Zeile $index ungültig (fehlendes Datum/Betrag)";
                // continue;
            }
            
            if (importBuchung($row, $pdo, $tableName, $bankType)) {
                $imported++;
            } else {
                $errors[] = "Zeile $index: " . ($row['buchungstext'] ?? 'Unbekannt');
            }
        }
        echo "DEBUG: Import abgeschlossen - $imported Zeilen importiert, $skipped übersprungen<br>";
        $result['success'] = true;
        $result['imported_rows'] = $imported;
        $result['table_name'] = $tableName;
        $result['message'] = "✅ $imported Buchungen in <strong>$tableName</strong> importiert";
        
        if ($skipped > 0) {
            $result['message'] .= "<br>⚠️ $skipped Zeilen übersprungen (ungültige Daten)";
        }
        
        if (!empty($errors)) {
            $result['message'] .= "<br>❌ " . count($errors) . " Fehler";
        }
        
    } catch (Exception $e) {
        $result['message'] = "❌ Import-Fehler: " . $e->getMessage();
    }
    
    return $result;
}

// NEUE FUNKTION hinzufügen - vor importToDatabase()
function isValidRow($row) {
    // Prüfe ob Pflichtfelder vorhanden sind
    if (empty($row['buchungsdatum']) || $row['buchungsdatum'] === 'NULL') {
        return false;
    }
    
    if (!isset($row['betrag']) || $row['betrag'] === 0) {
        return false;
    }
    
    return true;
}
// function importToDatabase($data, $pdo, $bankType) {
//     $result = ['success' => false, 'message' => '', 'imported_rows' => 0, 'table_name' => ''];
    
//     error_log("DEBUG: importToDatabase gestartet - Daten: " . count($data) . " Zeilen");
    
//     try {
//         $tableName = createBuchungenTableIfNotExists($pdo, $bankType);
//         $imported = 0;
//         $errors = [];
        
//         foreach ($data as $index => $row) {
//             if (importBuchung($row, $pdo, $tableName, $bankType)) {
//                 $imported++;
//             } else {
//                 $errors[] = "Zeile $index: " . ($row['buchungstext'] ?? 'Unbekannt');
//             }
//         }
        
//         $result['success'] = true;
//         $result['imported_rows'] = $imported;
//         $result['table_name'] = $tableName;
//         $result['message'] = "✅ $imported Buchungen in <strong>$tableName</strong> importiert";
        
//         error_log("DEBUG: Import abgeschlossen - $imported Zeilen importiert");
        
//         if (!empty($errors)) {
//             $result['message'] .= "<br>⚠️ " . count($errors) . " Fehler";
//             error_log("DEBUG: Import-Fehler: " . implode(', ', array_slice($errors, 0, 3)));
//         }
        
//     } catch (Exception $e) {
//         $result['message'] = "❌ Import-Fehler: " . $e->getMessage();
//         error_log("DEBUG: Import Exception: " . $e->getMessage());
//     }
    
//     return $result;
// }

function processUpload() {
    error_log("DEBUG: processUpload aufgerufen - POST: " . print_r($_POST, true));
    
    if (isset($_POST['upload_file']) && isset($_FILES['bank_file'])) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $pdo = getDatabaseConnection();
        $uploadResult = handleFileUpload($_FILES['bank_file'], $_POST['bank_type'], $pdo);
        
        $_SESSION['upload_result'] = $uploadResult;
        $_SESSION['last_bank_type'] = $_POST['bank_type'];
        
        error_log("DEBUG: Upload verarbeitet - Redirect zu import");
        
        header("Location: ?module=import");
        exit;
    }
}

// function processImport() {
//     if (isset($_POST['action']) && $_POST['action'] === 'import_data') {
//         if (session_status() === PHP_SESSION_NONE) {
//             session_start();
//         }
        
//         // BankType aus POST oder Session holen
//         $bankType = $_POST['bank_type'] ?? $_SESSION['last_bank_type'] ?? '';
//         $uploadResult = $_SESSION['upload_result'] ?? null;
        
//         error_log("DEBUG: Import gestartet - Bank: $bankType, Daten: " . ($uploadResult ? count($uploadResult['data']) : '0'));
        
//         if ($uploadResult && $uploadResult['success'] && !empty($uploadResult['data']) && $bankType) {
//             $pdo = getDatabaseConnection();
//             $importResult = importToDatabase($uploadResult['data'], $pdo, $bankType);
            
//             // Ergebnis speichern
//             $_SESSION['import_result'] = $importResult;
            
//             // Alte Upload-Daten löschen
//             unset($_SESSION['upload_result']);
//             unset($_SESSION['last_bank_type']);
            
//             header("Location: ?module=import");
//             exit;
//         } else {
//             // Fehlermeldung falls was fehlt
//             $_SESSION['import_result'] = [
//                 'success' => false,
//                 'message' => '❌ Fehler: Keine Daten zum Importieren gefunden'
//             ];
//             header("Location: ?module=import");
//             exit;
//         }
//     }
// }
// function processImport() {
//     error_log("=== PROCESS IMPORT WIRD AUFGERUFEN ===");
    
//     if (isset($_POST['action']) && $_POST['action'] === 'import_data') {
//         error_log("=== IMPORT ACTION ERKANNT ===");
//         session_start();
//         $_SESSION['import_result'] = [
//             'success' => true, 
//             'message' => '✅ TEST: Import funktioniert!',
//             'imported_rows' => 5
//         ];
//         header("Location: ?module=import");
//         exit;
//     }
// }
// // function processImport() {
//     if (isset($_POST['action']) && $_POST['action'] === 'import_data') {
//         if (session_status() === PHP_SESSION_NONE) {
//             session_start();
//         }
        
//         $uploadResult = $_SESSION['upload_result'] ?? null;
//         $bankType = $_POST['bank_type'] ?? $_SESSION['last_bank_type'] ?? '';
        
//         error_log("DEBUG: Import - UploadResult vorhanden: " . ($uploadResult ? 'ja' : 'nein'));
//         error_log("DEBUG: Import - BankType: $bankType");
        
//         if ($uploadResult && $uploadResult['success'] && !empty($uploadResult['data']) && $bankType) {
//             error_log("DEBUG: Starte DB-Import mit " . count($uploadResult['data']) . " Zeilen");
            
//             $pdo = getDatabaseConnection();
//             $importResult = importToDatabase($uploadResult['data'], $pdo, $bankType);
            
//             $_SESSION['import_result'] = $importResult;
//             unset($_SESSION['upload_result']);
//             unset($_SESSION['last_bank_type']);
            
//             error_log("DEBUG: Import abgeschlossen - Redirect");
            
//             header("Location: ?module=import");
//             exit;
//         } else {
//             error_log("DEBUG: Import-Bedingungen nicht erfüllt");
//         }
//     }
// }
// function processImport() {
//     if (isset($_POST['action']) && $_POST['action'] === 'import_data') {
    
//     if (isset($_POST['import_data'])) {
//         if (session_status() === PHP_SESSION_NONE) {
//             session_start();
//         }
        
//         $uploadResult = $_SESSION['upload_result'] ?? null;
//         $bankType = $_SESSION['last_bank_type'] ?? '';
        
//         error_log("DEBUG: Import - UploadResult vorhanden: " . ($uploadResult ? 'ja' : 'nein'));
//         error_log("DEBUG: Import - BankType: $bankType");
        
//         if ($uploadResult && $uploadResult['success'] && !empty($uploadResult['data']) && $bankType) {
//             error_log("DEBUG: Starte DB-Import mit " . count($uploadResult['data']) . " Zeilen");
            
//             $pdo = getDatabaseConnection();
//             $importResult = importToDatabase($uploadResult['data'], $pdo, $bankType);
            
//             $_SESSION['import_result'] = $importResult;
//             unset($_SESSION['upload_result']);
//             unset($_SESSION['last_bank_type']);
            
//             error_log("DEBUG: Import abgeschlossen - Redirect");
            
//             header("Location: ?module=import");
//             exit;
//         } else {
//             error_log("DEBUG: Import-Bedingungen nicht erfüllt");
//         }
//     }
// }

// ... die getter-Funktionen bleiben gleich ...
function getUploadResult() {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    return $_SESSION['upload_result'] ?? null;
}

function getImportResult() {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    $result = $_SESSION['import_result'] ?? null;
    if ($result) { unset($_SESSION['import_result']); }
    return $result;
}

function getLastBankType() {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    return $_SESSION['last_bank_type'] ?? '';
}

function clearUploadSession() {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    unset($_SESSION['upload_result']);
    unset($_SESSION['last_bank_type']);
}
?>