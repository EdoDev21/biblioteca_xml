<?php
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca UFM 360</title>
    <link rel="stylesheet" href="assets/styles.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <header class="main-header">
        <div class="logo">
            <h1><i class="fas fa-book"></i> Biblioteca UFM 360</h1>
        </div>
        <div class="actions">
            <a href="login.php" class="btn btn-secondary" style="background: #333;">
                <i class="fas fa-user-shield"></i> Acceso Admin
            </a>
        </div>
    </header>

    <main class="container">
        <?php if(MODO_LECTURA): ?>
            <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #ffeeba;">
                <strong>⚠️ MODO LECTURA:</strong> Operando desde servidor de respaldo.
            </div>
        <?php endif; ?>

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
                        <th>Págs</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Acción</th> 
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
                                    <span id="badge-<?php echo $libro['id']; ?>" class="badge <?php echo $libro['disponible'] ? 'available' : 'borrowed'; ?>">
                                        <?php echo $libro['disponible'] ? 'Disponible' : 'Prestado'; ?>
                                    </span>
                                </td>
                                <td>
                                    <button 
                                        id="btn-<?php echo $libro['id']; ?>"
                                        class="btn btn-secondary" 
                                        style="font-size: 0.8rem; padding: 2px 8px;"
                                        onclick="cambiarEstadoLibro(<?php echo $libro['id']; ?>, <?php echo $libro['disponible']; ?>)">
                                        <?php echo $libro['disponible'] ? 'Prestar' : 'Devolver'; ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="10" style="text-align:center;">No hay libros registrados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer style="text-align: center; margin-top: 50px; padding: 20px; color: #666; font-size: 0.8rem;">
        <div>Estado del Servidor: <span style="color: <?php echo $color_conexion; ?>;">● <?php echo $estado_conexion; ?></span></div>
    </footer>
    
    <script src="assets/realtime.js"></script>
    <script>const ES_ADMIN = false;</script>
</body>
</html>