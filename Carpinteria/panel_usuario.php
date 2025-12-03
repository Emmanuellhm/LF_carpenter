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
<title>Panel Usuario - LF Carpintería</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="min-h-screen bg-gradient-to-br from-amber-50 to-stone-100 flex flex-col">

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

<header class="bg-white shadow-md p-5 flex justify-between items-center border-b border-stone-200">
  <div class="flex items-center gap-4">
    <img src="img/Logo de Carpintería LF.png" alt="Logo" class="h-12 w-auto">
    <h2 class="text-xl font-bold text-stone-800">Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?></h2>
  </div>
  <div class="flex gap-4">
    <a href="index.php" class="text-amber-600 font-semibold hover:text-amber-700">Inicio</a>
    <a href="logout.php" class="text-red-600 font-semibold hover:text-red-700">Cerrar sesión</a>
  </div>
</header>

<main class="p-10 max-w-6xl mx-auto">
  <h1 class="text-4xl font-bold text-stone-800 mb-6">Panel de Usuario</h1>
  
  <div class="grid md:grid-cols-2 gap-6">
    <!-- Buscar Carpinteros -->
    <div class="bg-white p-6 rounded-xl shadow-lg border border-stone-200">
      <h2 class="text-2xl font-bold text-stone-800 mb-4">🔍 Buscar Carpinteros</h2>
      <p class="text-stone-600 mb-4">Encuentra al carpintero perfecto para tu proyecto.</p>
      <a href="ver_carpinteros.php" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-semibold px-6 py-3 rounded-lg shadow">
        Ver Carpinteros
      </a>
    </div>

    <!-- Proyectos Destacados -->
    <div class="bg-white p-6 rounded-xl shadow-lg border border-stone-200">
      <h2 class="text-2xl font-bold text-stone-800 mb-4">✨ Proyectos Destacados</h2>
      <p class="text-stone-600 mb-4">Explora los mejores trabajos de nuestros carpinteros.</p>
      <a href="ver_proyectos.php" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-semibold px-6 py-3 rounded-lg shadow">
        Ver Proyectos
      </a>
    </div>

    <!-- Mi Perfil -->
    <div class="bg-white p-6 rounded-xl shadow-lg border border-stone-200">
      <h2 class="text-2xl font-bold text-stone-800 mb-4">👤 Mi Perfil</h2>
      <p class="text-stone-600 mb-4">Actualiza tu información personal.</p>
      <button onclick="abrirEditarPerfil()" class="bg-stone-600 hover:bg-stone-700 text-white font-semibold px-6 py-3 rounded-lg shadow">
        Editar Perfil
      </button>
    </div>

    <!-- Mis Solicitudes -->
    <div class="bg-white p-6 rounded-xl shadow-lg border border-stone-200">
      <h2 class="text-2xl font-bold text-stone-800 mb-4">📋 Mis Solicitudes</h2>
      <p class="text-stone-600 mb-4">Revisa el estado de tus proyectos solicitados.</p>
      <button class="bg-stone-600 hover:bg-stone-700 text-white font-semibold px-6 py-3 rounded-lg shadow">
        Ver Solicitudes
      </button>
    </div>
  </div>

  <div class="mt-10 bg-white p-8 rounded-xl shadow-lg border border-stone-200">
    <h2 class="text-2xl font-bold text-stone-800 mb-4">Información de la Cuenta</h2>
    <div class="grid md:grid-cols-2 gap-4">
      <div>
        <p class="text-stone-600"><strong>Nombre:</strong> <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'N/A'); ?></p>
      </div>
      <div>
        <p class="text-stone-600"><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email'] ?? 'N/A'); ?></p>
      </div>
      <div>
        <p class="text-stone-600"><strong>Teléfono:</strong> <?php echo htmlspecialchars($_SESSION['user_phone'] ?? 'N/A'); ?></p>
      </div>
      <div>
        <p class="text-stone-600"><strong>Ciudad:</strong> <?php echo htmlspecialchars($_SESSION['user_city'] ?? 'N/A'); ?></p>
      </div>
    </div>
  </div>
</main>

<!-- Modal Editar Perfil -->
<div id="modal-editar-perfil" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
  <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl p-8 relative">
    <button onclick="cerrarEditarPerfil()" class="absolute top-4 right-4 text-stone-500 hover:text-stone-800">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
    
    <h2 class="text-2xl font-bold text-stone-800 mb-6">Editar Perfil</h2>
    
    <form action="update_user_profile.php" method="POST" class="space-y-4">
      <div>
        <label class="block font-semibold text-stone-700 mb-2">Nombre Completo</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>" required
               class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-700">
      </div>
      
      <div>
        <label class="block font-semibold text-stone-700 mb-2">Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" required
               class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-700">
      </div>
      
      <div>
        <label class="block font-semibold text-stone-700 mb-2">Teléfono</label>
        <input type="text" name="telefono" value="<?php echo htmlspecialchars($_SESSION['user_phone'] ?? ''); ?>"
               class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-700">
      </div>
      
      <div>
        <label class="block font-semibold text-stone-700 mb-2">Ciudad</label>
        <input type="text" name="ciudad" value="<?php echo htmlspecialchars($_SESSION['user_city'] ?? ''); ?>"
               class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-700">
      </div>
      
      <div class="flex gap-3 mt-6">
        <button type="button" onclick="cerrarEditarPerfil()" 
                class="flex-1 px-5 py-3 rounded-lg text-stone-600 hover:bg-stone-100 font-medium transition border border-stone-300">
          Cancelar
        </button>
        <button type="submit" 
                class="flex-1 px-5 py-3 rounded-lg bg-amber-700 text-white hover:bg-amber-800 font-bold shadow-lg transition">
          Guardar Cambios
        </button>
      </div>
    </form>
  </div>
</div>

<footer class="mt-10 bg-white py-4 text-center text-stone-600 border-t border-stone-200">
  <p>&copy; 2025 LF Carpintería. Todos los derechos reservados.</p>
</footer>

</body>
</html>
