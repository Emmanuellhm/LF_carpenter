<?php
include 'db_conexion.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: iniciar-sesion.php");
    exit;
}

$id = intval($_GET['id']);

// Borrar completamente el registro del carpintero rechazado
$sql = "DELETE FROM carpenters WHERE carpenter_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: admin.php?msg=rechazado");
} else {
    header("Location: admin.php?msg=error");
}

$stmt->close();
$conn->close();
exit;
?>
