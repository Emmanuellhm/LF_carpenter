<?php
include '../includes/db_conexion.php';
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$uid = $_SESSION['user_id'];
$project_id = intval($_POST['project_id'] ?? 0);
$comment = trim($_POST['comment'] ?? '');

if ($project_id <= 0 || $comment === '') {
    echo json_encode(['error' => 'Datos inválidos']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO project_comments (project_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param('iis', $project_id, $uid, $comment);
if ($stmt->execute()) {
    // Crear notificación para el propietario del proyecto
    $comment_id = $stmt->insert_id;
    $stmt->close();

    $q = $conn->prepare("SELECT carpenter_user_id FROM portafolio WHERE project_id = ? LIMIT 1");
    $q->bind_param('i', $project_id);
    $q->execute();
    $resq = $q->get_result();
    if ($resq && $resq->num_rows > 0) {
        $prow = $resq->fetch_assoc();
        $owner = $prow['carpenter_user_id'];
        if ($owner != $uid) {
            $msg = 'Nuevo comentario en tu proyecto';
            $insn = $conn->prepare("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (?, ?, 0, NOW())");
            $insn->bind_param('is', $owner, $msg);
            $insn->execute();
            $insn->close();
        }
    }
    $q->close();

    echo json_encode(['ok' => true, 'comment_id' => $comment_id]);
} else {
    echo json_encode(['error' => $conn->error]);
}
$conn->close();
?>
