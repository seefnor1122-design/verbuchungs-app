<?php
/**
 * Layout-Modul - Verwaltet das Gesamt-Layout
 * Nur Layout-Logik, kein HTML!
 */

/**
 * Startet das HTML-Grundgerüst mit CSS
 */
function startLayout() {
    // Session für Fehlerlogging etc.
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // CSS auslagern in separate Funktion
    $css = getLayoutCSS();
    
    // HTML-Grundgerüst beginnen
    echo '<!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>💾 Verbuchungs-App</title>
        <style>' . $css . '</style>
    </head>
    <body>';
}

/**
 * Beendet das HTML-Grundgerüst
 */
function endLayout() {
    echo '</body></html>';
}

/**
 * Gibt das CSS für das Layout zurück
 * Später können wir das in eine separate CSS-Datei auslagern
 */
function getLayoutCSS() {
    return '
    body { 
        font-family: Arial, sans-serif; 
        margin: 0; 
        padding: 0; 
        background: #f8f9fa; 
        display: flex;
    }
    
    /* Sidebar Styles */
    .sidebar {
        width: 300px;
        background: #ffffff;
        color: #333;
        padding: 20px;
        height: 100vh;
        position: fixed;
        overflow-y: auto;
        border-right: 1px solid #dee2e6;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    }
    
    .sidebar h2 {
        color: #2c3e50;
        border-bottom: 2px solid #3498db;
        padding-bottom: 10px;
        margin-top: 0;
    }
    
    /* Main Content */
    .main-content {
        margin-left: 300px;
        padding: 20px;
        width: calc(100% - 300px);
    }
    
    .container { 
        max-width: none; 
        margin: 0; 
        background: white; 
        padding: 20px; 
        border-radius: 8px; 
        box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
    }
    
    /* === SIDEBAR FORMULAR-STYLES === */
    .sidebar .form-group {
        margin: 20px 0;
    }
    
    .sidebar label {
        display: block;
        margin-bottom: 8px;
        color: #2c3e50;
        font-weight: bold;
        font-size: 14px;
    }
    
    .sidebar select, 
    .sidebar input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        background: white;
        color: #333;
        font-size: 14px;
    }
    
    .sidebar select:focus,
    .sidebar input:focus {
        border-color: #3498db;
        outline: none;
        box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
    }
    
    /* === BUTTON STYLES === */
    .sidebar button {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        margin: 10px 0;
        transition: all 0.3s ease;
    }
    
    .sidebar button.simulation {
        background: #17a2b8;
        color: white;
    }
    
    .sidebar button.simulation:hover {
        background: #138496;
        transform: translateY(-1px);
    }
    
    .sidebar button.gefahr {
        background: #dc3545;
        color: white;
    }
    
    .sidebar button.gefahr:hover {
        background: #c82333;
        transform: translateY(-1px);
    }
    
    /* === FEHLER & INFO STYLES === */
    .error {
        background: #f8d7da;
        color: #721c24;
        padding: 10px;
        border-radius: 4px;
        margin: 10px 0;
        border-left: 4px solid #dc3545;
    }
    
    .success {
        background: #d4edda;
        color: #155724;
        padding: 10px;
        border-radius: 4px;
        margin: 10px 0;
        border-left: 4px solid #28a745;
    }
    
    .info {
        background: #cce7ff;
        color: #004085;
        padding: 10px;
        border-radius: 4px;
        margin: 10px 0;
        border-left: 4px solid #3498db;
    }
    
    /* === KLEINE HINWEISE === */
    .small {
        font-size: 12px;
        color: #6c757d;
        margin-top: 5px;
    }
        /* Info-Leiste Styles */
    .info-bar {
        display: flex;
        gap: 15px;
        margin: 15px 0;
        flex-wrap: wrap;
    }

    .info-box {
        flex: 1;
        min-width: 200px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 12px;
        font-size: 16px;
    }

    .info-box h4 {
        margin: 0 0 8px 0;
        color: #2c3e50;
        font-size: 14px;
    }

    .syntax-examples {
        font-size: 14px;
        color: #6c757d;
        margin-top: 5px;
    }

    .status, .next-steps {
        font-size: 13px;
        line-height: 1.4;
    }
        ';
}
?>