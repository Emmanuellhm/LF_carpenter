<?php
/**
 * Helper de correo usando PHPMailer + Gmail SMTP
 * Uso: enviarCorreo($para, $asunto, $cuerpoHTML)
 */
function enviarCorreo(string $para, string $asunto, string $cuerpoHTML): bool {
    require_once __DIR__ . '/../PHPMailer/PHPMailer.php';
    require_once __DIR__ . '/../PHPMailer/SMTP.php';
    require_once __DIR__ . '/../PHPMailer/Exception.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'soporte.lfcarpinter@gmail.com';
        $mail->Password   = 'cqbdlwlulrvlcpqb';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom('soporte.lfcarpinter@gmail.com', 'LF Carpintería');
        $mail->addAddress($para);
        $mail->Subject = $asunto;
        $mail->isHTML(true);

        // Envolver en plantilla base
        $mail->Body = "
        <div style='font-family:Arial,sans-serif;max-width:560px;margin:auto;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb'>
          <div style='background:#92400e;padding:22px 24px;text-align:center'>
            <h1 style='color:#fff;margin:0;font-size:20px;letter-spacing:1px'>LF Carpintería</h1>
          </div>
          <div style='background:#ffffff;padding:32px 28px;color:#1c1917'>
            $cuerpoHTML
          </div>
          <div style='background:#f5f0eb;padding:16px;text-align:center;font-size:12px;color:#a8a29e'>
            &copy; 2025 LF Carpintería. Todos los derechos reservados.
          </div>
        </div>";

        $mail->send();
        return true;
    } catch (\Exception $e) {
        error_log('PHPMailer Error: ' . $mail->ErrorInfo);
        return false;
    }
}
?>
