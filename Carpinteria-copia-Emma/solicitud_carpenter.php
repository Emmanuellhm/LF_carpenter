<?php
include 'db_conexion.php';
include 'includes/mailer.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: registro_C.php");
    exit;
}

// Recoger datos del formulario
$nombre       = trim($_POST['nombre'] ?? '');
$email        = trim($_POST['email'] ?? '');
$telefono     = trim($_POST['telefono'] ?? '');
$ciudad       = trim($_POST['ciudad'] ?? '');
$especialidad = trim($_POST['especialidad'] ?? '');
$experiencia  = intval($_POST['experiencia'] ?? 0);
$password     = trim($_POST['password'] ?? '');

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
    $upload_dir = __DIR__ . '/uploads/cvs/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    $file_type = $_FILES['hoja_vida']['type'];
    if ($file_type === 'application/pdf') {
        $filename = 'cv_' . time() . '_' . basename($_FILES['hoja_vida']['name']);
        $dest = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['hoja_vida']['tmp_name'], $dest)) {
            $cv_ruta = 'uploads/cvs/' . $filename;
        }
    }
}

// Descripción compuesta
$description_parts = [];
if ($telefono)  $description_parts[] = "Teléfono: $telefono";
if ($ciudad)    $description_parts[] = "Ciudad: $ciudad";
if ($email)     $description_parts[] = "Email: $email";
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

$stmt->bind_param("ssssiss", $nombre, $email, $hash, $especialidad, $experiencia, $description, $cv_ruta);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();

    // ── Correo de confirmación al carpintero ──────────────────────
    $cuerpo = "
        <h2 style='color:#92400e'>¡Solicitud recibida, <strong>" . htmlspecialchars($nombre) . "</strong>!</h2>
        <p>Gracias por registrarte como carpintero en <strong>LF Carpintería</strong>. Hemos recibido tu solicitud y está siendo revisada por nuestro equipo.</p>
        <div style='background:#fef3c7;border-left:4px solid #f59e0b;padding:14px 18px;border-radius:6px;margin:20px 0'>
          <p style='margin:0;color:#92400e'><strong>⏳ Estado actual:</strong> Pendiente de aprobación</p>
        </div>
        <p>Te notificaremos por este correo en cuanto tu solicitud sea revisada. Si fue aprobada, podrás iniciar sesión en la plataforma.</p>
        <p style='margin-top:24px'>Saludos,<br><strong>Equipo LF Carpintería</strong></p>";

    enviarCorreo($email, '✅ Solicitud de registro recibida - LF Carpintería', $cuerpo);

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
