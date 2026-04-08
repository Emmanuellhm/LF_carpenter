<?php
// Iniciar sesiÃ³n
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si ya hay sesiÃ³n activa, redirigir al panel correspondiente
if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: admin.php");
            exit;
        case 'carpenter':
            header("Location: panel_carpintero.php");
            exit;
        case 'user':
            header("Location: panel_usuario.php");
            exit;
    }
}

// Para leer parÃ¡metros como ?error=1 o ?pendiente=1
$error = isset($_GET['error']);
$pendiente = isset($_GET['pendiente']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar SesiÃ³n - LF Carpinter</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-to-br from-amber-50 to-stone-100 text-stone-800 font-sans">

  <!-- ðŸ”¥ MODAL ERROR (Credenciales invÃ¡lidas) -->
  <?php if ($error): ?>
  <div id="modalError"
       class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
    <div class="bg-white px-8 py-6 rounded-2xl shadow-xl max-w-sm text-center">
      <p class="text-red-600 text-xl font-bold mb-3">âŒ Error de inicio de sesiÃ³n</p>
      <p class="text-stone-700 mb-6">Credenciales incorrectas o rol invÃ¡lido.</p>
      <button onclick="document.getElementById('modalError').remove()"
              class="bg-red-600 text-white px-5 py-2 rounded-lg hover:bg-red-700">
        Cerrar
      </button>
    </div>
  </div>
  <script>
    setTimeout(() => {
      let m = document.getElementById("modalError");
      if (m) m.remove();
    }, 3000);
  </script>
  <?php endif; ?>

  <!-- âš ï¸ MODAL PENDIENTE APROBACIÃ“N -->
  <?php if ($pendiente): ?>
  <div id="modalPendiente"
       class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
    <div class="bg-white px-8 py-6 rounded-2xl shadow-xl max-w-sm text-center">
      <p class="text-amber-600 text-xl font-bold mb-3">â³ Solicitud en revisiÃ³n</p>
      <p class="text-stone-700 mb-6">Tu cuenta de carpintero aÃºn no ha sido aprobada.</p>
      <button onclick="document.getElementById('modalPendiente').remove()"
              class="bg-amber-600 text-white px-5 py-2 rounded-lg hover:bg-amber-700">
        Entendido
      </button>
    </div>
  </div>
  <script>
    setTimeout(() => {
      let m = document.getElementById("modalPendiente");
      if (m) m.remove();
    }, 3500);
  </script>
  <?php endif; ?>

  <!-- HEADER -->
  <header class="flex justify-between items-center px-8 h-20 bg-white shadow-md border-b border-stone-200">
    <div class="flex items-center">
      <img src="./img/Logo de CarpinterÃ­a LF.png" alt="LF Logo" class="h-20 w-auto">
    </div>
    <nav class="flex gap-8 items-center">
      <a href="index.php" class="font-medium text-stone-700 hover:text-amber-600">PÃ¡gina de inicio</a>
      <a href="contacto.php" class="font-medium text-stone-700 hover:text-amber-600">ContÃ¡ctanos</a>
      
      <!-- Dropdown Registrar -->
      <div class="relative" id="dropdown-registrar">
        <button onclick="toggleDropdown()" 
                class="font-semibold text-white bg-amber-700 hover:bg-amber-800 px-5 py-2 rounded-lg shadow flex items-center gap-2">
          Registrar
          <i class="fas fa-chevron-down text-sm"></i>
        </button>
        <div id="dropdown-menu" 
             class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-stone-200 overflow-hidden z-50">
          <a href="registro_U.php" 
             class="block px-4 py-3 text-stone-700 hover:bg-amber-50 hover:text-amber-700 transition flex items-center gap-3">
            <img src="./img/nueva-cuenta.png" class="w-5 h-5">
            <span class="font-medium">Usuario</span>
          </a>
          <a href="registro_C.php" 
             class="block px-4 py-3 text-stone-700 hover:bg-amber-50 hover:text-amber-700 transition flex items-center gap-3 border-t border-stone-100">
            <img src="./img/herramienta-de-martillo.png" class="w-5 h-5">
            <span class="font-medium">Carpintero</span>
          </a>
        </div>
      </div>
    </nav>
  </header>

  <!-- MAIN -->
  <main class="px-6 md:px-20 py-16">
    <h2 class="text-4xl font-extrabold text-center text-stone-800 mb-12">Iniciar SesiÃ³n</h2>

    <!-- SelecciÃ³n de rol -->
    <div class="flex justify-center gap-6 flex-wrap mb-12">
      <div class="card bg-white border border-stone-200 rounded-xl p-6 w-64 text-center cursor-pointer hover:shadow-lg transition"
           onclick="seleccionarRol(this, 'carpenter')">
        <img src="./img/herramienta-de-martillo.png" class="w-12 mx-auto mb-3">
        <h3 class="text-xl font-bold">Carpintero</h3>
        <p class="text-sm text-stone-600">Accede a tu panel de carpintero</p>
      </div>

      <div class="card bg-white border border-stone-200 rounded-xl p-6 w-64 text-center cursor-pointer hover:shadow-lg transition"
           onclick="seleccionarRol(this, 'user')">
        <img src="./img/nueva-cuenta.png" class="w-12 mx-auto mb-3">
        <h3 class="text-xl font-bold">Usuario</h3>
        <p class="text-sm text-stone-600">Consulta proyectos o contrata servicios</p>
      </div>

      <div class="card bg-white border border-stone-200 rounded-xl p-6 w-64 text-center cursor-pointer hover:shadow-lg transition"
           onclick="seleccionarRol(this, 'admin')">
        <img src="./img/administrador.png" class="w-12 mx-auto mb-3">
        <h3 class="text-xl font-bold">Administrador</h3>
        <p class="text-sm text-stone-600">Gestiona la plataforma</p>
      </div>
    </div>

    <!-- FORMULARIO -->
    <div id="form-container" class="hidden max-w-md mx-auto bg-white p-8 rounded-xl shadow-lg border border-stone-200">
      <form id="login-form" action="login.php" method="post" class="space-y-5">

        <input type="hidden" id="rol" name="rol" value="">

        <input type="email" name="email" placeholder="Correo electrÃ³nico" required
               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-amber-700">

        <input type="password" name="password" placeholder="ContraseÃ±a" required
               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-amber-700">

        <button class="w-full bg-amber-700 hover:bg-amber-800 text-white font-semibold py-3 rounded-lg shadow">
          Entrar
        </button>
        
        <div class="text-center mt-3">
          <a href="recuperar_contrasena.php" class="text-sm text-amber-700 hover:text-amber-800 hover:underline font-medium">
            Â¿Olvidaste tu contraseÃ±a?
          </a>
        </div>
      </form>
    </div>
  </main>

  <script>
    function seleccionarRol(elemento, rol) {
      document.querySelectorAll('.card').forEach(c =>
        c.classList.remove('ring-4', 'ring-amber-700')
      );
      elemento.classList.add('ring-4', 'ring-amber-700');
      document.getElementById('form-container').classList.remove('hidden');
      document.getElementById('rol').value = rol;
    }
    
    // Dropdown functionality
    function toggleDropdown() {
      const menu = document.getElementById('dropdown-menu');
      menu.classList.toggle('hidden');
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
      const dropdown = document.getElementById('dropdown-registrar');
      const menu = document.getElementById('dropdown-menu');
      if (dropdown && !dropdown.contains(event.target)) {
        menu.classList.add('hidden');
      }
    });
  </script>

  <!-- Footer -->
  <footer class="bg-amber-950 text-amber-200 py-6 mt-16">
    <div class="container mx-auto px-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div>
          <div class="flex items-center mb-4">
            <img src="./img/Logo de CarpinterÃ­a LF.png" alt="LF Logo" class="h-16 w-16">
          </div>
          <p class="text-amber-300">Creando muebles con alma. Calidad, tradiciÃ³n e innovaciÃ³n en cada proyecto.</p>
        </div>
        
        <div>
          <h4 class="text-base font-semibold text-white mb-3">Enlaces RÃ¡pidos</h4>
          <ul class="space-y-1 text-sm">
            <li><a href="index.php" class="hover:text-amber-400 transition">PÃ¡gina de inicio</a></li>
            <li><a href="contactanos.php" class="hover:text-amber-400 transition">ContÃ¡ctanos</a></li>
            <li><a href="iniciar-sesion.php" class="hover:text-amber-400 transition">Iniciar sesiÃ³n</a></li>
          </ul>
        </div>
        
        <div>
          <h4 class="text-base font-semibold text-white mb-3">Servicios</h4>
          <ul class="space-y-1 text-sm">
            <li><a href="#" class="hover:text-amber-400 transition">Muebles a Medida</a></li>
            <li><a href="#" class="hover:text-amber-400 transition">RestauraciÃ³n</a></li>
            <li><a href="#" class="hover:text-amber-400 transition">Asesoramiento</a></li>
          </ul>
        </div>
        
        <div>
          <h4 class="text-base font-semibold text-white mb-3">Contacto</h4>
          <div class="space-y-2 text-sm">
            <div class="flex items-start">
              <i class="fas fa-map-marker-alt text-amber-400 mt-1 mr-3"></i>
              <p class="text-amber-300">Taller LF Carpinter, Localidad</p>
            </div>
            <div class="flex items-start">
              <i class="fas fa-phone text-amber-400 mt-1 mr-3"></i>
              <p class="text-amber-300">+57 311 80 20 103</p>
            </div>
            <div class="flex items-start">
              <i class="fas fa-envelope text-amber-400 mt-1 mr-3"></i>
              <p class="text-amber-300">info@lfcarpinter.com</p>
            </div>
          </div>
        </div>
      </div>
      
      <div class="border-t border-amber-800 mt-6 pt-4 text-center text-amber-400">
        <p>&copy; 2025 LF CarpinterÃ­a. Todos los derechos reservados.</p>
      </div>
    </div>
  </footer>

</body>
</html>
