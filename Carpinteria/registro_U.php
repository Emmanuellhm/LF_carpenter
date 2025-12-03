<?php
// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si ya hay sesión activa, redirigir al panel correspondiente
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
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro Usuario - LF Carpinter</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-amber-50 to-stone-100 text-stone-800 font-sans min-h-screen flex flex-col">

  <!-- Header -->
  <header class="flex justify-between items-center px-8 h-20 bg-white shadow-md border-b border-stone-200">
    <div class="flex items-center gap-4">
      <a href="index.php" class="text-stone-600 hover:text-amber-600 font-medium flex items-center gap-2">← Volver</a>
      <img src="./img/Logo de Carpintería LF.png" alt="LF Logo" class="h-16 w-auto">
    </div>
    <nav class="flex gap-6 items-center">
      <a href="index.php" class="font-medium text-stone-700 hover:text-amber-600">Inicio</a>
      <a href="contactanos.php" class="font-medium text-stone-700 hover:text-amber-600">Contáctanos</a>
      <a href="iniciar-seccion.php"
        class="font-semibold text-white bg-amber-600 hover:bg-amber-700 px-5 py-2 rounded-lg shadow">
        Iniciar sesión
      </a>
    </nav>
  </header>

  <!-- Formulario -->
  <main class="flex-grow flex justify-center items-center px-6 py-12">
    <div class="bg-white p-10 rounded-2xl shadow-xl w-full max-w-lg border border-stone-200">
      <h2 class="text-3xl font-extrabold text-center text-stone-800 mb-8">Registro de Usuario</h2>

      <form id="registroUsuario" action="register_user.php" method="post" class="space-y-6">
        
        <!-- Paso 1 -->
        <div class="step" id="step-1">
          <h3 class="text-xl font-bold mb-4">Datos personales</h3>
          
          <div>
            <label for="nombre" class="block font-semibold text-stone-700">Nombre completo</label>
            <input type="text" id="nombre" name="nombre" required
              class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500">
          </div>

          <div>
            <label for="email" class="block font-semibold text-stone-700">Correo electrónico</label>
            <input type="email" id="email" name="email" required
              class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500">
          </div>

          <button type="button" onclick="nextStep(2)"
            class="w-full mt-6 bg-amber-600 hover:bg-amber-700 text-white font-semibold py-3 px-4 rounded-lg shadow-lg">
            Siguiente
          </button>
        </div>

        <!-- Paso 2 -->
        <div class="step hidden" id="step-2">
          <h3 class="text-xl font-bold mb-4">Contacto y ubicación</h3>
          
          <div>
            <label for="telefono" class="block font-semibold text-stone-700">Teléfono</label>
            <input type="text" id="telefono" name="telefono" required
              class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500">
          </div>

          <div>
            <label for="ciudad" class="block font-semibold text-stone-700">Ciudad</label>
            <input type="text" id="ciudad" name="ciudad" required
              class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500">
          </div>

          <div class="flex justify-between mt-6">
            <button type="button" onclick="prevStep(1)"
              class="bg-stone-400 hover:bg-stone-500 text-white font-semibold py-3 px-4 rounded-lg shadow-lg">
              Atrás
            </button>

            <button type="button" onclick="nextStep(3)"
              class="bg-amber-600 hover:bg-amber-700 text-white font-semibold py-3 px-4 rounded-lg shadow-lg">
              Siguiente
            </button>
          </div>
        </div>

        <!-- Paso 3 -->
        <div class="step hidden" id="step-3">
          <h3 class="text-xl font-bold mb-4">Seguridad</h3>
          
          <div>
            <label for="password" class="block font-semibold text-stone-700">Contraseña</label>
            <input type="password" id="password" name="password" required
              class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500">
          </div>

          <div class="flex justify-between mt-6">
            <button type="button" onclick="prevStep(2)"
              class="bg-stone-400 hover:bg-stone-500 text-white font-semibold py-3 px-4 rounded-lg shadow-lg">
              Atrás
            </button>

            <button type="submit"
              class="bg-amber-600 hover:bg-amber-700 text-white font-semibold py-3 px-4 rounded-lg shadow-lg">
              Registrarse
            </button>
          </div>
        </div>

      </form>
    </div>
  </main>

  <script>
    function nextStep(step) {
      document.querySelectorAll('.step').forEach(s => s.classList.add('hidden'));
      document.getElementById(`step-${step}`).classList.remove('hidden');
    }

    function prevStep(step) {
      document.querySelectorAll('.step').forEach(s => s.classList.add('hidden'));
      document.getElementById(`step-${step}`).classList.remove('hidden');
    }
  </script>

</body>
</html>
