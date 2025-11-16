<?php
/**
 * Sidebar-Template - MIT FEHLERBEHANDLUNG
 */

/**
 * Sidebar-Template - NUR HTML!
 */
function renderSidebar($sidebarData) {
    echo '<div class="sidebar">';
    echo '<h2>📊 Verbuchungs-Steuerung</h2>';
    
    // Fehler anzeigen falls vorhanden
    if (!empty($sidebarData['error'])) {
        echo '<div class="error">';
        echo '❌ ' . htmlspecialchars($sidebarData['error']);
        echo '</div>';
    }
    
    // Sidebar-Formular
    echo '<form method="post">';
    
    // Quell-Tabelle Auswahl
    echo '<div class="form-group">';
    echo '<label for="quell_tabelle">📁 Quell-Tabelle:</label>';
    echo '<select name="quell_tabelle" id="quell_tabelle" required>';
    echo '<option value="">-- Tabelle wählen --</option>';
    
    foreach ($sidebarData['tables'] as $table) {
        $selected = ($table === $sidebarData['current_quell_tabelle']) ? 'selected' : '';
        echo '<option value="' . htmlspecialchars($table) . '" ' . $selected . '>' . htmlspecialchars($table) . '</option>';
    }
    
    echo '</select>';
    echo '</div>';
    
    // Ziel-Tabelle Eingabe
    echo '<div class="form-group">';
    echo '<label for="ziel_tabelle">💾 Ziel-Tabelle:</label>';
    echo '<input type="text" name="ziel_tabelle" id="ziel_tabelle" 
           value="' . htmlspecialchars($sidebarData['current_ziel_tabelle']) . '" 
           required placeholder="z.B. bankbuchungen">';
    echo '<div class="small">Wird automatisch erstellt falls nicht vorhanden</div>';
    echo '</div>';
    
    // Buttons
    echo '<div class="form-group">';
    echo '<button type="submit" name="nur_simulation" class="simulation">🔍 Nur Simulation</button>';
    echo '<button type="submit" name="verbuchung_starten" class="gefahr">💾 Echte Verbuchung</button>';
    echo '</div>';
    
    echo '</form>';
    echo '</div>';
}

// function renderSidebar($sidebarData) {
//     echo '<div class="sidebar">';
//     echo '<h2>📊 Verbuchungs-Steuerung</h2>';
    
//     // 🎯 Fehler anzeigen falls vorhanden
//     if (!empty($sidebarData['error'])) {
//         echo '<div class="error" style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px;">';
//         echo '❌ ' . htmlspecialchars($sidebarData['error']);
//         echo '</div>';
//     }
    
//     // Sidebar-Formular
//     echo '<form method="post">';
    
//     // Quell-Tabelle Auswahl
//     echo '<div class="form-group">';
//     echo '<label for="quell_tabelle">📁 Quell-Tabelle:</label>';
//     echo '<select name="quell_tabelle" id="quell_tabelle" required>';
//     echo '<option value="">-- Tabelle wählen --</option>';
    
//     foreach ($sidebarData['tables'] as $table) {
//         $selected = ($table === $sidebarData['current_quell_tabelle']) ? 'selected' : '';
//         echo '<option value="' . htmlspecialchars($table) . '" ' . $selected . '>' . htmlspecialchars($table) . '</option>';
//     }
    
//     echo '</select>';
//     echo '</div>';
    
//     // Ziel-Tabelle Eingabe
//     echo '<div class="form-group">';
//     echo '<label for="ziel_tabelle">💾 Ziel-Tabelle:</label>';
//     echo '<input type="text" name="ziel_tabelle" id="ziel_tabelle" 
//            value="' . htmlspecialchars($sidebarData['current_ziel_tabelle']) . '" 
//            required placeholder="z.B. bankbuchungen">';
//     echo '</div>';
    
//     // Buttons
//     echo '<div class="form-group">';
//     echo '<button type="submit" name="nur_simulation" class="simulation">🔍 Nur Simulation</button>';
//     echo '<button type="submit" name="verbuchung_starten" class="gefahr">💾 Echte Verbuchung</button>';
//     echo '</div>';
    
//     echo '</form>';
//     echo '</div>';
// }
?>