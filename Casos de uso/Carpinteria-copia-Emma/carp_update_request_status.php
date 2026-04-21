<?php
session_start();
include 'db_conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'carpenter') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['request_id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$request_id = intval($data['request_id']);
$status = $data['status'];
$carpenter_id = $_SESSION['user_id'];

// Validar estado
if (!in_array($status, ['accepted', 'rejected'])) {
    echo json_encode(['success' => false, 'message' => 'Estado inválido']);
    exit;
}

// Verificar que la solicitud pertenece al carpintero
$check_stmt = $conn->prepare("SELECT user_id, title FROM project_requests WHERE request_id = ? AND carpenter_user_id = ?");
$check_stmt->bind_param("ii", $request_id, $carpenter_id);
$check_stmt->execute();
$res = $check_stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Solicitud no encontrada o no autorizada']);
    exit;
}

$req_data = $res->fetch_assoc();
$client_id = $req_data['user_id'];
$project_title = $req_data['title'];
$check_stmt->close();

// Actualizar estado
$update_stmt = $conn->prepare("UPDATE project_requests SET status = ? WHERE request_id = ?");
$update_stmt->bind_param("si", $status, $request_id);

if ($update_stmt->execute()) {
    // Notificar al cliente
    $status_msg = ($status === 'accepted') ? "aceptado" : "rechazado";
    $notif_msg = "El carpintero ha $status_msg tu solicitud para el proyecto '$project_title'.";
    
    $notif_stmt = $conn->prepare("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (?, ?, 0, NOW())");
    $notif_stmt->bind_param("is", $client_id, $notif_msg);
    $notif_stmt->execute();
    $notif_stmt->close();

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $conn->error]);
}

$update_stmt->close();
$conn->close();
?>
