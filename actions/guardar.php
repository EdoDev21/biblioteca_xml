<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../session_check.php';

require_once '../database/conexion.php';

header('Content-Type: application/json');

if (defined('MODO_LECTURA') && MODO_LECTURA === true) {
    echo json_encode([
        'success' => false, 
        'error' => '⛔ ERROR CRÍTICO: El sistema está operando en el Servidor de Respaldo. No se permiten cambios hasta que se restablezca el Servidor Principal.'
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $isbn = isset($_POST['isbn']) ? htmlspecialchars(trim($_POST['isbn'])) : null;
    $titulo = isset($_POST['titulo']) ? htmlspecialchars(trim($_POST['titulo'])) : null;
    $autor = isset($_POST['autor']) ? htmlspecialchars(trim($_POST['autor'])) : null;
    $genero = isset($_POST['genero']) ? htmlspecialchars(trim($_POST['genero'])) : null;
    $anio_publicacion = isset($_POST['anio']) ? (int)$_POST['anio'] : null; 
    $editorial = isset($_POST['editorial']) ? htmlspecialchars(trim($_POST['editorial'])) : null;
    $paginas = isset($_POST['paginas']) ? (int)$_POST['paginas'] : null; 
    $precio = isset($_POST['precio']) ? (float)$_POST['precio'] : null; 

    if (empty($isbn) || empty($titulo) || empty($autor) || is_null($precio)) {
        echo json_encode(['success' => false, 'error' => 'Campos obligatorios incompletos']);
        exit;
    }

    $sql = "INSERT INTO libros 
            (isbn, titulo, autor, genero, año_publicacion, editorial, paginas, precio) 
            VALUES 
            (:isbn, :titulo, :autor, :genero, :anio, :editorial, :paginas, :precio)";

    try {
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':isbn', $isbn);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':autor', $autor);
        $stmt->bindParam(':genero', $genero);
        $stmt->bindParam(':anio', $anio_publicacion, PDO::PARAM_INT);
        $stmt->bindParam(':editorial', $editorial);
        $stmt->bindParam(':paginas', $paginas, PDO::PARAM_INT);
        $stmt->bindParam(':precio', $precio);
        $stmt->execute();

        $nuevo_id = $pdo->lastInsertId();

        $libro_creado = [
            'id' => $nuevo_id,
            'isbn' => $isbn,
            'titulo' => $titulo,
            'autor' => $autor,
            'genero' => $genero,
            'anio' => $anio_publicacion,
            'editorial' => $editorial,
            'paginas' => $paginas,
            'precio' => $precio,
            'disponible' => 1
        ];

        echo json_encode([
            'success' => true,
            'libro' => $libro_creado
        ]);
        exit;

    } catch (PDOException $e) {
        
        $mensaje_error = $e->getMessage();
        
        if ($e->getCode() == 23000) { 
            $mensaje_error = "El ISBN ya está registrado en el sistema.";
        }

        echo json_encode([
            'success' => false, 
            'error' => $mensaje_error
        ]);
        exit;
    }

} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}
?>