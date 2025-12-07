<?php
include 'database/conexion.php';

$query = "SELECT * FROM libros ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$libros = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca XML</title>
    <link rel="stylesheet" href="assets/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <header class="main-header">
        <div class="logo">
            <h1><i class="fas fa-book"></i> Gestión de Biblioteca</h1>
        </div>
        <div class="actions">
            <a href="ver_xml.php" target="_blank" class="btn btn-secondary">
                <i class="fas fa-eye"></i> Ver XML
            </a>
            
            <a href="xml/exportar.php" class="btn btn-warning">
                <i class="fas fa-file-export"></i> Exportar XML
            </a>

            <button onclick="abrirModal('modalImportar')" class="btn btn-info">
                <i class="fas fa-file-import"></i> Importar
            </button>

            <button onclick="abrirModal('modalNuevo')" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Libro
            </button>
        </div>
    </header>

    <main class="container">
        <h2>Catálogo de Libros</h2>
        
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ISBN</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Género</th>
                        <th>Año</th>
                        <th>Editorial</th>
                        <th>Páginas</th>
                        <th>Precio</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($libros) > 0): ?>
                        <?php foreach($libros as $libro): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($libro['isbn']); ?></td>
                                <td><?php echo htmlspecialchars($libro['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($libro['autor']); ?></td>
                                <td><?php echo htmlspecialchars($libro['genero']); ?></td>
                                <td><?php echo htmlspecialchars($libro['año_publicacion']); ?></td>
                                <td><?php echo htmlspecialchars($libro['editorial']); ?></td>
                                <td><?php echo htmlspecialchars($libro['paginas']); ?></td>
                                <td>$<?php echo number_format($libro['precio'], 2); ?></td>
                                <td>
                                    <span class="badge <?php echo $libro['disponible'] ? 'available' : 'borrowed'; ?>">
                                        <?php echo $libro['disponible'] ? 'Disponible' : 'Prestado'; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center;">No hay libros registrados aún.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="modalNuevo" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal('modalNuevo')">&times;</span>
            <h2>Registrar Nuevo Libro</h2>
            <form action="actions/guardar.php" method="POST">
                <div class="form-group">
                    <label>ISBN:</label>
                    <input type="text" name="isbn" required placeholder="Ej: 978-3-16-148410-0">
                </div>
                <div class="form-group">
                    <label>Título:</label>
                    <input type="text" name="titulo" required>
                </div>
                <div class="form-group">
                    <label>Autor:</label>
                    <input type="text" name="autor" required>
                </div>
                <div class="row">
                    <div class="col">
                        <label>Género:</label>
                        <select name="genero" required>
                            <option value="Novela">Novela</option>
                            <option value="Ciencia Ficción">Ciencia Ficción</option>
                            <option value="Fantasía">Fantasía</option>
                            <option value="Realismo Mágico">Realismo Mágico</option>
                            <option value="Fábula">Fábula</option>
                            <option value="Poesía">Poesía</option>
                            <option value="Teatro">Teatro</option>
                            <option value="Ensayo">Ensayo</option>
                            <option value="Biografía">Biografía</option>
                            <option value="Historia">Historia</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="col">
                        <label>Año:</label>
                        <input type="number" name="anio" required min="1000" max="2099">
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label>Editorial:</label>
                        <input type="text" name="editorial">
                    </div>
                    <div class="col">
                        <label>Páginas:</label>
                        <input type="number" name="paginas">
                    </div>
                </div>
                <div class="form-group">
                    <label>Precio:</label>
                    <input type="number" name="precio" step="0.01" required>
                </div>
                <button type="submit" class="btn btn-primary full-width">Guardar Libro</button>
            </form>
        </div>
    </div>

    <div id="modalImportar" class="modal">
        <div class="modal-content small">
            <span class="close" onclick="cerrarModal('modalImportar')">&times;</span>
            <h2>Importar Catálogo XML</h2>
            <p>Selecciona un archivo XML válido para cargar a la base de datos.</p>
            
            <form action="xml/importar.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="file" name="archivo_xml" accept=".xml" required>
                </div>
                <button type="submit" class="btn btn-info full-width">Subir e Importar</button>
            </form>
        </div>
    </div>

    <script>
        function abrirModal(id) {
            document.getElementById(id).style.display = "block";
        }

        function cerrarModal(id) {
            document.getElementById(id).style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = "none";
            }
        }
    </script>
</body>
</html>