<?php
session_start();

// Validar sesión y rol
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'carpenter') {
  header("Location: iniciar-seccion.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Carpintero - LF Carpintería</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="flex h-screen bg-gradient-to-br from-amber-50 to-stone-100">

  <!-- Sidebar -->
  <aside class="w-64 h-full bg-white shadow-xl flex flex-col justify-between border-r border-stone-200 overflow-y-auto">
    <div>
      <div class="flex flex-col items-center py-8 border-b border-stone-200">
        <!-- Avatar con iniciales -->
        <div class="w-28 h-28 rounded-full bg-gradient-to-br from-amber-500 to-amber-700 flex items-center justify-center text-white text-4xl font-bold shadow-lg border-4 border-amber-700">
          <?php
            $name = $_SESSION['user_name'] ?? 'Carpintero';
            $initials = '';
            $names = explode(' ', $name);
            foreach ($names as $i => $n) {
              if ($i < 2) $initials .= strtoupper(substr($n, 0, 1));
            }
            echo htmlspecialchars($initials);
          ?>
        </div>
        <span class="mt-3 font-bold text-stone-800 text-lg text-center px-4">
          <?php echo htmlspecialchars($name); ?>
        </span>
        <span class="text-xs text-stone-500 mt-1">Carpintero</span>
      </div>

      <nav class="flex flex-col space-y-2 px-6 mt-6">
        <button onclick="mostrarSeccion('dashboard')" class="seccion-btn text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-home w-5"></i>
          <span>Panel Principal</span>
        </button>
        <button onclick="mostrarSeccion('info')" class="seccion-btn text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-user w-5"></i>
          <span>Información</span>
        </button>
        <button onclick="mostrarSeccion('proyectos')" class="seccion-btn text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-folder-open w-5"></i>
          <span>Subir Proyectos</span>
        </button>
        <button onclick="mostrarSeccion('solicitudes')" class="seccion-btn text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-clipboard-list w-5"></i>
          <span>Solicitudes</span>
        </button>
        <button onclick="mostrarSeccion('notificaciones')" class="seccion-btn text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-bell w-5"></i>
          <span>Notificaciones</span>
        </button>
        <button onclick="mostrarSeccion('cambiar-password')" class="seccion-btn text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-lock w-5"></i>
          <span>Cambiar Contraseña</span>
        </button>
        <button onclick="abrirVistaPrevia()" class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-eye w-5"></i>
          <span>Vista Previa</span>
        </button>
      </nav>
    </div>

    <!-- Cerrar sesión -->
    <a href="logout.php"
       class="text-center py-5 text-stone-500 hover:text-amber-600 cursor-pointer font-medium border-t border-stone-200 flex items-center justify-center gap-2">
       <i class="fas fa-sign-out-alt"></i>
       <span>Cerrar sesión</span>
    </a>
  </aside>

  <!-- Main content -->
  <div class="flex-1 flex flex-col h-full overflow-hidden">

    <header class="flex items-center justify-between bg-white border-b border-stone-200 px-6 h-20 shadow-sm flex-shrink-0">
      <div class="logo">
        <img src="img/Logo de Carpintería LF.png" alt="Logo" class="h-16 w-auto">
      </div>
      <nav class="flex items-center gap-4">
        <a href="index.php" class="font-semibold text-stone-700 hover:text-amber-600">Inicio</a>
        <a href="contactanos.php" class="font-semibold text-stone-700 hover:text-amber-600">Contáctanos</a>
      </nav>
    </header>

    <!-- Contenedor con scroll para el contenido principal -->
    <div class="flex-1 overflow-y-auto">
      <?php include 'contenido_carpintero.php'; ?>
    </div>

  </div>

</body>
</html>
