<?php
ini_set('display_errors', 0);
error_reporting(0);
ob_start(); // Iniciar buffer de salida
include 'db_conexion.php';
session_start();
ob_clean(); // Limpiar cualquier output previo
header('Content-Type: application/json; charset=utf-8');

// Permitir acceso público si se desea; aquí sólo comprobamos sesión opcional
// if (!isset($_SESSION['user_id'])) { echo json_encode(['error'=>'No autenticado']); exit; }

$limit = intval($_GET['limit'] ?? 12);
$limit = $limit > 0 && $limit <= 100 ? $limit : 12;

// Verificar existencia de la tabla portafolio
$check = $conn->query("SHOW TABLES LIKE 'portafolio'");
if (!$check || $check->num_rows === 0) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT p.project_id, p.title, p.description, p.image_path, p.price, p.created_at, u.user_id as owner_id, u.full_name as owner_name, u.city as owner_city
        FROM portafolio p
        LEFT JOIN users u ON p.carpenter_user_id = u.user_id
        ORDER BY p.created_at DESC
        LIMIT ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $limit);
$stmt->execute();
$res = $stmt->get_result();
$rows = [];
while ($r = $res->fetch_assoc()) {
    // Normalizar separadores de ruta para el navegador
    if (!empty($r['image_path'])) {
        $r['image_path'] = str_replace('\\', '/', $r['image_path']);
    }
    $rows[] = $r;
}
$stmt->close();

echo json_encode($rows);
$conn->close();
