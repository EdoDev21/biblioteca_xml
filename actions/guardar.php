<?php

require_once '../database/conexion.php';


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
        header("Location: ../index.php?error=campos_incompletos");
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

        
        header("Location: ../index.php?mensaje=guardado_exitoso");
        exit;

    } catch (PDOException $e) {
        
        $error_message = $e->getMessage();
        if ($e->getCode() == 23000) { 
            header("Location: ../index.php?error=isbn_duplicado");
        } else {
            
            header("Location: ../index.php?error=" . urlencode("Error en DB: " . $error_message));
        }
        exit;
    }

} else {
    
    header("Location: ../index.php");
    exit;
}
?>