<?php

$xmlFile = 'xml/catalogo.xml';
$xslFile = 'schemas/libros.xsl';

if (file_exists($xmlFile) && file_exists($xslFile)) {
    
    $xml = new DOMDocument();
    $xml->load($xmlFile);

    $xsl = new DOMDocument();
    $xsl->load($xslFile);

    $proc = new XSLTProcessor();
    $proc->importStyleSheet($xsl);

    echo $proc->transformToXML($xml);

} else {
    echo "Error: No se encuentra el archivo 'xml/catalogo.xml' o 'schemas/libros.xsl'. <br>";
    echo "Intenta primero 'Exportar XML' desde el menú principal.";
}
?>