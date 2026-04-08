<?php
include 'db_conexion.php';
include 'includes/mailer.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: iniciar-sesion.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin.php");
    exit;
}

$id     = intval($_POST['id']);
$motivo = trim($_POST['motivo'] ?? 'No se especificÃ³ un motivo.');

// Obtener datos del carpintero antes de eliminar
$info = $conn->prepare("SELECT carpenter_name, email FROM carpenters WHERE carpenter_id = ?");
$info->bind_param("i", $id);
$info->execute();
$row = $info->get_result()->fetch_assoc();

// Borrar el registro del carpintero rechazado
$stmt = $conn->prepare("DELETE FROM carpenters WHERE carpenter_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();

    // â”€â”€ Correo de rechazo al carpintero â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    if ($row) {
        $nombre  = htmlspecialchars($row['carpenter_name']);
        $motivoH = nl2br(htmlspecialchars($motivo));
        $cuerpo = "
            <h2 style='color:#991b1b'>Solicitud no aprobada</h2>
            <p>Hola <strong>$nombre</strong>, lamentamos informarte que tu solicitud para unirte como carpintero en <strong>LF CarpinterÃ­a</strong> no ha sido aprobada en esta ocasiÃ³n.</p>
            <div style='background:#fef2f2;border-left:4px solid #ef4444;padding:14px 18px;border-radius:6px;margin:20px 0'>
              <p style='margin:0 0 6px;font-weight:bold;color:#b91c1c'>Motivo del rechazo:</p>
              <p style='margin:0;color:#7f1d1d'>$motivoH</p>
            </div>
            <p>Si tienes alguna duda o crees que fue un error, puedes ponerte en contacto con nosotros respondiendo a este correo.</p>
            <p style='margin-top:24px'>Saludos,<br><strong>Equipo LF CarpinterÃ­a</strong></p>";

        enviarCorreo($row['email'], 'âŒ Tu solicitud no fue aprobada - LF CarpinterÃ­a', $cuerpo);
    }

    $conn->close();
    header("Location: admin.php?msg=rechazado");
} else {
    $conn->close();
    header("Location: admin.php?msg=error");
}
exit;
?>
