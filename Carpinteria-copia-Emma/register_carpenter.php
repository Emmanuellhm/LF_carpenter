<?php
include 'db_conexion.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: registro_C.php");
    exit;
}

$nombre = trim($_POST['nombre']);
$email  = trim($_POST['email']);
$tel    = trim($_POST['telefono']);
$ciudad = trim($_POST['ciudad']);
$esp    = trim($_POST['especialidad']);
$exp    = trim($_POST['experiencia']);
$port   = trim($_POST['portafolio']);
$pass   = trim($_POST['password']);

if ($nombre === '' || $email === '' || $pass === '' || $tel === '' || $ciudad === '') {
    header("Location: registro_C.php?error=1");
    exit;
}

$passHash = password_hash($pass, PASSWORD_DEFAULT);

// Subir hoja de vida (opcional)
$hojaVidaRuta = NULL;

if (isset($_FILES["hoja_vida"]) && $_FILES["hoja_vida"]["error"] === UPLOAD_ERR_OK) {
    $nombreArchivo = time() . "_" . basename($_FILES["hoja_vida"]["name"]);
    $destino = "uploads/" . $nombreArchivo;

    if (!is_dir("uploads")) {
        mkdir("uploads", 0777, true);
    }

    if (move_uploaded_file($_FILES["hoja_vida"]["tmp_name"], $destino)) {
        $hojaVidaRuta = $destino;
    }
}

$sql = "INSERT INTO carpenters 
        (carpenter_name, email, phone, city, specialties, experience_years, description, portfolio_url, hoja_vida_url, password_hash, approved)
        VALUES (?, ?, ?, ?, ?, ?, '', ?, ?, ?, 0)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssssissss",
    $nombre,
    $email,
    $tel,
    $ciudad,
    $esp,
    $exp,
    $port,
    $hojaVidaRuta,
    $passHash
);

$stmt->execute();

header("Location: registro_C.php?ok=1");
exit;

?>
