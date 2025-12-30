<?php
require_once '../database/conexion.php';

try {
    $stmt = $pdo->query("SELECT * FROM libros");
    $libros = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->formatOutput = true;
    $xslt = $dom->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="../schemas/libros.xsl"');
    $dom->appendChild($xslt);
    $root = $dom->createElement('catalogo');
    $root->setAttribute('version', '1.0');
    $root->setAttribute('fecha_exportacion', date('Y-m-d\TH:i:s'));
    $root->setAttribute('total_libros', count($libros));
    $dom->appendChild($root);
    $biblio = $dom->createElement('biblioteca');
    $biblio->appendChild($dom->createElement('nombre', 'Biblioteca Central Universitaria'));
    $biblio->appendChild($dom->createElement('ubicacion', 'Campus Principal - Edificio A'));
    $biblio->appendChild($dom->createElement('telefono', '555-0123-456'));
    $root->appendChild($biblio);

    foreach ($libros as $libroDB) {
        $libroNode = $dom->createElement('libro');
        $libroNode->setAttribute('id', $libroDB['id']);
        $esDisponible = $libroDB['disponible'] ? 'true' : 'false';
        $libroNode->setAttribute('disponible', $esDisponible);
        $libroNode->appendChild($dom->createElement('isbn', $libroDB['isbn']));
        $libroNode->appendChild($dom->createElement('titulo', htmlspecialchars($libroDB['titulo'])));
        $libroNode->appendChild($dom->createElement('autor', htmlspecialchars($libroDB['autor'])));
        $libroNode->appendChild($dom->createElement('genero', $libroDB['genero']));
        $libroNode->appendChild($dom->createElement('ano_publicacion', $libroDB['ano_publicacion']));
        
        if (!empty($libroDB['editorial'])) {
            $libroNode->appendChild($dom->createElement('editorial', htmlspecialchars($libroDB['editorial'])));
        }
        if (!empty($libroDB['paginas'])) {
            $libroNode->appendChild($dom->createElement('paginas', $libroDB['paginas']));
        }
        if (!empty($libroDB['precio'])) {
            $libroNode->appendChild($dom->createElement('precio', $libroDB['precio']));
        }
        $fechaIngreso = !empty($libroDB['fecha_ingreso']) ? $libroDB['fecha_ingreso'] : date('Y-m-d');
        $libroNode->appendChild($dom->createElement('fecha_ingreso', $fechaIngreso));
        $root->appendChild($libroNode);
    }

    $rutaArchivo = __DIR__ . '/catalogo.xml';
    
    if ($dom->save($rutaArchivo)) {
        header("Location: ../index.php?mensaje=exportado");
    } else {
        echo "Error: No se pudo escribir el archivo XML. Verifica los permisos de la carpeta xml/";
    }

} catch (PDOException $e) {
    echo "Error de base de datos: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error general: " . $e->getMessage();
}
?>