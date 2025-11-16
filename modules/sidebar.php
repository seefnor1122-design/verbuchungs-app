<?php
/**
 * Sidebar-Modul - MIT BESSERER FEHLERANZEIGE
 */

/**
 * Holt alle verfügbaren Tabellen aus der Datenbank
 */
function getAvailableTables($pdo) {
    try {
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($tables)) {
            throw new Exception("Keine Tabellen in der Datenbank gefunden");
        }
        
        // System-Tabellen filtern
        $filteredTables = array_filter($tables, function($table) {
            $systemTables = ['table_configs', 'kontenliste', 'users', 'sessions', 'migrations'];
            return !in_array($table, $systemTables);
        });
        
        if (empty($filteredTables)) {
            throw new Exception("Keine passenden Tabellen gefunden (nur System-Tabellen)");
        }
        
        return $filteredTables;
        
    } catch (Exception $e) {
        // Fehler zurückgeben statt nur zu loggen
        throw new Exception("Tabellen konnten nicht geladen werden: " . $e->getMessage());
    }
}

/**
 * Generiert die Sidebar-Daten für das Template
 */
function getSidebarData($pdo, $currentSelection = []) {
    try {
        $tables = getAvailableTables($pdo);
        
        return [
            'tables' => $tables,
            'current_quell_tabelle' => $currentSelection['quell_tabelle'] ?? '',
            'current_ziel_tabelle' => $currentSelection['ziel_tabelle'] ?? 'bankbuchungen',
            'current_split_funktion' => $currentSelection['split_funktion'] ?? 'keiner',
            'error' => null // Kein Fehler
        ];
        
    } catch (Exception $e) {
        // Fehler in den Daten zurückgeben
        return [
            'tables' => [],
            'current_quell_tabelle' => $currentSelection['quell_tabelle'] ?? '',
            'current_ziel_tabelle' => $currentSelection['ziel_tabelle'] ?? 'bankbuchungen', 
            'current_split_funktion' => $currentSelection['split_funktion'] ?? 'keiner',
            'error' => $e->getMessage() // Fehler mitgeben
        ];
    }
}
?>