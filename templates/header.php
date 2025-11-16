<?php
/**
 * Header-Template - Nur HTML Ausgabe!
 */
// function renderHeader($title = "💾 Verbuchungs-App") {
//     echo '
//     <div class="header">
//         <h3>' . htmlspecialchars($title) . '</h3>
//         <p><strong>⚠️ ACHTUNG: Simulation vs. Echte Verbuchung!</strong></p>
//     </div>';
// }
function renderHeader($title = "💾 Verbuchungs-App", $infoData = []) {
    echo '<div class="header">';
    echo '<h1>' . htmlspecialchars($title) . '</h1>';
    
    // 🎯 NEU: Info-Leiste mit 3 Bereichen
    echo '<div class="info-bar">';
    
    // Bereich 1: Datei-Syntax
    echo '<div class="info-box">';
    echo '<h4>📁 Datei-Syntax</h4>';
    echo '<code>bank2024mm.csv</code>';
    echo '<div class="syntax-examples">';
    echo 'spk202401.csv, postb2024q1.csv, paypal2024.csv';
    echo '</div>';
    echo '</div>';
    
    // Bereich 2: Import-Status
    echo '<div class="info-box">';
    echo '<h4>🔄 Import-Status</h4>';
    echo '<div class="status">Bereit für Import</div>';
    echo '</div>';
    
    // Bereich 3: Nächste Schritte
    echo '<div class="info-box">';
    echo '<h4>👉 Nächste Schritte</h4>';
    echo '<div class="next-steps">';
    echo '1. Datei hochladen<br>';
    echo '2. Mapping prüfen<br>';
    echo '3. Import starten';
    echo '</div>';
    echo '</div>';
    
    echo '</div>'; // Ende info-bar
    echo '</div>'; // Ende header
}
?>