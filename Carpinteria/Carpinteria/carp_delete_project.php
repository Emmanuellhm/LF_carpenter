<?php
header('Content-Type: application/json; charset=utf-8');
include 'db_conexion.php';
session_start();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['error' => 'Método no permitido']);
        exit;
    }

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['error' => 'No autenticado']);
        exit;
    }

    $uid = $_SESSION['user_id'];
    $project_id = intval($_POST['project_id'] ?? 0);

    if ($project_id <= 0) {
        echo json_encode(['error' => 'ID inválido']);
        exit;
    }

    // Verificar que el proyecto pertenece al usuario
    $stmt = $conn->prepare("SELECT image_path FROM portafolio WHERE project_id = ? AND carpenter_user_id = ? LIMIT 1");
    $stmt->bind_param('ii', $project_id, $uid);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if (!$res || $res->num_rows === 0) {
        $stmt->close();
        echo json_encode(['error' => 'Proyecto no encontrado']);
        exit;
    }
    
    $row = $res->fetch_assoc();
    $stmt->close();

    // Borrar archivo físico si existe
    if (!empty($row['image_path'])) {
        $path = __DIR__ . DIRECTORY_SEPARATOR . $row['image_path'];
        if (file_exists($path)) @unlink($path);
    }

    // Borrar proyecto
    $stmt = $conn->prepare("DELETE FROM portafolio WHERE project_id = ? AND carpenter_user_id = ?");
    $stmt->bind_param('ii', $project_id, $uid);
    
    if ($stmt->execute()) {
        echo json_encode(['ok' => true]);
    } else {
        echo json_encode(['error' => 'Error al eliminar']);
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
