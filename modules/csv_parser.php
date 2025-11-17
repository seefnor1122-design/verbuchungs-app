<?php
// modules/csv_parser.php
// NEUE FUNKTION in csv_parser.php hinzufügen
function handleFileUpload($uploadedFile, $bankType, $pdo) {
    $result = ['success' => false, 'message' => '', 'data' => [], 'stats' => []];
    
    // Validierung
    if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
        $result['message'] = "❌ Upload-Fehler: " . $uploadedFile['error'];
        return $result;
    }
    
    if ($uploadedFile['size'] > 5 * 1024 * 1024) {
        $result['message'] = "❌ Datei zu groß (max. 5MB)";
        return $result;
    }
    
    $allowedExtensions = ['csv', 'txt'];
    $fileName = $uploadedFile['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if (!in_array($fileExtension, $allowedExtensions)) {
        $result['message'] = "❌ Ungültiger Dateityp: .{$fileExtension} - nur .csv und .txt erlaubt";
        return $result;
    }
    
    try {
        // Je nach Banktyp parsen
        if ($bankType === 'sparkasse') {
            $parsedData = parseSparkasseCSV($uploadedFile['tmp_name']);
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
        
    } catch (Exception $e) {
        $result['message'] = "❌ Fehler: " . $e->getMessage();
    }
    
    return $result;
}

// Import-Funktion hinzufügen
function importDataToDatabase($data, $pdo, $bankType) {
    $imported = 0;
    
    // echo "DEBUG: importDataToDatabase gestartet csv_parser--><br>";
    // echo "DEBUG: Daten count: " . count($data) . " --><br>";
    // echo "DEBUG: BankType: $bankType --><br>";
    
    try {
        $year = date('Y');
        $tableName = "umsatz_" . substr($bankType, 0, 2) . $year;
        // echo "DEBUG: TableName: $tableName --><br>";
        
        // TABELLE ERSTELLEN
        $sql = "CREATE TABLE IF NOT EXISTS $tableName (
            id INT AUTO_INCREMENT PRIMARY KEY,
            buchungsdatum VARCHAR(20),
            valuta VARCHAR(20), 
            buchungstext VARCHAR(255),
            verwendungszweck TEXT,
            betrag DECIMAL(10,2) NOT NULL,
            umsatzart VARCHAR(100),
            banktyp VARCHAR(20),
            import_datum TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            kategorie VARCHAR(50),
            rohdatum_buchung VARCHAR(20),
            rohdatum_valuta VARCHAR(20)
        )";
        
        $pdo->exec($sql);
        
        
        // DATEN IMPORTIEREN
        // echo "DEBUG: Starte Import mit " . count($data) . " Zeilen --><br>";
        
        foreach ($data as $index => $row) {
            // echo "DEBUG: Verarbeite Zeile $index --><br>";
            
            $sql = "INSERT INTO $tableName 
                    (buchungsdatum, valuta, buchungstext, verwendungszweck, betrag, umsatzart, banktyp) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute([
                $row['buchungsdatum'] ?? '',
                $row['valuta'] ?? '',
                $row['buchungstext'] ?? 'Unbekannt',
                $row['verwendungszweck'] ?? '',
                $row['betrag'] ?? 0.01,
                $row['umsatzart'] ?? '',
                $bankType,
                // $row['rohdatum_buchung'] ?? '',
                // $row['rohdatum_valuta'] ?? ''
            ]);
            
            if ($success) {
                $imported++;
            } else {
                // echo "DEBUG: FEHLER in Zeile $index: " . implode(', ', $stmt->errorInfo()) . " -->";
            }
            
            // Nur erste 3 Zeilen debuggen
            if ($index < 3) {
                // echo "DEBUG Row $index: " . print_r($row, true) . " -->";
            }
        }
        
        // echo "DEBUG: Import abgeschlossen - $imported Zeilen importiert -->";
        
    } catch (Exception $e) {
       
    }
    
    return $imported;
}
// function importDataToDatabase($data, $pdo, $bankType) {
//     $imported = 0;
    
//     try {
//         $year = date('Y');
//         $tableName = "umsatz_" . substr($bankType, 0, 2) . $year;
//          echo "tbleName: $tableName\n"; // DEBUG
//         // TABELLE MIT VARCHAR FÜR DATUM ERSTELLEN
//         $sql = "CREATE TABLE IF NOT EXISTS $tableName (
//             id INT AUTO_INCREMENT PRIMARY KEY,
//             buchungsdatum VARCHAR(20),        // 🎯 VARCHAR statt DATE
//             valuta VARCHAR(20),               // 🎯 VARCHAR statt DATE
//             buchungstext VARCHAR(255),
//             verwendungszweck TEXT,
//             betrag DECIMAL(10,2) NOT NULL,
//             umsatzart VARCHAR(100),
//             banktyp VARCHAR(20),
//             import_datum TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//             kategorie VARCHAR(50),
//             rohdatum_buchung VARCHAR(20),     // 🎯 NEU: Original-Datum
//             rohdatum_valuta VARCHAR(20)       // 🎯 NEU: Original-Datum
//         )";
//         $pdo->exec($sql);
        
//         // Daten importieren - OHNE Konvertierung
//         foreach ($data as $row) {
//             echo "fore tbleName: $tableName\n"; // DEBUG
//             $sql = "INSERT INTO $tableName 
//                     (buchungsdatum, valuta, buchungstext, verwendungszweck, betrag, umsatzart, banktyp, rohdatum_buchung, rohdatum_valuta) 
//                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
//             $stmt = $pdo->prepare($sql);
//             $success = $stmt->execute([
//                 $row['buchungsdatum'] ?? '',           // Kann SQL-Datum oder deutsches Format sein
//                 $row['valuta'] ?? '',                  // Kann SQL-Datum oder deutsches Format sein
//                 $row['buchungstext'] ?? 'Unbekannt',
//                 $row['verwendungszweck'] ?? '',
//                 $row['betrag'] ?? 0.01,
//                 $row['umsatzart'] ?? '',
//                 $bankType,
//                 $row['rohdatum_buchung'] ?? '',        // 🎯 Original "01.01.2024"
//                 $row['rohdatum_valuta'] ?? ''          // 🎯 Original "02.01.2024"
//             ]);
            
//             if ($success) {
//                 $imported++;
//             }
//         }
        
//     } catch (Exception $e) {
//         error_log("Import Error: " . $e->getMessage());
//     }
    
//     return $imported;
// }
// function importDataToDatabase($data, $pdo, $bankType) {
//     $imported = 0;
    
//     try {
//         // Tabellenname nach deinem Schema
//         $year = date('Y');
//         $tableName = "umsatz_" . substr($bankType, 0, 2) . $year;
        
//         // Tabelle erstellen falls nicht vorhanden
//         $sql = "CREATE TABLE IF NOT EXISTS $tableName (
//             id INT AUTO_INCREMENT PRIMARY KEY,
//             buchungsdatum DATE NOT NULL,
//             valuta DATE,
//             buchungstext VARCHAR(255),
//             verwendungszweck TEXT,
//             betrag DECIMAL(10,2) NOT NULL,
//             umsatzart VARCHAR(100),
//             banktyp VARCHAR(20),
//             import_datum TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//             kategorie VARCHAR(50)
//         )";
//         $pdo->exec($sql);
        
//         // Daten importieren
//         foreach ($data as $row) {
//             // Default-Werte falls NULL
//             $buchungsdatum = $row['buchungsdatum'] ?? date('Y-m-d');
//             $betrag = $row['betrag'] ?? 0.01;
            
//             $sql = "INSERT INTO $tableName 
//                     (buchungsdatum, valuta, buchungstext, verwendungszweck, betrag, umsatzart, banktyp) 
//                     VALUES (?, ?, ?, ?, ?, ?, ?)";
            
//             $stmt = $pdo->prepare($sql);
//             $success = $stmt->execute([
//                 $buchungsdatum,
//                 $row['valuta'] ?? $buchungsdatum,
//                 $row['buchungstext'] ?? 'Unbekannt',
//                 $row['verwendungszweck'] ?? '',
//                 $betrag,
//                 $row['umsatzart'] ?? '',
//                 $bankType
//             ]);
            
//             if ($success) {
//                 $imported++;
//             }
//         }
        
//     } catch (Exception $e) {
//         error_log("Import Error: " . $e->getMessage());
//     }
    
//     return $imported;
// }

function parseSparkasseCSV($filePath) {
    $result = [
        'success' => false,
        'data' => [],
        'errors' => [],
        'stats' => []
    ];
    
    try {
        // Encoding erkennen und konvertieren
        $content = file_get_contents($filePath);
        $encoding = mb_detect_encoding($content, ['UTF-8', 'Windows-1252', 'ISO-8859-1'], true);
        
        if ($encoding != 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }
        
        // Temporäre UTF-8 Datei erstellen
        $tempFile = tempnam(sys_get_temp_dir(), 'spk_utf8_');
        file_put_contents($tempFile, $content);
        
        // CSV einlesen
        $handle = fopen($tempFile, 'r');
        if (!$handle) {
            throw new Exception("Datei konnte nicht geöffnet werden");
        }
        
        // Header-Zeile lesen und ANZEIGEN
        $header = fgetcsv($handle, 0, ';');
        
        // 🚨 DEBUG: Header in HTML anzeigen
        echo '<div style="background: #ffeb3b; color: #333; padding: 10px; margin: 10px 0; border-radius: 4px;">';
        echo '<strong>🔍 DEBUG - Gefundene Spalten:</strong><br>';
        foreach ($header as $index => $column) {
            echo "Spalte $index: <strong>" . htmlspecialchars($column) . "</strong><br>";
        }
        echo '</div>';
        
        $result['stats']['header'] = $header;
        
        // Daten verarbeiten
        $rowCount = 0;
        $validRows = 0;
        
        while (($row = fgetcsv($handle, 0, ';')) !== FALSE) {
            $rowCount++;
            
            // Leere Zeilen überspringen
            if (count($row) < 3 || empty(trim(implode('', $row)))) {
                continue;
            }
            
            // 🚨 DEBUG: Erste Zeile anzeigen
            if ($rowCount === 1) {
                echo '<div style="background: #e3f2fd; color: #333; padding: 10px; margin: 10px 0; border-radius: 4px;">';
                echo '<strong>🔍 DEBUG - Erste Datenzeile:</strong><br>';
                foreach ($row as $index => $value) {
                    echo "Spalte $index: <strong>" . htmlspecialchars($value) . "</strong><br>";
                }
                echo '</div>';
            }
            
            // Sparkasse-spezifische Verarbeitung
            $processedRow = processSparkasseRow($row, $header);
            
            if ($processedRow) {
                $result['data'][] = $processedRow;
                $validRows++;
            }
        }
        
        fclose($handle);
        unlink($tempFile);
        
        $result['success'] = true;
        $result['stats']['total_rows'] = $rowCount;
        $result['stats']['valid_rows'] = $validRows;
        $result['stats']['encoding'] = $encoding;
        
    } catch (Exception $e) {
        $result['errors'][] = $e->getMessage();
    }
    
    return $result;
}
// function parseSparkasseCSV($filePath) {
//     $result = [
//         'success' => false,
//         'data' => [],
//         'errors' => [],
//         'stats' => []
//     ];
    
//     try {
//         // Encoding erkennen und konvertieren
//         $content = file_get_contents($filePath);
//         $encoding = mb_detect_encoding($content, ['UTF-8', 'Windows-1252', 'ISO-8859-1'], true);
        
//         if ($encoding != 'UTF-8') {
//             $content = mb_convert_encoding($content, 'UTF-8', $encoding);
//         }
        
//         // Temporäre UTF-8 Datei erstellen
//         $tempFile = tempnam(sys_get_temp_dir(), 'spk_utf8_');
//         file_put_contents($tempFile, $content);
        
//         // CSV einlesen
//         $handle = fopen($tempFile, 'r');
//         if (!$handle) {
//             throw new Exception("Datei konnte nicht geöffnet werden");
//         }
        
//         // Header-Zeile lesen
//         $header = fgetcsv($handle, 0, ';');
//         $result['stats']['header'] = $header;
        
//         // Daten verarbeiten
//         $rowCount = 0;
//         $validRows = 0;
        
//         while (($row = fgetcsv($handle, 0, ';')) !== FALSE) {
//             $rowCount++;
            
//             // Leere Zeilen überspringen
//             if (count($row) < 3 || empty(trim(implode('', $row)))) {
//                 continue;
//             }
            
//             // Sparkasse-spezifische Verarbeitung
//             $processedRow = processSparkasseRow($row, $header);
            
//             if ($processedRow) {
//                 $result['data'][] = $processedRow;
//                 $validRows++;
//             }
//         }
        
//         fclose($handle);
//         unlink($tempFile); // Temporäre Datei löschen
        
//         $result['success'] = true;
//         $result['stats']['total_rows'] = $rowCount;
//         $result['stats']['valid_rows'] = $validRows;
//         $result['stats']['encoding'] = $encoding;
        
//     } catch (Exception $e) {
//         $result['errors'][] = $e->getMessage();
//     }
    
//     return $result;
// }
function processSparkasseRow($row, $header) {
    $processed = [
        'buchungsdatum' => '',
        'valuta' => '',  
        'buchungstext' => '',
        'verwendungszweck' => '',
        'betrag' => 0,
        'umsatzart' => '',
        'rohdatum_buchung' => '',
        'rohdatum_valuta' => ''
    ];
    
    // DEBUG: Erste Zeile anzeigen
    static $debugged = false;
    if (!$debugged) {
        echo "<!-- DEBUG processSparkasseRow -->";
        echo "<!-- Header: " . htmlspecialchars(implode(' | ', $header)) . " -->";
        echo "<!-- Row: " . htmlspecialchars(implode(' | ', $row)) . " -->";
        $debugged = true;
    }
    
    foreach ($header as $index => $columnName) {
        if (!isset($row[$index])) continue;
        
        $value = trim($row[$index]);
        $columnLower = strtolower($columnName);
        
        if (strpos($columnLower, 'buchungstag') !== false) {
            $processed['rohdatum_buchung'] = $value;
            $converted = parseGermanDate($value);
            $processed['buchungsdatum'] = $converted;
            
            // DEBUG: Konvertierung anzeigen
            if (!$debugged) {
                echo "<!-- Buchungstag: '$value' → '$converted' -->";
            }
        }
        elseif (strpos($columnLower, 'valuta') !== false) {
            $processed['rohdatum_valuta'] = $value;
            $converted = parseGermanDate($value);
            $processed['valuta'] = $converted;
            
            // DEBUG: Konvertierung anzeigen
            if (!$debugged) {
                echo "<!-- Valuta: '$value' → '$converted' -->";
            }
        }
        elseif (strpos($columnLower, 'betrag') !== false) {
            $processed['betrag'] = parseGermanAmount($value);
        }
        elseif (strpos($columnLower, 'verwendungszweck') !== false) {
            $processed['verwendungszweck'] = $value;
        }
        elseif (strpos($columnLower, 'buchungstext') !== false || 
                strpos($columnLower, 'umsatzart') !== false) {
            $processed['umsatzart'] = $value;
        }
        elseif (strpos($columnLower, 'auftraggeber') !== false || 
                strpos($columnLower, 'empfänger') !== false ||
                strpos($columnLower, 'name') !== false) {
            if (!empty($value)) {
                $processed['buchungstext'] .= (empty($processed['buchungstext']) ? '' : ' - ') . $value;
            }
        }
    }
    
    // Fallback: Buchungstext aus Verwendungszweck
    if (empty($processed['buchungstext']) && !empty($processed['verwendungszweck'])) {
        $processed['buchungstext'] = substr($processed['verwendungszweck'], 0, 100);
    }
    
    return $processed;
}
// function processSparkasseRow($row, $header) {
//     $processed = [
//         'buchungsdatum' => '',      // 🎯 Leer statt null
//         'valuta' => '',             // 🎯 Leer statt null  
//         'buchungstext' => '',
//         'verwendungszweck' => '',
//         'betrag' => 0,
//         'umsatzart' => '',
//         'rohdatum_buchung' => '',   // 🎯 NEU: Original "01.01.2024"
//         'rohdatum_valuta' => ''     // 🎯 NEU: Original "02.01.2024"
//     ];
    
//     foreach ($header as $index => $columnName) {
//         if (!isset($row[$index])) continue;
        
//         $value = trim($row[$index]);
//         $columnLower = strtolower($columnName);
        
//         if (strpos($columnLower, 'buchungstag') !== false) {
//             $processed['rohdatum_buchung'] = $value;  // 🎯 Original speichern
//             $processed['buchungsdatum'] = parseGermanDate($value); // 🎯 Versuch zu konvertieren
//         }
//         elseif (strpos($columnLower, 'valuta') !== false) {
//             $processed['rohdatum_valuta'] = $value;   // 🎯 Original speichern  
//             $processed['valuta'] = parseGermanDate($value); // 🎯 Versuch zu konvertieren
//         }
//         elseif (strpos($columnLower, 'betrag') !== false) {
//             $processed['betrag'] = parseGermanAmount($value);
//         }
//         // ... restliches Mapping
//     }
    
//     return $processed;
// }
// function processSparkasseRow($row, $header) {
//     $processed = [
//         'buchungsdatum' => null,
//         'valuta' => null, 
//         'buchungstext' => '',
//         'verwendungszweck' => '',
//         'betrag' => 0,
//         'umsatzart' => ''
//     ];
    
//     // Sparkasse-spezifisches Mapping
//     foreach ($header as $index => $columnName) {
//         if (!isset($row[$index])) continue;
        
//         $value = trim($row[$index]);
//         $columnLower = strtolower($columnName);
        
//         // FLEXIBLES MAPPING für verschiedene Spaltennamen
//         if (strpos($columnLower, 'buchungstag') !== false || 
//             strpos($columnLower, 'buchungsdatum') !== false) {
//             // 🎯 HAUPTSDATUM: Buchungstag
//             $processed['buchungsdatum'] = parseGermanDate($value);
//         }
//         elseif (strpos($columnLower, 'valuta') !== false || 
//                 strpos($columnLower, 'wertstellung') !== false) {
//             // 🎯 VALUTA: Zweites Datum
//             $processed['valuta'] = parseGermanDate($value);
//         }
//         elseif (strpos($columnLower, 'betrag') !== false) {
//             $processed['betrag'] = parseGermanAmount($value);
//         }
//         elseif (strpos($columnLower, 'verwendungszweck') !== false) {
//             $processed['verwendungszweck'] = $value;
//         }
//         elseif (strpos($columnLower, 'buchungstext') !== false || 
//                 strpos($columnLower, 'umsatzart') !== false) {
//             $processed['umsatzart'] = $value;
//         }
//         elseif (strpos($columnLower, 'auftraggeber') !== false || 
//                 strpos($columnLower, 'empfänger') !== false ||
//                 strpos($columnLower, 'name') !== false) {
//             if (!empty($value)) {
//                 $processed['buchungstext'] .= (empty($processed['buchungstext']) ? '' : ' - ') . $value;
//             }
//         }
//     }
    
//     // 🎯 FALLBACK-LOGIK:
//     // Wenn Buchungsdatum fehlt, nimm Valuta
//     if (!$processed['buchungsdatum'] && $processed['valuta']) {
//         $processed['buchungsdatum'] = $processed['valuta'];
//     }
    
//     // Wenn Valuta fehlt, nimm Buchungsdatum
//     if (!$processed['valuta'] && $processed['buchungsdatum']) {
//         $processed['valuta'] = $processed['buchungsdatum'];
//     }
    
//     // Fallback: Buchungstext aus Verwendungszweck
//     if (empty($processed['buchungstext']) && !empty($processed['verwendungszweck'])) {
//         $processed['buchungstext'] = substr($processed['verwendungszweck'], 0, 100);
//     }
    
//     return $processed;
// }
function parseGermanDate($date) {
    // Leer oder null abfangen
    if (empty($date)) {
        return null;
    }

    // Erwartetes Format: 25.01.25 oder 25.01.2025
    $dt = DateTime::createFromFormat('d.m.y', $date);

    // Falls 4-stelliges Jahr vorhanden ist
    if (!$dt) {
        $dt = DateTime::createFromFormat('d.m.Y', $date);
    }

    // Falls es immer noch kein valides Datum ist → Fehler zurückgeben
    if (!$dt) {
        return null;
    }

    // SQL-Format
    return $dt->format('Y-m-d');
}

// function parseGermanDate($dateString) {
//     // Deutsche Datumsformate: TT.MM.JJJJ
//     if (preg_match('/(\d{1,2})\.(\d{1,2})\.(\d{4})/', $dateString, $matches)) {
//         $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
//         $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
//         $year = $matches[3];
//         return "$year-$month-$day";
//     }
    
//     // Falls schon SQL-Format: YYYY-MM-DD
//     if (preg_match('/\d{4}-\d{2}-\d{2}/', $dateString)) {
//         return $dateString;
//     }
    
//     return null;
// }
// function parseGermanDate($dateString) {
//     // Deutsche Datumsformate: TT.MM.JJJJ
//     if (preg_match('/(\d{1,2})\.(\d{1,2})\.(\d{4})/', $dateString, $matches)) {
//         return $matches[3] . '-' . str_pad($matches[2], 2, '0', STR_PAD_LEFT) . '-' . str_pad($matches[1], 2, '0', STR_PAD_LEFT);
//     }
//     return null;
// }

function parseGermanAmount($amountString) {
    // Deutsche Beträge: 1.234,56 oder -1.234,56
    $clean = str_replace(['.', ','], ['', '.'], $amountString);
    return floatval($clean);
}
// function processSparkasseRow($row, $header) {
//     // DEBUG: Erste Zeile loggen
//     static $firstRow = true;
//     if ($firstRow) {
//         error_log("DEBUG CSV Header: " . print_r($header, true));
//         error_log("DEBUG CSV First Row: " . print_r($row, true));
//         $firstRow = false;
//     }
    
//     $processed = [
//         'buchungsdatum' => null,
//         'valuta' => null, 
//         'buchungstext' => '',
//         'verwendungszweck' => '',
//         'betrag' => 0,
//         'umsatzart' => ''
//     ];
    
//     // Sparkasse-spezifisches Mapping
//     foreach ($header as $index => $columnName) {
//         if (!isset($row[$index])) continue;
        
//         $value = trim($row[$index]);
//         $columnLower = strtolower($columnName);
        
//         error_log("DEBUG Column: $columnLower = '$value'");
        
//         // FLEXIBLERES MAPPING für verschiedene Sparkassen-Formate
//         if (strpos($columnLower, 'buchung') !== false && strpos($columnLower, 'tag') !== false) {
//             $processed['buchungsdatum'] = parseGermanDate($value);
//         }
//         elseif (strpos($columnLower, 'valuta') !== false || strpos($columnLower, 'wertstellung') !== false) {
//             $processed['valuta'] = parseGermanDate($value);
//         }
//         elseif (strpos($columnLower, 'betrag') !== false) {
//             $processed['betrag'] = parseGermanAmount($value);
//         }
//         elseif (strpos($columnLower, 'verwendungszweck') !== false) {
//             $processed['verwendungszweck'] = $value;
//         }
//         elseif (strpos($columnLower, 'buchungstext') !== false || strpos($columnLower, 'umsatzart') !== false) {
//             $processed['umsatzart'] = $value;
//         }
//         elseif (strpos($columnLower, 'auftraggeber') !== false || strpos($columnLower, 'empfänger') !== false) {
//             if (!empty($value)) {
//                 $processed['buchungstext'] .= (empty($processed['buchungstext']) ? '' : ' - ') . $value;
//             }
//         }
//     }
    
//     // Fallback: Buchungstext aus Verwendungszweck
//     if (empty($processed['buchungstext']) && !empty($processed['verwendungszweck'])) {
//         $processed['buchungstext'] = substr($processed['verwendungszweck'], 0, 100);
//     }
    
//     // Fallback: Valuta = Buchungsdatum
//     if (!$processed['valuta'] && $processed['buchungsdatum']) {
//         $processed['valuta'] = $processed['buchungsdatum'];
//     }
    
//     error_log("DEBUG Processed: " . print_r($processed, true));
    
//     return $processed;
// }
// function processSparkasseRow($row, $header) {
//     // Sparkasse-spezifische Verarbeitung
//     $processed = [
//         'buchungsdatum' => null,
//         'valuta' => null,
//         'buchungstext' => '',
//         'verwendungszweck' => '',
//         'betrag' => 0,
//         'umsatzart' => ''
//     ];
    
//     // Mapping basierend auf typischer Sparkassen-Struktur
//     foreach ($header as $index => $columnName) {
//         if (!isset($row[$index])) continue;
        
//         $value = trim($row[$index]);
        
//         switch (strtolower($columnName)) {
//             case 'buchungstag':
//             case 'buchungsdatum':
//                 $processed['buchungsdatum'] = parseGermanDate($value);
//                 break;
                
//             case 'valuta':
//             case 'wertstellung':
//                 $processed['valuta'] = parseGermanDate($value);
//                 break;
                
//             case 'buchungstext':
//             case 'umsatzart':
//                 $processed['umsatzart'] = $value;
//                 break;
                
//             case 'verwendungszweck':
//                 $processed['verwendungszweck'] = $value;
//                 break;
                
//             case 'betrag':
//                 $processed['betrag'] = parseGermanAmount($value);
//                 break;
                
//             case 'auftraggeber':
//             case 'empfänger':
//                 if (!empty($value)) {
//                     $processed['buchungstext'] .= (empty($processed['buchungstext']) ? '' : ' - ') . $value;
//                 }
//                 break;
//         }
//     }
    
//     // Buchungstext aus Verwendungszweck falls leer
//     if (empty($processed['buchungstext']) && !empty($processed['verwendungszweck'])) {
//         $processed['buchungstext'] = substr($processed['verwendungszweck'], 0, 100);
//     }
    
//     // Valuta = Buchungsdatum falls nicht vorhanden
//     if (!$processed['valuta'] && $processed['buchungsdatum']) {
//         $processed['valuta'] = $processed['buchungsdatum'];
//     }
    
//     return $processed;
// }

?>