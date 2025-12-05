<?php
session_start();
include '../includes/db_conexion.php';

// Validar sesión y rol
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'carpenter') {
  header("Location: ../auth/iniciar-seccion.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perfil Carpintero - LF Carpintería</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="flex h-screen bg-gradient-to-br from-amber-50 to-stone-100">

  <!-- Sidebar -->
  <aside class="w-64 h-full bg-white shadow-xl flex flex-col justify-between border-r border-stone-200 overflow-y-auto">
    <div>
      <div class="flex flex-col items-center py-8 border-b border-stone-200">
        <?php 
          // Siempre mostrar iniciales con gradiente azul
          $initials = '';
          $name = $_SESSION['user_name'] ?? 'Carpintero';
          $names = explode(' ', $name);
          foreach ($names as $i => $n) {
            if ($i < 2) $initials .= strtoupper(substr($n, 0, 1));
          }
        ?>
        <div class="w-28 h-28 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white text-4xl font-bold shadow-lg border-4 border-blue-700">
          <?php echo htmlspecialchars($initials); ?>
        </div>
        
        <span class="mt-3 font-bold text-stone-800 text-lg text-center px-4">
          <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Carpintero'); ?>
        </span>
        <span class="text-xs text-stone-500 mt-1">Carpintero</span>
      </div>
      
      <nav class="flex flex-col space-y-2 px-6 mt-6">
        <button onclick="mostrarSeccion('dashboard')" class="seccion-btn text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-home w-5"></i>
          <span>Panel Principal</span>
        </button>
        <button onclick="mostrarSeccion('info')" class="seccion-btn text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-info-circle w-5"></i>
          <span>Información</span>
        </button>
        <button onclick="mostrarSeccion('proyectos')" class="seccion-btn text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-upload w-5"></i>
          <span>Subir proyectos</span>
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
          <i class="fas fa-key w-5"></i>
          <span>Cambiar Contraseña</span>
        </button>
        <button onclick="abrirVistaPrevia()" class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-eye w-5"></i>
          <span>Vista Previa</span>
        </button>
      </nav>
    </div>
    
    <a href="../auth/logout.php"
       class="text-center py-5 text-stone-500 hover:text-amber-600 cursor-pointer font-medium border-t border-stone-200 flex items-center justify-center gap-2">
       <i class="fas fa-sign-out-alt"></i>
       <span>Cerrar sesión</span>
    </a>
  </aside>

  <div class="flex-1 flex flex-col h-full overflow-hidden">
    <header class="flex items-center justify-between bg-white border-b border-stone-200 px-6 h-20 shadow-sm flex-shrink-0">
      <div class="logo">
        <img src="../assets/img/Logo de Carpintería LF.png" alt="Logo" class="h-16 w-auto">
      </div>
      
      <div class="flex items-center gap-6">
        <nav class="flex items-center gap-4">
          <a href="../index.php" class="font-semibold text-stone-700 hover:text-amber-600">Inicio</a>
          <a href="../contactanos.php" class="font-semibold text-stone-700 hover:text-amber-600">Contáctanos</a>
        </nav>
        
        <?php
        // Obtener número de notificaciones pendientes
        $user_id_carp = $_SESSION['user_id'];
        $notif_query = "SELECT COUNT(*) as pending FROM project_requests WHERE carpenter_user_id = ? AND status = 'pending'";
        $notif_stmt = $conn->prepare($notif_query);
        $notif_stmt->bind_param('i', $user_id_carp);
        $notif_stmt->execute();
        $notif_result = $notif_stmt->get_result();
        $notif_count = 0;
        if ($notif_result && $row = $notif_result->fetch_assoc()) {
          $notif_count = $row['pending'];
        }
        $notif_stmt->close();
        ?>
        
        <!-- Campanita de notificaciones -->
        <button onclick="mostrarSeccion('solicitudes')" class="relative p-2 hover:bg-stone-100 rounded-full transition" title="<?php echo $notif_count; ?> solicitud(es) pendiente(s)">
          <i class="fas fa-bell text-2xl text-stone-600"></i>
          <?php if ($notif_count > 0): ?>
            <span class="absolute top-0 right-0 bg-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
              <?php echo $notif_count > 9 ? '9+' : $notif_count; ?>
            </span>
          <?php endif; ?>
        </button>
      </div>
    </header>

    <!-- Contenedor con scroll para el contenido principal -->
    <div class="flex-1 overflow-y-auto">
      <!-- Aquí va TODO el contenido HTML -->
      <?php include 'contenido_carpintero.php'; ?>
    </div>
  </div>

</body>
</html>
