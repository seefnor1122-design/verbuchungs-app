<?php
/**
 * Sidebar-Modul - Verwaltet die Sidebar-Logik
 * Kein HTML, nur Daten und Logik!
 */

/**
 * Holt alle verfügbaren Tabellen aus der Datenbank
 */

/**
 * Holt alle verfügbaren Tabellen aus der Datenbank
 */
function getAvailableTables($pdo) {
    try {
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // System-Tabellen filtern
        return array_filter($tables, function($table) {
            $systemTables = ['table_configs', 'kontenliste', 'users', 'sessions', 'migrations'];
            return !in_array($table, $systemTables);
        });
    } catch (Exception $e) {
        // 🎯 FÜR DEBUGGING: Fehler anzeigen
        echo '<div class="error" style="margin: 10px; padding: 10px; background: #f8d7da; color: #721c24; border-radius: 4px;">';
        echo '<strong>❌ Datenbank-Fehler:</strong> ' . htmlspecialchars($e->getMessage());
        echo '</div>';
        
        // Trotzdem im Log speichern
        error_log("Fehler beim Tabellen abrufen: " . $e->getMessage());
        return [];
    }
}
// function getAvailableTables($pdo) {
//     try {
//         $stmt = $pdo->query("SHOW TABLES");
//         $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
//         // System-Tabellen filtern
//         return array_filter($tables, function($table) {
//             $systemTables = ['table_configs', 'kontenliste', 'users', 'sessions', 'migrations'];
//             return !in_array($table, $systemTables);
//         });
//     } catch (Exception $e) {
//         // Fehler logging - aber keine Ausgabe
//         error_log("Fehler beim Tabellen abrufen: " . $e->getMessage());
//         return [];
//     }
// }

// /**
//  * Generiert die Sidebar-Daten für das Template
//  */
// function getSidebarData($pdo, $currentSelection = []) {
//     return [
//         'tables' => getAvailableTables($pdo),
//         'current_quell_tabelle' => $currentSelection['quell_tabelle'] ?? '',
//         'current_ziel_tabelle' => $currentSelection['ziel_tabelle'] ?? 'bankbuchungen',
//         'current_split_funktion' => $currentSelection['split_funktion'] ?? 'keiner'
//     ];
// }
?>