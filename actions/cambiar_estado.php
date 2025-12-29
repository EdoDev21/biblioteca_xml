<?php
require_once '../database/conexion.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id']) && isset($data['estado'])) {
    $id = $data['id'];
    $nuevo_estado = $data['estado']; // 1 o 0

    $stmt = $pdo->prepare("UPDATE libros SET disponible = ? WHERE id = ?");
    if ($stmt->execute([$nuevo_estado, $id])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>