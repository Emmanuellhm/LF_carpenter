<?php
include 'db_conexion.php';
include 'includes/mailer.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: iniciar-seccion.php");
    exit;
}

$id = intval($_GET['id']);

// Obtener datos del carpintero antes de aprobar
$info = $conn->prepare("SELECT carpenter_name, email FROM carpenters WHERE carpenter_id = ?");
$info->bind_param("i", $id);
$info->execute();
$row = $info->get_result()->fetch_assoc();

// Actualizar estado a aprobado
$sql  = "UPDATE carpenters SET approved = 1 WHERE carpenter_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();

    // ── Correo de aprobación al carpintero ───────────────────────
    if ($row) {
        $nombre = htmlspecialchars($row['carpenter_name']);
        $cuerpo = "
            <h2 style='color:#166534'>🎉 ¡Felicitaciones, <strong>$nombre</strong>!</h2>
            <p>Tu solicitud para unirte como carpintero en <strong>LF Carpintería</strong> ha sido <strong style='color:#16a34a'>APROBADA</strong>.</p>
            <p>Ya puedes iniciar sesión en la plataforma y comenzar a gestionar tu perfil y proyectos.</p>
            <div style='text-align:center;margin:28px 0'>
              <a href='http://localhost/Carpinteria-copia/iniciar-seccion.php'
                 style='background:#b45309;color:#fff;padding:13px 30px;border-radius:8px;text-decoration:none;font-weight:bold;font-size:15px'>
                Iniciar sesión
              </a>
            </div>
            <p>Saludos,<br><strong>Equipo LF Carpintería</strong></p>";

        enviarCorreo($row['email'], '✅ Tu cuenta ha sido aprobada - LF Carpintería', $cuerpo);
    }

    $conn->close();
    header("Location: admin.php?msg=aprobado");
} else {
    $conn->close();
    header("Location: admin.php?msg=error");
}
exit;
?>
