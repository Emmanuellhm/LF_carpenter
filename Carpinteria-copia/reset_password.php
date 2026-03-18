<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'db_conexion.php';

$token   = trim($_GET['token'] ?? '');
$mensaje = '';
$tipo    = '';
$valid   = false;

// ─── Validar token ────────────────────────────────────────────────
if ($token !== '') {
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW() LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $valid = true;
        $tokenEmail = $res->fetch_assoc()['email'];
    }
}

// ─── Procesar nueva contraseña ────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid) {
    $nueva    = $_POST['password'] ?? '';
    $confirma = $_POST['confirmar'] ?? '';

    if (strlen($nueva) < 8) {
        $mensaje = 'La contraseña debe tener al menos 8 caracteres.';
        $tipo    = 'error';
    } elseif ($nueva !== $confirma) {
        $mensaje = 'Las contraseñas no coinciden.';
        $tipo    = 'error';
    } else {
        $hash = password_hash($nueva, PASSWORD_DEFAULT);

        // Actualizar en users
        $u = $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
        $u->bind_param("ss", $hash, $tokenEmail);
        $u->execute();

        // Actualizar en carpenters
        $c = $conn->prepare("UPDATE carpenters SET password_hash = ? WHERE email = ?");
        $c->bind_param("ss", $hash, $tokenEmail);
        $c->execute();

        // Borrar token usado
        $conn->prepare("DELETE FROM password_resets WHERE token = ?")->execute();
        $del = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
        $del->bind_param("s", $token);
        $del->execute();

        $mensaje = '¡Contraseña actualizada! Ahora puedes iniciar sesión.';
        $tipo    = 'ok';
        $valid   = false; // Ocultar formulario
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nueva Contraseña - LF Carpinter</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-amber-50 text-stone-800 font-sans min-h-screen flex flex-col">

  <!-- Header -->
  <header class="bg-white shadow-lg sticky top-0 z-50">
    <div class="container mx-auto px-6 py-2">
      <div class="flex justify-between items-center">
        <img src="./img/Logo de Carpintería LF.png" alt="LF Logo" class="h-16 w-16">
        <a href="iniciar-seccion.php" class="bg-amber-700 text-white px-6 py-2 rounded-lg hover:bg-amber-800 transition shadow-md">
          Iniciar sesión
        </a>
      </div>
    </div>
  </header>

  <main class="px-6 py-16 flex-grow flex items-center justify-center">
    <div class="w-full max-w-md">

      <!-- Aviso -->
      <?php if ($mensaje): ?>
      <div class="mb-6 px-5 py-4 rounded-xl text-center font-medium <?php echo $tipo === 'ok' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300'; ?>">
        <?php echo $tipo === 'ok' ? '✅' : '❌'; ?> <?php echo $mensaje; ?>
        <?php if ($tipo === 'ok'): ?>
          <br><a href="iniciar-seccion.php" class="underline font-bold mt-2 inline-block">Ir al inicio de sesión</a>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <?php if (!$valid && $tipo !== 'ok'): ?>
        <!-- Token inválido o expirado -->
        <div class="bg-white p-10 rounded-2xl shadow-xl text-center border border-stone-200">
          <div class="text-5xl mb-4">⏰</div>
          <h2 class="text-2xl font-bold text-stone-800 mb-2">Enlace inválido o expirado</h2>
          <p class="text-stone-500 mb-6">Este enlace ya expiró o no es válido. Solicita uno nuevo.</p>
          <a href="recuperar_contrasena.php"
             class="bg-amber-700 text-white px-6 py-3 rounded-lg hover:bg-amber-800 transition font-semibold">
            Solicitar nuevo enlace
          </a>
        </div>

      <?php elseif ($valid): ?>
        <!-- Formulario nueva contraseña -->
        <div class="text-center mb-8">
          <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 rounded-full mb-4">
            <i class="fas fa-key text-amber-700 text-2xl"></i>
          </div>
          <h2 class="text-3xl font-extrabold text-gray-800 mb-2">Nueva Contraseña</h2>
          <p class="text-gray-600">Elige una contraseña segura de al menos 8 caracteres</p>
        </div>

        <div class="bg-white p-8 rounded-xl shadow-lg border border-amber-100">
          <form method="POST" action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" class="space-y-5">

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Nueva contraseña</label>
              <input type="password" name="password" required minlength="8"
                title="Mínimo 8 caracteres"
                placeholder="Mínimo 8 caracteres"
                class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-700 transition">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Confirmar contraseña</label>
              <input type="password" name="confirmar" required minlength="8"
                placeholder="Repite la contraseña"
                class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-700 transition">
            </div>

            <button type="submit"
              class="w-full bg-amber-700 hover:bg-amber-800 text-white font-semibold py-3 rounded-lg shadow-md transition flex items-center justify-center gap-2">
              <i class="fas fa-check"></i> Guardar nueva contraseña
            </button>
          </form>
        </div>
      <?php endif; ?>

    </div>
  </main>

  <footer class="bg-amber-950 text-amber-200 py-4 text-center text-sm">
    <p>&copy; 2025 LF Carpintería. Todos los derechos reservados.</p>
  </footer>

</body>
</html>
