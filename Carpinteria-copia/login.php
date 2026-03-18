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

// ─── Rate Limiting ────────────────────────────────────────────
$limitKey   = 'login_fails_' . md5($email);   // intento por correo
$timeKey    = 'login_locktime_' . md5($email);  // cuando se bloqueó
$maxIntentos = 10;
$lockDuration = 5 * 60; // 5 minutos en segundos

// Si hay bloqueo activo comprobamos si ya expiró
if (isset($_SESSION[$timeKey])) {
    $elapsed = time() - $_SESSION[$timeKey];
    if ($elapsed < $lockDuration) {
        $restantes = $lockDuration - $elapsed;
        header("Location: iniciar-seccion.php?error=3&wait=" . ceil($restantes / 60));
        exit;
    } else {
        // Bloqueo expirado → reset
        unset($_SESSION[$limitKey], $_SESSION[$timeKey]);
    }
}
// ──────────────────────────────────────────────────────────────

if ($rol === "admin" || $rol === "user") {

    $sql = "SELECT user_id, full_name, email, password_hash, role, phone, city, is_active 
            FROM users 
            WHERE email = ? 
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
}

elseif ($rol === "carpenter") {

    $sql = "SELECT carpenter_id AS id, carpenter_name AS full_name, 
                   email, password_hash, is_active 
            FROM carpenters 
            WHERE email = ? AND approved = 1
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
}

$stmt->execute();
$result = $stmt->get_result();

// ── Función helper para registrar un fallo ──────────────────────
function registrarFallo($limitKey, $timeKey, $maxIntentos) {
    $_SESSION[$limitKey] = ($_SESSION[$limitKey] ?? 0) + 1;
    if ($_SESSION[$limitKey] >= $maxIntentos) {
        $_SESSION[$timeKey] = time();
    }
}
// ───────────────────────────────────────────────────────────────

if ($result->num_rows === 0) {
    registrarFallo($limitKey, $timeKey, $maxIntentos);
    header("Location: iniciar-seccion.php?error=1");
    exit;
}

$user = $result->fetch_assoc();

// Verificar si la cuenta está activa
if (isset($user['is_active']) && $user['is_active'] == 0) {
    header("Location: iniciar-seccion.php?error=2"); // error=2 para cuenta inactivada/bloqueada
    exit;
}

if (!password_verify($password, $user['password_hash'])) {
    registrarFallo($limitKey, $timeKey, $maxIntentos);
    header("Location: iniciar-seccion.php?error=1");
    exit;
}

// Login exitoso → limpiar contadores
unset($_SESSION[$limitKey], $_SESSION[$timeKey]);

$_SESSION['user_id']    = $user['user_id'] ?? $user['id'];
$_SESSION['user_name']  = $user['full_name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_phone'] = $user['phone'] ?? '';
$_SESSION['user_city']  = $user['city'] ?? '';
$_SESSION['name']       = $user['full_name']; // Mantener por compatibilidad
$_SESSION['role']       = $user['role'] ?? $rol;

if ($_SESSION['role'] === "admin") {
    header("Location: admin.php");
    exit;
}

if ($_SESSION['role'] === "carpenter") {
    header("Location: panel_carpintero.php");
    exit;
}

if ($_SESSION['role'] === "user") {
    header("Location: panel_usuario.php");
    exit;
}

header("Location: iniciar-seccion.php");
exit;

?>
