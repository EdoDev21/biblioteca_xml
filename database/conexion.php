


<?php
// database/conexion.php
//$host = 'localhost';
//$host = '192.168.1.82';
//$db   = 'biblioteca';
//$db   = 'biblioteca_xml';
//$user = 'root';
//$user = 'admin_remoto';
//$pass = 'root';
//$pass = 'eduardo21';

$host_maestro = '192.168.100.185';
$host_respaldo = '192.168.100.18';

$db_name = 'biblioteca';
$db_user = 'admin_remoto';
$db_pass = 'eduardo21';

$estado_conexion = 'DESCONECTADO';
$color_conexion = 'gray';
$modo_lectura = false;

try {
    $dsn_maestro = "mysql:host=$host_maestro;dbname=$db_name;charset=utf8mb4";
    $opciones = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 2, 
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];

    $pdo = new PDO($dsn_maestro, $db_user, $db_pass, $opciones);
    $estado_conexion = 'MAESTRO (Escritura)';
    $color_conexion = '#28a745';
    $modo_lectura = false;

} catch (PDOException $e_maestro) {
    
    try {
        $dsn_respaldo = "mysql:host=$host_respaldo;dbname=$db_name;charset=utf8mb4";
        $pdo = new PDO($dsn_respaldo, $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        $estado_conexion = 'RESPALDO (Solo Lectura)';
        $color_conexion = '#ffc107';
        $modo_lectura = true;

    } catch (PDOException $e_respaldo) {
        die("<h1>FATAL ERROR: Sistema de Base de Datos No Disponible</h1>
             <p>Maestro: " . $e_maestro->getMessage() . "</p>
             <p>Respaldo: " . $e_respaldo->getMessage() . "</p>");
    }
}

if (!defined('MODO_LECTURA')) {
    define('MODO_LECTURA', $modo_lectura);
}
?>