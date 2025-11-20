<?php 
ob_start(); // 🔹 Inicia el buffer de salida
include 'db_conexion.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $rol = $_POST['rol'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password_hash'])) {
            // Guardar datos de sesión
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];

            // Redirigir según el rol
            if ($user['role'] === 'admin') {
                header("Location: admin.php");
                exit;
            } elseif ($user['role'] === 'carpenter') {
                header("Location: carpintero.php");
                exit;
            } else {
              echo "<script>alert('Inicio correcto como usuario');</script>";
                header("Location: cliente1.php");
                exit;
            }
        } else {
            echo "<script>alert('Contraseña incorrecta'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Correo no encontrado'); window.history.back();</script>";
    }
}

ob_end_flush(); // 🔹 Finaliza el buffer de salida
?>
