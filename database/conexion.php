<?php
$host = 'localhost';
//$host = '192.168.1.83';
$db   = 'biblioteca';
//$db   = 'biblioteca_xml';
$user = 'root';
//$user = 'usuario_biblio';
$pass = 'root';
//$pass = '123456';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO("mysql:host=$host;port=3306;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión remota: " . $e->getMessage());
}
?>