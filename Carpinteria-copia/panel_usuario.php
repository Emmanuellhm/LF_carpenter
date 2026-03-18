<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "user") {
    header("Location: iniciar-seccion.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel Usuario - LF Carpintería</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="flex h-screen overflow-hidden bg-gradient-to-br from-amber-50 to-stone-100">

<!-- Mensajes de éxito/error -->
<?php if (isset($_GET['success'])): ?>
<div id="mensaje-exito" class="fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
  ✅ Perfil actualizado correctamente
</div>
<script>
  setTimeout(() => {
    document.getElementById('mensaje-exito')?.remove();
  }, 3000);
</script>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div id="mensaje-error" class="fixed top-4 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
  ❌ Error al actualizar perfil
</div>
<script>
  setTimeout(() => {
    document.getElementById('mensaje-error')?.remove();
  }, 3000);
</script>
<?php endif; ?>

  <!-- Sidebar -->
  <aside class="w-64 h-full bg-white shadow-xl flex flex-col justify-between border-r border-stone-200 overflow-y-auto">
    <div>
      <div class="flex flex-col items-center py-8 border-b border-stone-200">
        <div class="w-28 h-28 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white text-4xl font-bold shadow-lg border-4 border-amber-600">
          <?php 
            $initials = '';
            $name = $_SESSION['user_name'] ?? 'Usuario';
            $names = explode(' ', $name);
            foreach ($names as $i => $n) {
              if ($i < 2) $initials .= strtoupper(substr($n, 0, 1));
            }
            echo htmlspecialchars($initials);
          ?>
        </div>
        <span class="mt-3 font-bold text-stone-800 text-lg text-center px-4">
          <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?>
        </span>
        <span class="text-xs text-stone-500 mt-1">Usuario</span>
      </div>
      <nav class="flex flex-col space-y-2 px-6 mt-6">
        <button onclick="mostrarSeccion('dashboard')" class="seccion-btn text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-home w-5"></i>
          <span>Panel Principal</span>
        </button>
        <button onclick="mostrarSeccion('perfil')" class="seccion-btn text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-user w-5"></i>
          <span>Mi Perfil</span>
        </button>
        <a href="mis_solicitudes.php" class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-clipboard-list w-5"></i>
          <span>Mis Solicitudes</span>
        </a>
        <a href="ver_carpinteros.php" class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-search w-5"></i>
          <span>Buscar Carpinteros</span>
        </a>
        <a href="ver_proyectos.php" class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-folder-open w-5"></i>
          <span>Ver Proyectos</span>
        </a>
      </nav>
    </div>
    <a href="logout.php"
       class="text-center py-5 text-stone-500 hover:text-amber-600 cursor-pointer font-medium border-t border-stone-200 flex items-center justify-center gap-2">
       <i class="fas fa-sign-out-alt"></i>
       <span>Cerrar sesión</span>
    </a>
  </aside>

  <div class="flex-1 flex flex-col h-full overflow-hidden">
    <header class="flex items-center justify-between bg-white border-b border-stone-200 px-6 h-20 shadow-sm flex-shrink-0">
      <div class="logo">
        <img src="./img/Logo de Carpintería LF.png" alt="Logo" class="h-16 w-auto">
      </div>
      <nav class="flex items-center gap-4">
        <a href="index.php" class="font-semibold text-stone-700 hover:text-amber-600">Inicio</a>
        <a href="contactanos.php" class="font-semibold text-stone-700 hover:text-amber-600">Contáctanos</a>
      </nav>
    </header>

    <!-- Contenedor con scroll para el contenido principal -->
    <div class="flex-1 overflow-y-auto">
      <main class="p-10">
        
        <!-- Dashboard Section -->
        <section id="dashboard" class="seccion">
          <h1 class="text-4xl font-bold text-stone-800 mb-2">Panel de Usuario</h1>
          <p class="text-stone-600 mb-8">Bienvenido a tu panel de control</p>
          
          <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Buscar Carpinteros -->
            <div class="bg-white p-6 rounded-xl shadow-lg border border-stone-200 hover:shadow-xl transition">
              <div class="flex items-center gap-4 mb-4">
                <div class="bg-amber-100 p-3 rounded-lg">
                  <i class="fas fa-search text-2xl text-amber-600"></i>
                </div>
                <h2 class="text-xl font-bold text-stone-800">Buscar Carpinteros</h2>
              </div>
              <p class="text-stone-600 mb-4">Encuentra al carpintero perfecto para tu proyecto.</p>
              <a href="ver_carpinteros.php" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-semibold px-6 py-3 rounded-lg shadow w-full text-center">
                Ver Carpinteros
              </a>
            </div>

            <!-- Proyectos Destacados -->
            <div class="bg-white p-6 rounded-xl shadow-lg border border-stone-200 hover:shadow-xl transition">
              <div class="flex items-center gap-4 mb-4">
                <div class="bg-blue-100 p-3 rounded-lg">
                  <i class="fas fa-star text-2xl text-blue-600"></i>
                </div>
                <h2 class="text-xl font-bold text-stone-800">Proyectos Destacados</h2>
              </div>
              <p class="text-stone-600 mb-4">Explora los mejores trabajos de nuestros carpinteros.</p>
              <a href="ver_proyectos.php" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-semibold px-6 py-3 rounded-lg shadow w-full text-center">
                Ver Proyectos
              </a>
            </div>

            <!-- Mis Solicitudes -->
            <div class="bg-white p-6 rounded-xl shadow-lg border border-stone-200 hover:shadow-xl transition">
              <div class="flex items-center gap-4 mb-4">
                <div class="bg-green-100 p-3 rounded-lg">
                  <i class="fas fa-clipboard-list text-2xl text-green-600"></i>
                </div>
                <h2 class="text-xl font-bold text-stone-800">Mis Solicitudes</h2>
              </div>
              <p class="text-stone-600 mb-4">Revisa el estado de tus proyectos solicitados.</p>
              <a href="mis_solicitudes.php" class="inline-block bg-stone-600 hover:bg-stone-700 text-white font-semibold px-6 py-3 rounded-lg shadow w-full text-center">
                Ver Solicitudes
              </a>
            </div>
          </div>

          <div class="mt-10 bg-white p-8 rounded-xl shadow-lg border border-stone-200">
            <h2 class="text-2xl font-bold text-stone-800 mb-6">Información de la Cuenta</h2>
            <div class="grid md:grid-cols-2 gap-6">
              <div class="bg-stone-50 p-4 rounded-lg border border-stone-100">
                <p class="text-xs font-bold text-stone-400 uppercase mb-1">Nombre</p>
                <p class="text-stone-800 font-medium"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'N/A'); ?></p>
              </div>
              <div class="bg-stone-50 p-4 rounded-lg border border-stone-100">
                <p class="text-xs font-bold text-stone-400 uppercase mb-1">Email</p>
                <p class="text-stone-800 font-medium"><?php echo htmlspecialchars($_SESSION['user_email'] ?? 'N/A'); ?></p>
              </div>
              <div class="bg-stone-50 p-4 rounded-lg border border-stone-100">
                <p class="text-xs font-bold text-stone-400 uppercase mb-1">Teléfono</p>
                <p class="text-stone-800 font-medium"><?php echo htmlspecialchars($_SESSION['user_phone'] ?? 'N/A'); ?></p>
              </div>
              <div class="bg-stone-50 p-4 rounded-lg border border-stone-100">
                <p class="text-xs font-bold text-stone-400 uppercase mb-1">Ciudad</p>
                <p class="text-stone-800 font-medium"><?php echo htmlspecialchars($_SESSION['user_city'] ?? 'N/A'); ?></p>
              </div>
            </div>
            <div class="mt-6 text-center">
              <button onclick="mostrarSeccion('perfil')" class="bg-amber-600 hover:bg-amber-700 text-white font-semibold px-8 py-3 rounded-lg shadow">
                <i class="fas fa-edit mr-2"></i>
                Editar Mi Perfil
              </button>
            </div>
          </div>
        </section>

        <!-- Perfil Section -->
        <section id="perfil" class="seccion hidden">
          <h1 class="text-3xl font-extrabold text-stone-800 mb-6">Editar Mi Perfil</h1>
          
          <div class="bg-white p-8 rounded-xl shadow-lg border border-stone-200 max-w-2xl">
            <form action="update_user_profile.php" method="POST" class="space-y-4">
              <div>
                <label class="block font-semibold text-stone-700 mb-2">Nombre Completo</label>
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>" required
                       class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none">
              </div>
              
              <div>
                <label class="block font-semibold text-stone-700 mb-2">Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" required
                       pattern="[a-zA-Z0-9._%+\\-]+@[a-zA-Z0-9.\\-]+\\.[a-zA-Z]{2,}" title="Debe ser un correo electrónico válido"
                       class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none">
              </div>
              
              <div>
                <label class="block font-semibold text-stone-700 mb-2">Teléfono</label>
                <input type="tel" name="telefono" value="<?php echo htmlspecialchars($_SESSION['user_phone'] ?? ''); ?>"
                       pattern="[0-9]{10}" maxlength="10" minlength="10" title="Debe contener exactamente 10 dígitos numéricos" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                       class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none">
              </div>
              
              <div>
                <label class="block font-semibold text-stone-700 mb-2">Ciudad</label>
                <input type="text" name="ciudad" value="<?php echo htmlspecialchars($_SESSION['user_city'] ?? ''); ?>"
                       class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none">
              </div>
              
              <div class="flex gap-3 pt-4">
                <button type="button" onclick="mostrarSeccion('dashboard')" 
                        class="flex-1 px-5 py-3 rounded-lg text-stone-600 hover:bg-stone-100 font-medium transition border border-stone-300">
                  Cancelar
                </button>
                <button type="submit" 
                        class="flex-1 px-5 py-3 rounded-lg bg-amber-600 text-white hover:bg-amber-700 font-bold shadow-lg transition">
                  Guardar Cambios
                </button>
              </div>
            </form>
          </div>
        </section>

      </main>
    </div>
  </div>

<script>
  function mostrarSeccion(id) {
    // Ocultar todas las secciones
    document.querySelectorAll('.seccion').forEach(sec => sec.classList.add('hidden'));
    
    // Mostrar la sección seleccionada
    document.getElementById(id).classList.remove('hidden');
    
    // Actualizar botones activos
    document.querySelectorAll('.seccion-btn').forEach(btn => {
      btn.classList.remove('bg-amber-100', 'text-amber-700');
    });
    
    // Marcar botón activo
    const activeBtn = document.querySelector(`button[onclick="mostrarSeccion('${id}')"]`);
    if (activeBtn) {
      activeBtn.classList.add('bg-amber-100', 'text-amber-700');
    }
  }

  // Mostrar dashboard por defecto
  window.addEventListener('DOMContentLoaded', () => {
    mostrarSeccion('dashboard');
  });
</script>

</body>
</html>
