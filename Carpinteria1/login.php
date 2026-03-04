<?php
session_start();
include 'db_conexion.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: iniciar-seccion.php");
    exit;
}

$email = trim($_POST['email']);
$password = trim($_POST['password']);
$rol = trim($_POST['rol']);

if ($email === '' || $password === '' || $rol === '') {
    header("Location: iniciar-seccion.php?error=1");
    exit;
}

if ($rol === "admin" || $rol === "user") {

    $sql = "SELECT user_id, full_name, email, password_hash, role, phone, city 
            FROM users 
            WHERE email = ? AND role = ? 
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $rol);
}

elseif ($rol === "carpenter") {

    $sql = "SELECT carpenter_id AS id, carpenter_name AS full_name, 
                   email, password_hash 
            FROM carpenters 
            WHERE email = ? AND approved = 1
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: iniciar-seccion.php?error=1");
    exit;
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password_hash'])) {
    header("Location: iniciar-seccion.php?error=1");
    exit;
}

$_SESSION['user_id'] = $user['user_id'] ?? $user['id'];
$_SESSION['user_name'] = $user['full_name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_phone'] = $user['phone'] ?? '';
$_SESSION['user_city'] = $user['city'] ?? '';
$_SESSION['name'] = $user['full_name']; // Mantener por compatibilidad
$_SESSION['role'] = $rol;

if ($rol === "admin") {
    header("Location: admin.php");
    exit;
}

if ($rol === "carpenter") {
    header("Location: panel_carpintero.php");
    exit;
}

if ($rol === "user") {
    header("Location: panel_usuario.php");
    exit;
}

header("Location: iniciar-seccion.php");
exit;

?>
