<?php
require_once 'session_check.php';
require_once 'database/conexion.php';

$query = "SELECT * FROM libros ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$libros = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrativo</title>
    <link rel="stylesheet" href="assets/styles.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>.main-header { background: #2c3e50; } body { background-color: #f4f6f9; }</style>
</head>
<body>

    <header class="main-header">
        <div class="logo"><h1><i class="fas fa-cogs"></i> Panel de Control</h1></div>
        <div class="actions">
            <span style="color:white; margin-right: 15px;">Admin: <strong><?php echo $_SESSION['usuario_ad']; ?></strong></span>
            <a href="logout.php" class="btn btn-warning"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </div>
    </header>

    <main class="container">
        <div class="admin-toolbar" style="margin-bottom: 20px; display: flex; gap: 10px; justify-content: flex-end;">
            <a href="ver_xml.php" target="_blank" class="btn btn-secondary"><i class="fas fa-code"></i> Ver XML</a>
            <a href="xml/exportar.php" class="btn btn-info"><i class="fas fa-download"></i> Exportar XML</a>
            <button onclick="abrirModal('modalImportar')" class="btn btn-warning"><i class="fas fa-upload"></i> Importar XML</button>
            <button onclick="abrirModal('modalNuevo')" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Nuevo Libro</button>
        </div>

        <div class="table-responsive" style="background: white; padding: 20px; border-radius: 8px;">
            <h3>Inventario Completo</h3>
            <table>
                <thead>
                    <tr>
                        <th>ISBN</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Género</th>
                        <th>Año</th>
                        <th>Editorial</th>
                        <th>Págs</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Gestión</th>
                    </tr>
                </thead>
                <tbody>
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
                                <span id="badge-<?php echo $libro['id']; ?>" class="badge <?php echo $libro['disponible'] ? 'available' : 'borrowed'; ?>">
                                    <?php echo $libro['disponible'] ? 'Disponible' : 'Prestado'; ?>
                                </span>
                            </td>
                            <td>
                                <button 
                                    class="btn btn-danger" 
                                    style="font-size: 0.7rem;" 
                                    onclick="eliminarLibro(<?php echo $libro['id']; ?>)">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php include 'modales_admin.php'; ?>
    
    <div id="modalNuevo" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal('modalNuevo')">&times;</span>
            <h2>Registrar Nuevo Libro</h2>
            <form id="formNuevoLibro" onsubmit="crearLibroAJAX(event)">
                <div class="form-group"><label>ISBN:</label><input type="text" name="isbn" required></div>
                <div class="form-group"><label>Título:</label><input type="text" name="titulo" required></div>
                <div class="form-group"><label>Autor:</label><input type="text" name="autor" required></div>
                <div class="row">
                    <div class="col"><label>Género:</label><input type="text" name="genero" required></div>
                    <div class="col"><label>Año:</label><input type="number" name="anio" required></div>
                </div>
                <div class="row">
                    <div class="col"><label>Editorial:</label><input type="text" name="editorial"></div>
                    <div class="col"><label>Páginas:</label><input type="number" name="paginas"></div>
                </div>
                <div class="form-group"><label>Precio:</label><input type="number" name="precio" step="0.01" required></div>
                <button type="submit" class="btn btn-primary full-width">Guardar Libro</button>
            </form>
        </div>
    </div>

    <div id="modalImportar" class="modal">
        <div class="modal-content small">
            <span class="close" onclick="cerrarModal('modalImportar')">&times;</span>
            <h2>Importar XML</h2>
            <form id="formImportarXML" onsubmit="importarXmlAJAX(event)" enctype="multipart/form-data">
                <input type="file" name="archivo_xml" accept=".xml" required>
                <button type="submit" class="btn btn-info full-width">Subir e Importar</button>
            </form>
        </div>
    </div>

    <script>
        function abrirModal(id) { document.getElementById(id).style.display = "block"; }
        function cerrarModal(id) { document.getElementById(id).style.display = "none"; }
        const ES_ADMIN = true;
    </script>
    <script src="assets/realtime.js"></script>
</body>
</html>