<?php
session_start();
include '../includes/db_conexion.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "user") {
    header("Location: iniciar-seccion.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    
    // Actualizar en la base de datos
    $sql = "UPDATE users SET full_name = ?, email = ?, phone = ?, city = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nombre, $email, $telefono, $ciudad, $user_id);
    
    if ($stmt->execute()) {
        // Actualizar sesión
        $_SESSION['user_name'] = $nombre;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_phone'] = $telefono;
        $_SESSION['user_city'] = $ciudad;
        $_SESSION['name'] = $nombre;
        
        header("Location: panel_usuario.php?success=1");
        exit;
    } else {
        header("Location: panel_usuario.php?error=1");
        exit;
    }
}

$conn->close();
?>
