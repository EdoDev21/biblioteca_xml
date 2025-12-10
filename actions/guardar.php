<?php
// 1. INCLUIR CONEXIÓN A LA BASE DE DATOS
// Asegúrate de que esta ruta sea correcta
require_once '../database/conexion.php';

// Verificar si se recibieron datos por el método POST (al enviar el formulario)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. RECUPERAR Y SANEAR (LIMPIAR) LOS DATOS DEL FORMULARIO
    // Utilizamos 'isset' para asegurar que el campo fue enviado
    // Utilizamos 'trim' para quitar espacios extra
    // Utilizamos 'htmlspecialchars' para prevenir XSS (seguridad básica)
    
    $isbn = isset($_POST['isbn']) ? htmlspecialchars(trim($_POST['isbn'])) : null;
    $titulo = isset($_POST['titulo']) ? htmlspecialchars(trim($_POST['titulo'])) : null;
    $autor = isset($_POST['autor']) ? htmlspecialchars(trim($_POST['autor'])) : null;
    $genero = isset($_POST['genero']) ? htmlspecialchars(trim($_POST['genero'])) : null;
    $anio_publicacion = isset($_POST['anio']) ? (int)$_POST['anio'] : null; // Convertir a entero
    $editorial = isset($_POST['editorial']) ? htmlspecialchars(trim($_POST['editorial'])) : null;
    $paginas = isset($_POST['paginas']) ? (int)$_POST['paginas'] : null; // Convertir a entero
    $precio = isset($_POST['precio']) ? (float)$_POST['precio'] : null; // Convertir a flotante/decimal

    // 3. VALIDACIÓN BÁSICA (Asegurar que los campos requeridos no estén vacíos)
    if (empty($isbn) || empty($titulo) || empty($autor) || is_null($precio)) {
        header("Location: ../index.php?error=campos_incompletos");
        exit;
    }

    // 4. SENTENCIA SQL PREPARADA
    $sql = "INSERT INTO libros 
            (isbn, titulo, autor, genero, año_publicacion, editorial, paginas, precio) 
            VALUES 
            (:isbn, :titulo, :autor, :genero, :anio, :editorial, :paginas, :precio)";

    try {
        $stmt = $pdo->prepare($sql);

        // 5. ENLAZAR PARÁMETROS (Binding)
        $stmt->bindParam(':isbn', $isbn);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':autor', $autor);
        $stmt->bindParam(':genero', $genero);
        $stmt->bindParam(':anio', $anio_publicacion, PDO::PARAM_INT);
        $stmt->bindParam(':editorial', $editorial);
        $stmt->bindParam(':paginas', $paginas, PDO::PARAM_INT);
        $stmt->bindParam(':precio', $precio);

        // 6. EJECUTAR
        $stmt->execute();

        // 7. ÉXITO: Redirigir de vuelta al índice con un mensaje de éxito
        header("Location: ../index.php?mensaje=guardado_exitoso");
        exit;

    } catch (PDOException $e) {
        // 8. MANEJO DE ERRORES (ej. ISBN Duplicado)
        $error_message = $e->getMessage();
        if ($e->getCode() == 23000) { // Código de error para clave única duplicada (UNIQUE)
            header("Location: ../index.php?error=isbn_duplicado");
        } else {
            // Error desconocido
            header("Location: ../index.php?error=" . urlencode("Error en DB: " . $error_message));
        }
        exit;
    }

} else {
    // Si alguien accede a este archivo sin enviar el formulario (directamente por URL)
    header("Location: ../index.php");
    exit;
}
?>