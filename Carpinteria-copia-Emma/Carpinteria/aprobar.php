<?php
include 'db_conexion.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: iniciar-sesion.php");
    exit;
}

$id = intval($_GET['id']);

// Solo actualizar el estado a aprobado
$sql = "UPDATE carpenters SET approved = 1 WHERE carpenter_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: admin.php?msg=aprobado");
} else {
    header("Location: admin.php?msg=error");
}

$stmt->close();
$conn->close();
exit;
?>
