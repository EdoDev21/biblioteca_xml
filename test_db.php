<?php
require 'database/conexion.php';
?>
<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; text-align: center; padding: 50px;">
    <h1>Estado del Sistema</h1>
    
    <div style="
        display: inline-block; 
        padding: 10px 20px; 
        border-radius: 20px; 
        color: white; 
        font-weight: bold;
        background-color: <?php echo $color_conexion; ?>;">
        ● <?php echo $estado_conexion; ?>
    </div>

    <p>Intenta recargar la página cambiando las IPs en conexion.php</p>
</body>
</html>