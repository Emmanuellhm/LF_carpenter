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
  <title>Perfil Carpintero - LF Carpinter</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex h-screen overflow-hidden bg-gradient-to-br from-amber-50 to-stone-100">

  <!-- Sidebar -->
  <aside class="w-64 h-full bg-white shadow-xl flex flex-col justify-between border-r border-stone-200 overflow-y-auto">
    <div>
      <div class="flex flex-col items-center py-8 border-b border-stone-200">
        <img src="img/fotoP.jpg" alt="Foto Carpintero"
             class="w-28 h-28 rounded-full border-4 border-amber-600 object-cover shadow-md" id="foto-perfil">
        <span class="mt-3 font-bold text-stone-800 text-lg">
          <?php echo htmlspecialchars($_SESSION['user_name']); ?>
        </span>
      </div>
      <nav class="flex flex-col space-y-2 px-6 mt-6">
        <button onclick="mostrarSeccion('info')" class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-2 font-medium text-left">Información</button>
        <button onclick="mostrarSeccion('proyectos')" class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-2 font-medium text-left">Subir proyectos</button>
        <button onclick="mostrarSeccion('solicitudes')" class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-2 font-medium text-left">Solicitudes</button>
        <button onclick="mostrarSeccion('notificaciones')" class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-2 font-medium text-left">Notificaciones</button>
        <button onclick="abrirVistaPrevia()" class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-2 font-medium text-left">Vista Previa</button>
      </nav>
    </div>
    <a href="logout.php"
       class="text-center py-5 text-stone-500 hover:text-amber-600 cursor-pointer font-medium border-t border-stone-200">
       Cerrar sesión
    </a>
  </aside>

  <div class="flex-1 flex flex-col h-full overflow-hidden">
    <header class="flex items-center justify-between bg-white border-b border-stone-200 px-6 h-20 shadow-sm flex-shrink-0">
      <div class="logo">
        <img src="./img/Logo de Carpintería LF.png" alt="Logo" class="h-16 w-auto">
      </div>
      <nav>
        <a href="../contactanos.php" class="font-semibold text-stone-700 hover:text-amber-600">Contáctanos</a>
      </nav>
    </header>

    <!-- Contenedor con scroll para el contenido principal -->
    <div class="flex-1 overflow-y-auto">
      <?php include 'contenido_carpintero.php'; ?> 
    </div>
  </div>

  <script>
    /* (deja aquí tus funciones JS: mostrarSeccion, toggleDisponibilidad, etc.) */
  </script>

</body>
</html>
