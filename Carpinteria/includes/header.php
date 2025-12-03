<?php
// Asegurarse de que la sesión esté iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si hay sesión activa
$isLoggedIn = isset($_SESSION['role']) && isset($_SESSION['user_name']);
$userRole = $_SESSION['role'] ?? null;
$userName = $_SESSION['user_name'] ?? '';

// Determinar el enlace del panel según el rol
$panelLink = '';
$panelText = 'Mi Panel';
if ($isLoggedIn) {
    switch ($userRole) {
        case 'admin':
            $panelLink = 'admin.php';
            $panelText = 'Panel Admin';
            break;
        case 'carpenter':
            $panelLink = 'panel_carpintero.php';
            $panelText = 'Mi Panel';
            break;
        case 'user':
            $panelLink = 'panel_usuario.php';
            $panelText = 'Mi Panel';
            break;
    }
}
?>

<!-- Header -->
<header class="bg-white shadow-lg sticky top-0 z-50">
  <div class="container mx-auto px-6 py-2">
    <div class="flex justify-between items-center md:grid md:grid-cols-3">
      <!-- Logo (Izquierda) -->
      <div class="flex items-center justify-self-start">
        <a href="index.php">
          <img src="./img/Logo de Carpintería LF.png" alt="LF Logo" class="h-16 w-16">
        </a>
      </div>
      
      <!-- Navigation Links (Centro absoluto) -->
      <div class="hidden md:flex justify-center justify-self-center">
        <div class="flex space-x-8">
          <a href="index.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'text-amber-700 font-medium' : 'text-gray-600 hover:text-amber-700'; ?> transition">Página de inicio</a>
          <a href="contactanos.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'contactanos.php') ? 'text-amber-700 font-medium' : 'text-gray-600 hover:text-amber-700'; ?> transition">Contáctanos</a>
          <?php if ($isLoggedIn && $userRole === 'user'): ?>
          <a href="ver_carpinteros.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'ver_carpinteros.php') ? 'text-amber-700 font-medium' : 'text-gray-600 hover:text-amber-700'; ?> transition">Ver Carpinteros</a>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- CTA Buttons (Derecha) -->
      <div class="flex items-center justify-self-end gap-3">
        <?php if ($isLoggedIn): ?>
          <!-- Usuario con sesión activa -->
          <div class="flex items-center gap-3">
            <span class="text-gray-700 font-medium hidden md:inline">Hola, <?php echo htmlspecialchars($userName); ?></span>
            <a href="<?php echo $panelLink; ?>" class="bg-amber-700 text-white px-6 py-2 rounded-lg hover:bg-amber-800 transition shadow-md">
              <?php echo $panelText; ?>
            </a>
            <a href="logout.php" class="text-gray-600 hover:text-red-600 transition font-medium">
              <i class="fas fa-sign-out-alt"></i> Cerrar sesión
            </a>
          </div>
        <?php else: ?>
          <!-- Usuario sin sesión -->
          <a href="iniciar-seccion.php" class="text-gray-600 hover:text-amber-700 transition font-medium">
            Iniciar sesión
          </a>
          
          <!-- Dropdown Registrar -->
          <div class="relative" id="dropdown-registrar">
            <button onclick="toggleDropdown()" 
                    class="bg-amber-700 text-white px-6 py-2 rounded-lg hover:bg-amber-800 transition shadow-md flex items-center gap-2">
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
        <?php endif; ?>
      </div>
    </div>
  </div>
</header>

<?php if (!$isLoggedIn): ?>
<script>
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
<?php endif; ?>
