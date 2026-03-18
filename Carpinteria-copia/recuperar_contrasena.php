<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'db_conexion.php';

$mensaje = '';
$tipo    = '';

// ─── Procesamiento del formulario ───────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);

    // Buscar en users
    $found = false;
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) $found = true;

    // Buscar en carpenters si no se encontró
    if (!$found) {
        $stmt2 = $conn->prepare("SELECT email FROM carpenters WHERE email = ? LIMIT 1");
        $stmt2->bind_param("s", $email);
        $stmt2->execute();
        if ($stmt2->get_result()->num_rows > 0) $found = true;
    }

    if (!$found) {
        // Por seguridad mostramos el mismo mensaje aunque no exista
        $mensaje = 'Si ese correo está registrado, recibirás el enlace en breve.';
        $tipo    = 'ok';
    } else {
        // Generar token seguro
        $token   = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 30 * 60); // 30 minutos

        // Borrar tokens anteriores del mismo correo
        $conn->prepare("DELETE FROM password_resets WHERE email = ?")->execute() == null;
        $del = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
        $del->bind_param("s", $email);
        $del->execute();

        // Guardar token
        $ins = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $ins->bind_param("sss", $email, $token, $expires);
        $ins->execute();

        // Enviar correo con PHPMailer
        require_once 'PHPMailer/PHPMailer.php';
        require_once 'PHPMailer/SMTP.php';
        require_once 'PHPMailer/Exception.php';

        $link = "http://localhost/Carpinteria-copia/reset_password.php?token=" . $token;

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'soporte.lfcarpinter@gmail.com';
            $mail->Password   = 'cqbdlwlulrvlcpqb';   // app password sin espacios
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom('soporte.lfcarpinter@gmail.com', 'LF Carpintería');
            $mail->addAddress($email);
            $mail->Subject = 'Recupera tu contraseña - LF Carpintería';
            $mail->isHTML(true);
            $mail->Body = "
                <div style='font-family:Arial,sans-serif;max-width:500px;margin:auto'>
                  <div style='background:#92400e;padding:24px;border-radius:12px 12px 0 0;text-align:center'>
                    <h1 style='color:#fff;margin:0;font-size:22px'>LF Carpintería</h1>
                  </div>
                  <div style='background:#fff;padding:32px;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 12px 12px'>
                    <h2 style='color:#1c1917'>Restablecer contraseña</h2>
                    <p style='color:#57534e'>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta. Haz clic en el botón de abajo para crear una nueva contraseña:</p>
                    <div style='text-align:center;margin:32px 0'>
                      <a href='$link'
                         style='background:#b45309;color:#fff;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:bold;font-size:16px'>
                        Restablecer contraseña
                      </a>
                    </div>
                    <p style='color:#78716c;font-size:13px'>Este enlace expirará en <strong>30 minutos</strong>. Si no solicitaste esto, ignora este mensaje.</p>
                    <hr style='border:none;border-top:1px solid #e5e7eb;margin:24px 0'>
                    <p style='color:#a8a29e;font-size:12px;text-align:center'>© 2025 LF Carpintería. Todos los derechos reservados.</p>
                  </div>
                </div>";

            $mail->send();
            $mensaje = 'Si ese correo está registrado, recibirás el enlace en breve.';
            $tipo    = 'ok';
        } catch (Exception $e) {
            $mensaje = 'No se pudo enviar el correo. Por favor intenta más tarde.';
            $tipo    = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperar Contraseña - LF Carpinter</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-amber-50 text-stone-800 font-sans min-h-screen flex flex-col">

  <!-- Header -->
  <header class="bg-white shadow-lg sticky top-0 z-50">
    <div class="container mx-auto px-6 py-2">
      <div class="flex justify-between items-center md:grid md:grid-cols-3">
        <div class="flex items-center justify-self-start">
          <img src="./img/Logo de Carpintería LF.png" alt="LF Logo" class="h-16 w-16">
        </div>
        <div class="hidden md:flex justify-center justify-self-center">
          <div class="flex space-x-8">
            <a href="index.php" class="text-gray-600 hover:text-amber-700 transition font-medium">Página de inicio</a>
            <a href="contactanos.php" class="text-gray-600 hover:text-amber-700 transition font-medium">Contáctanos</a>
          </div>
        </div>
        <div class="flex items-center justify-self-end">
          <a href="iniciar-seccion.php" class="bg-amber-700 text-white px-6 py-2 rounded-lg hover:bg-amber-800 transition shadow-md">
            Iniciar sesión
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- Main -->
  <main class="px-6 md:px-20 py-16 flex-grow">
    <div class="max-w-md mx-auto">

      <!-- Resultado -->
      <?php if ($mensaje): ?>
      <div class="mb-6 px-5 py-4 rounded-xl text-center font-medium <?php echo $tipo === 'ok' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300'; ?>">
        <?php echo $tipo === 'ok' ? '✅' : '❌'; ?> <?php echo $mensaje; ?>
      </div>
      <?php endif; ?>

      <!-- Encabezado -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 rounded-full mb-4">
          <i class="fas fa-lock text-amber-700 text-2xl"></i>
        </div>
        <h2 class="text-3xl font-extrabold text-gray-800 mb-2">Recuperar Contraseña</h2>
        <p class="text-gray-600">Ingresa tu correo registrado y te enviaremos un enlace para restablecerla</p>
      </div>

      <!-- Formulario -->
      <div class="bg-white p-8 rounded-xl shadow-lg border border-amber-100">
        <form method="POST" action="recuperar_contrasena.php" class="space-y-6">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico</label>
            <input type="email" id="email" name="email" placeholder="tu@correo.com" required
              pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}"
              class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-700 focus:border-amber-700 transition">
          </div>

          <button type="submit"
            class="w-full bg-amber-700 hover:bg-amber-800 text-white font-semibold py-3 rounded-lg shadow-md transition duration-200 flex items-center justify-center gap-2">
            <i class="fas fa-paper-plane"></i>
            Enviar Enlace de Recuperación
          </button>
        </form>

        <div class="text-center mt-6">
          <p class="text-gray-600">
            ¿Recordaste tu contraseña?
            <a href="iniciar-seccion.php" class="text-amber-700 hover:text-amber-800 font-medium">Inicia sesión aquí</a>
          </p>
        </div>
      </div>

      <!-- Info -->
      <div class="mt-8 bg-gradient-to-br from-amber-900 to-amber-700 text-white rounded-lg p-6 shadow-lg">
        <h3 class="font-semibold text-amber-100 mb-3 flex items-center">
          <i class="fas fa-info-circle mr-2"></i> ¿Cómo funciona?
        </h3>
        <ul class="text-sm text-amber-100 space-y-2">
          <li class="flex items-start gap-3"><span class="font-bold text-amber-300">1.</span><span>Ingresa el correo con el que te registraste</span></li>
          <li class="flex items-start gap-3"><span class="font-bold text-amber-300">2.</span><span>Te llegará un correo con un enlace seguro</span></li>
          <li class="flex items-start gap-3"><span class="font-bold text-amber-300">3.</span><span>El enlace expira en 30 minutos por seguridad</span></li>
          <li class="flex items-start gap-3"><span class="font-bold text-amber-300">4.</span><span>Haz clic en el enlace y crea tu nueva contraseña</span></li>
        </ul>
      </div>

    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-amber-950 text-amber-200 py-6 mt-16">
    <div class="container mx-auto px-6">
      <div class="border-t border-amber-800 pt-8 text-center text-amber-400">
        <p>&copy; 2025 LF Carpintería. Todos los derechos reservados.</p>
      </div>
    </div>
  </footer>

</body>
</html>
