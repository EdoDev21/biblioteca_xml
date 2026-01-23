<?php
require_once '../session_check.php';

require_once '../database/conexion.php';

header('Content-Type: application/json');

if (defined('MODO_LECTURA') && MODO_LECTURA === true) {
    echo json_encode([
        'success' => false,
        'mensajes' => '',
        'errores' => ['⛔ ERROR CRÍTICO: El sistema está operando en Modo de Respaldo. No se permiten importaciones masivas hasta restablecer el servidor principal.'],
        'cantidad' => 0
    ]);
    exit;
}

$mensajes = "";
$errores = [];
$contadorInsertados = 0;

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
                    $errores[] = "Error de validación XML (Línea {$error->line}): {$error->message}";
                }
                libxml_clear_errors();
            } else {
                $librosXML = $dom->getElementsByTagName('libro');
                
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
                
                if ($contadorInsertados > 0) {
                    $mensajes = "Importación exitosa. Se agregaron $contadorInsertados libros nuevos.";
                } else {
                    $mensajes = "El proceso terminó, pero no se insertaron libros nuevos (posibles duplicados).";
                }
            }

        } else {
            $errores[] = "No se pudo leer el archivo XML. Verifica que esté bien formado.";
        }

    } else {
        $errores[] = "Error al subir el archivo (Código PHP: {$archivo['error']})";
    }
}

$response = [
    'success' => (count($errores) === 0),
    'mensajes' => $mensajes,
    'errores' => $errores,
    'cantidad' => $contadorInsertados
];

echo json_encode($response);
?>