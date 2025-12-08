<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    
    <xsl:template match="/">
        <html>
            <head>
                <title>Vista XML Estilizada</title>
                <style>
                    body { font-family: Arial, sans-serif; background-color: #f4f4f9; padding: 20px; }
                    .header-info { background: #2c3e50; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
                    .header-info h1 { margin: 0; font-size: 24px; }
                    .meta-info { font-size: 0.9em; opacity: 0.8; margin-top: 5px; }
                    
                    table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
                    th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
                    th { background-color: #3498db; color: white; }
                    tr:hover { background-color: #f1f1f1; }
                    
                    .badge { padding: 5px 10px; border-radius: 15px; font-size: 0.8em; font-weight: bold; color: white;}
                    .si { background-color: #27ae60; }
                    .no { background-color: #c0392b; }
                    .precio { font-weight: bold; color: #2c3e50; }
                </style>
            </head>
            <body>
                <div class="header-info">
                    <h1><xsl:value-of select="catalogo/biblioteca/nombre"/></h1>
                    <div class="meta-info">
                        Ubicación: <xsl:value-of select="catalogo/biblioteca/ubicacion"/> | 
                        Tel: <xsl:value-of select="catalogo/biblioteca/telefono"/>
                    </div>
                    <div style="margin-top:10px; font-size: 0.8em;">
                        Reporte generado el: <xsl:value-of select="catalogo/@fecha_exportacion"/> | 
                        Total Libros: <xsl:value-of select="catalogo/@total_libros"/>
                    </div>
                </div>

                <h2>Listado de Libros (Vista XSLT)</h2>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>ISBN</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Género</th>
                        <th>Año</th>
                        <th>Precio</th>
                        <th>Disponible</th>
                    </tr>
                    <xsl:for-each select="catalogo/libro">
                        <tr>
                            <td><xsl:value-of select="@id"/></td>
                            <td><xsl:value-of select="isbn"/></td>
                            <td><xsl:value-of select="titulo"/></td>
                            <td><xsl:value-of select="autor"/></td>
                            <td><xsl:value-of select="genero"/></td>
                            <td><xsl:value-of select="año_publicacion"/></td>
                            <td class="precio">$<xsl:value-of select="precio"/></td>
                            <td>
                                <xsl:choose>
                                    <xsl:when test="@disponible = 'true'">
                                        <span class="badge si">Sí</span>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <span class="badge no">No</span>
                                    </xsl:otherwise>
                                </xsl:choose>
                            </td>
                        </tr>
                    </xsl:for-each>
                </table>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>