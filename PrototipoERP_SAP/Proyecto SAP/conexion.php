<?php
$host = "localhost";
$dbname = "sap_lite";
$puerto = "3307"; 
$username = "root";  
$password = "";      

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;port=$puerto;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<div style='color:white; background:#E53E3E; padding:15px; font-family:sans-serif; text-align:center;'><b>Error Crítico de Conexión en XAMPP (Puerto 3307):</b> " . htmlspecialchars($e->getMessage()) . "</div>");
}
?>