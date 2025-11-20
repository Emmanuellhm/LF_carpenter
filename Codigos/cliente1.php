<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  header("Location: iniciar-seccion.html");
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LF Carpinter - Perfil Cliente</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-screen bg-gradient-to-br from-amber-50 to-stone-100">

<!-- Sidebar -->
<aside class="w-64 bg-white shadow-xl flex flex-col justify-between border-r border-stone-200">
  <div>
    <div class="flex flex-col items-center py-8 border-b border-stone-200">
      <img src="img/fotoP.jpg" alt="Foto Perfil"
           class="w-28 h-28 rounded-full border-4 border-amber-600 object-cover shadow-md">
      <span class="mt-3 font-bold text-stone-800 text-lg">
        <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?>
      </span>
    </div>
    <nav class="flex flex-col space-y-2 px-6 mt-6">
      <a href="cliente1.php" class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-2 font-medium">Perfil</a>
      <a href="historial.php" class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-2 font-medium">Historial Contrataciones</a>
      <a href="solicitudes.php" class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-2 font-medium">Solicitudes</a>
    </nav>
  </div>
  <a href="logout.php" class="text-center py-5 text-stone-500 hover:text-amber-600 font-medium border-t border-stone-200">
    Cerrar sesión
  </a>
</aside>

<div class="flex-1 flex flex-col">
  <header class="flex items-center justify-between bg-white border-b border-stone-200 px-6 h-20 shadow-sm">
    <div class="logo">
      <img src="img/Logo de Carpintería LF.png" alt="Logo" class="h-16 w-auto">
    </div>
    <nav>
      <a href="contactanos.html" class="font-semibold text-stone-700 hover:text-amber-600">Contáctanos</a>
    </nav>
  </header>

  <main class="p-10">
    <h1 class="text-3xl font-extrabold text-stone-800 mb-6">Configura tu perfil</h1>
    <div class="bg-white p-8 rounded-xl shadow-lg max-w-lg border border-stone-200">
      <form class="space-y-5">
        <div>
          <label for="nombre" class="block font-semibold text-stone-700">Nombre</label>
          <input type="text" id="nombre" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>"
                 class="w-full px-4 py-2 border border-stone-300 rounded-lg">
        </div>
        <div>
          <label for="correo" class="block font-semibold text-stone-700">Correo</label>
          <input type="email" id="correo" value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>"
                 class="w-full px-4 py-2 border border-stone-300 rounded-lg">
        </div>
        <div>
          <label for="ubicacion" class="block font-semibold text-stone-700">Ubicación</label>
          <input type="text" id="ubicacion" value="Medellín, Colombia"
                 class="w-full px-4 py-2 border border-stone-300 rounded-lg">
        </div>
        <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white py-3 rounded-lg font-semibold shadow">
          Guardar cambios
        </button>
      </form>
    </div>
  </main>
</div>

</body>
</html>
