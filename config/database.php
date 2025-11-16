<?php
/**
 * Datenbank-Konfiguration
 * Hier nur die Verbindung - keine Logik!
 */
function getDatabaseConnection() {
    $config = [
        'host' => '192.168.3.167',
        'dbname' => 'bhokt25', 
        'username' => 'dbuser',
        'password' => 'geheim',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    ];
    
    try {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8";
        return new PDO($dsn, $config['username'], $config['password'], $config['options']);
    } catch (PDOException $e) {
        die("Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
    }
}
?>