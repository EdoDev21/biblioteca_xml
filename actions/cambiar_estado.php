<?php
require_once '../database/conexion.php';

header('Content-Type: application/json');

if (defined('MODO_LECTURA') && MODO_LECTURA === true) {
    echo json_encode([
        'success' => false, 
        'error' => 'SISTEMA EN MANTENIMIENTO: No se pueden realizar préstamos/devoluciones mientras el servidor principal está fuera de línea.'
    ]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id']) && isset($data['estado'])) {
    $id = (int)$data['id'];
    $nuevo_estado = (int)$data['estado'];

    try {
        $stmt = $pdo->prepare("UPDATE libros SET disponible = ? WHERE id = ?");
        
        if ($stmt->execute([$nuevo_estado, $id])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se pudo actualizar el registro.']);
        }

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Error de BD: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
}
?>