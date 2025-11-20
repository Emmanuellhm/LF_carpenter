<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include 'db_conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $ciudad = $_POST['ciudad'];
    $especialidad = $_POST['especialidad'];
    $experiencia = $_POST['experiencia'];
    $portafolio = $_POST['portafolio'] ?? '';
    $hoja_vida = $_FILES['hoja_vida']['name'] ?? '';
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Guardar hoja de vida si se subió
    if (!empty($_FILES['hoja_vida']['tmp_name'])) {
        move_uploaded_file($_FILES['hoja_vida']['tmp_name'], "uploads/" . $hoja_vida);
    }

    // Insertar con tus columnas reales
    $sql = "INSERT INTO carpenters 
            (carpenter_name, specialties, experience_years, description, is_verified, approved, created_at, last_update)
            VALUES (?, ?, ?, ?, 0, 0, NOW(), NOW())";

    $stmt = $conn->prepare($sql);
    $descripcion = "Ciudad: $ciudad | Tel: $telefono | Email: $email | Portafolio: $portafolio | CV: $hoja_vida";
    $stmt->bind_param("ssis", $nombre, $especialidad, $experiencia, $descripcion);

    if ($stmt->execute()) {
        header("Location: registro_exitoso.html");
        exit;
    } else {
        echo "❌ Error al registrar la solicitud: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: registro-C.html");
    exit;
}
?>
