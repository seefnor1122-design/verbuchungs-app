<?php
// modules/layout.php

function startLayout() {
    echo '<!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Buchhaltungs-App</title>
        <style>
            /* Haupt-Layout */
            body {
                margin: 0;
                font-family: Arial, sans-serif;
                background: #f5f5f5;
            }
            
            /* Haupt-Navigationsmenü */
            .main-nav {
                background: #2c3e50;
                color: white;
                padding: 0 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            
            .nav-container {
                display: flex;
                align-items: center;
                justify-content: space-between;
                max-width: 1200px;
                margin: 0 auto;
            }
            
            .app-title {
                font-size: 1.5em;
                font-weight: bold;
                color: #3498db;
            }
            
            .nav-menu {
                display: flex;
                list-style: none;
                margin: 0;
                padding: 0;
            }
            
            .nav-item {
                position: relative;
            }
            
            .nav-button {
                background: none;
                border: none;
                color: white;
                padding: 15px 20px;
                cursor: pointer;
                font-size: 14px;
                transition: background 0.3s;
            }
            
            .nav-button:hover {
                background: #34495e;
            }
            
            .nav-button.active {
                background: #3498db;
                color: white;
            }
            
            /* App-Container */
            .app-container {
                display: flex;
                min-height: calc(100vh - 60px);
                max-width: 95%;
                margin: 0 auto;
                background: white;
            }
            
            /* Sidebar - bleibt wie in deiner App */
            .sidebar {
                width: 300px;
                background: #ecf0f1;
                border-right: 1px solid #bdc3c7;
                padding: 20px;
            }
            
            /* Hauptinhalt - bleibt kompatibel */
            .main-content {
                flex: 1;
                padding: 20px;
            }
            
            .container {
                max-width: 100%;
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
        .sidebar-header {
    border-bottom: 2px solid #3498db;
    padding-bottom: 15px;
    margin-bottom: 20px;
}

.sidebar-header h3 {
    margin: 0;
    color: #2c3e50;
}

.control-section {
    margin-bottom: 25px;
}

.control-section h4 {
    color: #34495e;
    margin-bottom: 10px;
    font-size: 14px;
}

.info-section h4 {
    color: #34495e;
    margin-bottom: 10px;
    font-size: 14px;
}

.info-item {
    border-left: 3px solid #3498db;
}
        
        </style>
    </head>
    <body>
        <!-- Haupt-Navigationsmenü -->
        <nav class="main-nav">
            <div class="nav-container">
                <div class="app-title">🏦 Buchhaltungs-App</div>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <button class="nav-button active" onclick="showModule(\'import\')">📁 Import</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-button" onclick="showModule(\'tabellen\')">🗃️ Tabellen</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-button" onclick="showModule(\'buchen\')">💳 Buchen</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-button" onclick="showModule(\'berichte\')">📊 Berichte</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-button" onclick="showModule(\'sonstiges\')">⚙️ Sonstiges</button>
                    </li>
                </ul>
            </div>
        </nav>
        
        <!-- App-Container - kompatibel mit deiner Struktur -->
        <div class="app-container">
        ';
}
function endLayout() {
    echo '
        </div>
        
        <script>
        function showModule(module) {
            // Sofort zur URL navigieren - keine Verzögerung
            window.location.href = "?module=" + module;
        }
        
        // Aktuelles Modul aus URL erkennen und korrekten Button aktivieren
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const currentModule = urlParams.get("module") || "import";
            
            // Mapping von Modul-Namen zu Button-Texten
            const moduleToButtonMap = {
                "import": "📁 Import",
                "tabellen": "🗃️ Tabellen", 
                "buchen": "💳 Buchen",
                "berichte": "📊 Berichte",
                "sonstiges": "⚙️ Sonstiges"
            };
            
            const targetButtonText = moduleToButtonMap[currentModule];
            
            // Alle Buttons durchgehen und den passenden aktivieren
            document.querySelectorAll(".nav-button").forEach(btn => {
                if (btn.textContent === targetButtonText) {
                    btn.classList.add("active");
                } else {
                    btn.classList.remove("active");
                }
            });
        });
        </script>
    </body>
    </html>';
}
// function endLayout() {
//     echo '
//         </div>
        
//         <script>
//         function showModule(module) {
//             // Visuelles Feedback
//             const button = event.target;
//             const originalText = button.textContent;
//             button.textContent = "⏳ Lädt...";
//             button.disabled = true;
            
//             // Aktiven Button markieren
//             document.querySelectorAll(".nav-button").forEach(btn => {
//                 btn.classList.remove("active");
//                 btn.disabled = false;
//                 // Text zurücksetzen
//                 const text = btn.textContent;
//                 if (text === "⏳ Lädt...") {
//                     btn.textContent = getOriginalText(btn);
//                 }
//             });
//             button.classList.add("active");
            
//             // Kurze Verzögerung für visuelles Feedback, dann navigieren
//             setTimeout(() => {
//                 window.location.href = "?module=" + module;
//             }, 300);
//         }
        
//         function getOriginalText(button) {
//             const moduleMap = {
//                 "import": "📁 Import",
//                 "tabellen": "🗃️ Tabellen",
//                 "buchen": "💳 Buchen", 
//                 "berichte": "📊 Berichte",
//                 "sonstiges": "⚙️ Sonstiges"
//             };
            
//             const urlParams = new URLSearchParams(window.location.search);
//             const currentModule = urlParams.get("module") || "import";
//             return moduleMap[currentModule];
//         }
        
//         // Aktuelles Modul aus URL erkennen und Button aktivieren
//         document.addEventListener("DOMContentLoaded", function() {
//             const urlParams = new URLSearchParams(window.location.search);
//             const currentModule = urlParams.get("module") || "import";
            
//             const moduleMap = {
//                 "import": "📁 Import",
//                 "tabellen": "🗃️ Tabellen",
//                 "buchen": "💳 Buchen",
//                 "berichte": "📊 Berichte", 
//                 "sonstiges": "⚙️ Sonstiges"
//             };
            
//             // Alle Buttons durchgehen und passenden aktivieren
//             document.querySelectorAll(".nav-button").forEach(btn => {
//                 if (btn.textContent === moduleMap[currentModule]) {
//                     btn.classList.add("active");
//                 }
//             });
//         });
//         </script>
//     </body>
//     </html>';
// }
// // function endLayout() {
//     echo '
//         </div>
        
//         <script>
//         function showModule(module) {
//             // Aktiven Button markieren
//             document.querySelectorAll(".nav-button").forEach(btn => {
//                 btn.classList.remove("active");
//             });
//             event.target.classList.add("active");
            
//             // Module laden - angepasst an deine Struktur
//             const moduleUrls = {
//                 \"import\": \"?module=import\",
//                 \"tabellen\": \"?module=tabellen\", 
//                 \"buchen\": \"?module=buchen\",
//                 \"berichte\": \"?module=berichte\",
//                 \"sonstiges\": \"?module=sonstiges\"
//             };
            
//             // Zur entsprechenden URL navigieren
//             window.location.href = moduleUrls[module];
//         }
        
//         // Aktuelles Modul aus URL erkennen
//         const urlParams = new URLSearchParams(window.location.search);
//         const currentModule = urlParams.get(\"module\") || \"import\");
        
//         // Aktiven Button setzen
//         document.querySelectorAll(\".nav-button\").forEach(btn => {
//             if (btn.textContent.includes(getButtonText(currentModule))) {
//                 btn.classList.add(\"active\");
//             }
//         });
        
//         function getButtonText(module) {
//             const texts = {
//                 \"import\": \"Import\",
//                 \"tabellen\": \"Tabellen\",
//                 \"buchen\": \"Buchen\", 
//                 \"berichte\": \"Berichte\",
//                 \"sonstiges\": \"Sonstiges\"
//             };
//             return texts[module] || \"\";
//         }
//         </script>
//     </body>
//     </html>';
// }
?>