<?php
include 'db_conexion.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("❌ No se envió por POST. Usa el formulario.");
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$rol = $_POST['rol'] ?? '';

echo "<h3>📩 Datos recibidos:</h3>";
echo "Email: $email <br>";
echo "Password: $password <br>";
echo "Rol: $rol <br><hr>";

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("⚠ No se encontró usuario con ese email.");
}

$user = $result->fetch_assoc();

echo "<h3>👤 Datos del usuario:</h3>";
echo "<pre>";
print_r($user);
echo "</pre><hr>";

if (password_verify($password, $user['password_hash'])) {
    echo "<h3>✅ Contraseña correcta</h3>";
} else {
    echo "<h3>❌ Contraseña incorrecta</h3>";
}
?>
