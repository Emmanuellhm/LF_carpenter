<?php
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');
include '../includes/db_conexion.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$uid = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM portafolio WHERE carpenter_user_id = ? ORDER BY created_at DESC");
$stmt->bind_param('i', $uid);
$stmt->execute();
$res = $stmt->get_result();
$rows = [];
while ($p = $res->fetch_assoc()) {
    $p['comments'] = [];
    $stmtC = $conn->prepare("SELECT pc.*, u.full_name as author_name FROM project_comments pc LEFT JOIN users u ON pc.user_id = u.user_id WHERE pc.project_id = ? ORDER BY pc.created_at DESC");
    $stmtC->bind_param('i', $p['project_id']);
    $stmtC->execute();
    $resC = $stmtC->get_result();
    while ($c = $resC->fetch_assoc()) {
        $p['comments'][] = $c;
    }
    $stmtC->close();
    $rows[] = $p;
}
$stmt->close();

echo json_encode($rows);
$conn->close();
?>
