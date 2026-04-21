<?php
session_start();
include 'db_conexion.php';

// Verificar que el usuario estÃ© autenticado y sea de tipo 'user'
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: iniciar-sesion.php");
    exit;
}

// Verificar que sea una solicitud POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ver_carpinteros.php");
    exit;
}

// Recoger datos del formulario
$user_id = $_SESSION['user_id'];
$carpenter_user_id = intval($_POST['carpenter_id'] ?? 0); // Recibimos directamente el user_id del carpintero
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$budget = !empty($_POST['budget']) ? floatval($_POST['budget']) : null;
$deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;
$dimensions = trim($_POST['dimensions'] ?? '');
$materials = trim($_POST['materials'] ?? '');

// ValidaciÃ³n bÃ¡sica
if ($carpenter_user_id === 0 || $title === '' || $description === '') {
    header("Location: ver_carpinteros.php?error=datos_incompletos");
    exit;
}

// Verificar que el carpintero (usuario) existe y es realmente un carpintero
$check_stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ? AND role = 'carpenter'");
$check_stmt->bind_param('i', $carpenter_user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    $check_stmt->close();
    header("Location: ver_carpinteros.php?error=carpintero_no_existe");
    exit;
}
$check_stmt->close();

// Manejo de imagen de referencia (si se subiÃ³)
$image_path = null;
if (isset($_FILES['reference_image']) && $_FILES['reference_image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = __DIR__ . '/uploads/project_requests/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Validar que sea una imagen
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
    $file_type = $_FILES['reference_image']['type'];
    
    if (in_array($file_type, $allowed_types)) {
        $ext = pathinfo($_FILES['reference_image']['name'], PATHINFO_EXTENSION);
        $filename = 'ref_' . time() . '_' . uniqid() . '.' . $ext;
        $dest = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['reference_image']['tmp_name'], $dest)) {
            $image_path = 'uploads/project_requests/' . $filename;
        }
    }
}

// Insertar la solicitud en la base de datos
$sql = "INSERT INTO project_requests 
        (user_id, carpenter_user_id, title, project_description, budget, deadline, dimensions, materials, image_path, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Error preparing statement: " . $conn->error);
    header("Location: ver_carpinteros.php?error=error_servidor");
    exit;
}

$stmt->bind_param("iissdssss", 
    $user_id,
    $carpenter_user_id,
    $title,
    $description,
    $budget,
    $deadline,
    $dimensions,
    $materials,
    $image_path
);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    
    // Redirigir con mensaje de Ã©xito
    header("Location: ver_carpinteros.php?success=solicitud_enviada");
    exit;
} else {
    error_log("Error executing: " . $stmt->error);
    $stmt->close();
    $conn->close();
    header("Location: ver_carpinteros.php?error=error_al_enviar");
    exit;
}
?>
