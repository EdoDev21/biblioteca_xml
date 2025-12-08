<?php
require_once '../database/conexion.php';

$mensajes = "";
$errores = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['archivo_xml'])) {
    
    $archivo = $_FILES['archivo_xml'];

    if ($archivo['error'] === UPLOAD_ERR_OK) {
        
        $rutaTemporal = $archivo['tmp_name'];
        $dom = new DOMDocument();
        libxml_use_internal_errors(true); 

        if ($dom->load($rutaTemporal)) {
            
            $rutaXSD = '../schemas/libro.xsd';
            
            if (!$dom->schemaValidate($rutaXSD)) {
                $errors = libxml_get_errors();
                foreach ($errors as $error) {
                    $errores[] = "Error de validación (Línea {$error->line}): {$error->message}";
                }
                libxml_clear_errors();
            } else {
                $librosXML = $dom->getElementsByTagName('libro');
                $contadorInsertados = 0;
                $sql = "INSERT IGNORE INTO libros (isbn, titulo, autor, genero, año_publicacion, editorial, paginas, precio, disponible, fecha_ingreso) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);

                foreach ($librosXML as $libro) {
                    $isbn      = $libro->getElementsByTagName('isbn')->item(0)->nodeValue;
                    $titulo    = $libro->getElementsByTagName('titulo')->item(0)->nodeValue;
                    $autor     = $libro->getElementsByTagName('autor')->item(0)->nodeValue;
                    $genero    = $libro->getElementsByTagName('genero')->item(0)->nodeValue;
                    $anio      = $libro->getElementsByTagName('año_publicacion')->item(0)->nodeValue;
                    $editorialNode = $libro->getElementsByTagName('editorial')->item(0);
                    $editorial = $editorialNode ? $editorialNode->nodeValue : null;

                    $paginasNode = $libro->getElementsByTagName('paginas')->item(0);
                    $paginas   = $paginasNode ? $paginasNode->nodeValue : null;

                    $precioNode = $libro->getElementsByTagName('precio')->item(0);
                    $precio    = $precioNode ? $precioNode->nodeValue : null;

                    $fechaNode = $libro->getElementsByTagName('fecha_ingreso')->item(0);
                    $fecha     = $fechaNode ? $fechaNode->nodeValue : date('Y-m-d');

                    $dispAttr = $libro->getAttribute('disponible');
                    $disponible = ($dispAttr === 'true') ? 1 : 0;

                    try {
                        $stmt->execute([$isbn, $titulo, $autor, $genero, $anio, $editorial, $paginas, $precio, $disponible, $fecha]);
                        
                        if ($stmt->rowCount() > 0) {
                            $contadorInsertados++;
                        }
                    } catch (Exception $e) {
                        $errores[] = "Error al insertar libro '$titulo': " . $e->getMessage();
                    }
                }
                
                $mensajes = "Importación exitosa. Se agregaron $contadorInsertados libros nuevos.";
            }

        } else {
            $errores[] = "No se pudo leer el archivo XML. Verifica que esté bien formado.";
        }

    } else {
        $errores[] = "Error al subir el archivo (Código: {$archivo['error']})";
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultado Importación</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .resultado-box { max-width: 600px; margin: 50px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #27ae60; background: #e8f8f5; padding: 15px; border-radius: 5px; margin-bottom: 20px;}
        .error-list { background: #fdedec; color: #c0392b; padding: 15px; border-radius: 5px; }
        .error-list ul { margin-left: 20px; }
    </style>
</head>
<body>
    <div class="resultado-box">
        <h2>Resultado de Importación</h2>
        
        <?php if (!empty($mensajes)): ?>
            <div class="success">
                <i class="fas fa-check-circle"></i> <?php echo $mensajes; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errores)): ?>
            <div class="error-list">
                <h4><i class="fas fa-exclamation-triangle"></i> Reporte de Errores:</h4>
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <a href="../index.php" class="btn btn-primary full-width" style="text-align:center; display:block;">Volver al Catálogo</a>
    </div>
</body>
</html>