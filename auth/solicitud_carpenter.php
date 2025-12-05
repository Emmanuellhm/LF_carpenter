<?php
include '../includes/db_conexion.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: registro_C.php");
    exit;
}

// Recoger datos del formulario
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$ciudad = trim($_POST['ciudad'] ?? '');
$especialidad = trim($_POST['especialidad'] ?? '');
$experiencia = intval($_POST['experiencia'] ?? 0);
$password = trim($_POST['password'] ?? '');

// Validación básica
if ($nombre === '' || $email === '' || $password === '') {
    header("Location: registro_C.php?error=1");
    exit;
}

// Hash de contraseña
$hash = password_hash($password, PASSWORD_DEFAULT);

// Manejo de carga de hoja de vida (CV)
$cv_ruta = NULL;
if (isset($_FILES['hoja_vida']) && $_FILES['hoja_vida']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = __DIR__ . '/../assets/uploads/cvs/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Validar que sea PDF
    $file_type = $_FILES['hoja_vida']['type'];
    if ($file_type === 'application/pdf') {
        $filename = 'cv_' . time() . '_' . basename($_FILES['hoja_vida']['name']);
        $dest = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['hoja_vida']['tmp_name'], $dest)) {
            $cv_ruta = 'assets/uploads/cvs/' . $filename; // Ruta relativa para la BD
        }
    }
}

// Construir descripción completa con todos los datos (sin portafolio)
$description_parts = [];
if ($telefono) $description_parts[] = "Teléfono: $telefono";
if ($ciudad) $description_parts[] = "Ciudad: $ciudad";
if ($email) $description_parts[] = "Email: $email";

$description = implode(' | ', $description_parts);

// Insertar en la base de datos
$sql = "INSERT INTO carpenters 
        (carpenter_name, email, password_hash, specialties, experience_years, description, cv_file, approved, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, 0, NOW())";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Error preparing statement: " . $conn->error);
    header("Location: registro_C.php?error=1");
    exit;
}

$stmt->bind_param("ssssiss", 
    $nombre,
    $email,
    $hash,
    $especialidad,
    $experiencia,
    $description,
    $cv_ruta
);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: registro_C.php?exito=1");
    exit;
} else {
    error_log("Error executing: " . $stmt->error);
    $stmt->close();
    $conn->close();
    header("Location: registro_C.php?error=1");
    exit;
}
?>
