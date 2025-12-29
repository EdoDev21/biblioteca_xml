<?php
// actions/guardar.php

require_once '../database/conexion.php';

// 1. IMPORTANTE: Indicamos que la respuesta será JSON, no HTML
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recolección y limpieza de datos (Igual que tenías antes)
    $isbn = isset($_POST['isbn']) ? htmlspecialchars(trim($_POST['isbn'])) : null;
    $titulo = isset($_POST['titulo']) ? htmlspecialchars(trim($_POST['titulo'])) : null;
    $autor = isset($_POST['autor']) ? htmlspecialchars(trim($_POST['autor'])) : null;
    $genero = isset($_POST['genero']) ? htmlspecialchars(trim($_POST['genero'])) : null;
    $anio_publicacion = isset($_POST['anio']) ? (int)$_POST['anio'] : null; 
    $editorial = isset($_POST['editorial']) ? htmlspecialchars(trim($_POST['editorial'])) : null;
    $paginas = isset($_POST['paginas']) ? (int)$_POST['paginas'] : null; 
    $precio = isset($_POST['precio']) ? (float)$_POST['precio'] : null; 

    // Validación básica
    if (empty($isbn) || empty($titulo) || empty($autor) || is_null($precio)) {
        // CAMBIO: En vez de redirigir, devolvemos JSON con error
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

        // 2. CAMBIO: Recuperamos el ID recién creado
        $nuevo_id = $pdo->lastInsertId();

        // 3. CAMBIO: Preparamos el array con TODOS los datos del libro
        // Esto es necesario para que Javascript pueda dibujar la fila sin recargar
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
            'disponible' => 1 // Valor por defecto en tu DB
        ];

        // 4. CAMBIO: Devolvemos éxito y los datos en JSON
        echo json_encode([
            'success' => true,
            'libro' => $libro_creado
        ]);
        exit;

    } catch (PDOException $e) {
        
        // Manejo de errores en JSON
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
    // Si entran por GET, devolvemos error JSON
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}
?>