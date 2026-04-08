<?php
include 'db_conexion.php';

$nombre = $_POST['nombre'];
$email = $_POST['email'];
$tel = $_POST['telefono'];
$ciudad = $_POST['ciudad'];
$pass = $_POST['password'];

$passHash = password_hash($pass, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (full_name, email, password_hash, phone, city, role, is_active)
        VALUES (?, ?, ?, ?, ?, 'user', 1)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $nombre, $email, $passHash, $tel, $ciudad);

$stmt->execute();

header("Location: registro_U.php?ok=1");
exit;
?>
