<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

try {
    require_once '../session_check.php';
    require_once '../database/conexion.php';

    if (defined('MODO_LECTURA') && MODO_LECTURA === true) {
        throw new Exception('SISTEMA EN MODO RESPALDO: No se puede eliminar contenido.');
    }

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!isset($data['id'])) {
        throw new Exception('No se recibió el ID del libro.');
    }

    $id = (int)$data['id'];

    $stmt = $pdo->prepare("DELETE FROM libros WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('El libro no existe o ya fue eliminado.');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>