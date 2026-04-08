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

// Mostrar modales segÃºn query string: ?exito=1 o ?error=1
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro Carpintero - LF Carpinter</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-amber-50 to-stone-100 text-stone-800 font-sans min-h-screen flex flex-col">

  <!-- ðŸ”” MODAL Ã‰XITO -->
  <?php if (isset($_GET["exito"])): ?>
  <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-green-600 text-white p-6 rounded-xl shadow-xl text-center w-80">
      <h2 class="text-xl font-bold mb-2">Registro enviado</h2>
      <p class="mb-4">Tu solicitud fue enviada con Ã©xito.</p>
      <a href="iniciar-sesion.php"
        class="block bg-white text-green-700 font-bold py-2 rounded-lg hover:bg-gray-100">
        Continuar
      </a>
    </div>
  </div>
  <?php endif; ?>

  <!-- ðŸ”” MODAL ERROR -->
  <?php if (isset($_GET["error"])): ?>
  <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-red-600 text-white p-6 rounded-xl shadow-xl text-center w-80">
      <h2 class="text-xl font-bold mb-2">Error</h2>
      <p class="mb-4">Hubo un problema enviando tu solicitud.</p>
      <button onclick="window.history.back()"
        class="block bg-white text-red-700 font-bold py-2 rounded-lg hover:bg-gray-100">
        Volver
      </button>
    </div>
  </div>
  <?php endif; ?>


  <!-- HEADER -->
  <header class="flex justify-between items-center px-8 h-20 bg-white shadow-md border-b border-stone-200">
    <div class="flex items-center gap-4">
      <a href="index.php" class="text-stone-600 hover:text-amber-600 font-medium flex items-center gap-2">â† Volver</a>
      <img src="./img/Logo de CarpinterÃ­a LF.png" alt="LF Logo" class="h-16 w-auto">
    </div>
    <nav class="flex gap-6 items-center">
      <a href="index.php" class="font-medium text-stone-700 hover:text-amber-600">Inicio</a>
      <a href="contactanos.php" class="font-medium text-stone-700 hover:text-amber-600">ContÃ¡ctanos</a>
      <a href="iniciar-sesion.php"
        class="font-semibold text-white bg-amber-600 hover:bg-amber-700 px-5 py-2 rounded-lg shadow">
        Iniciar sesiÃ³n
      </a>
    </nav>
  </header>


  <!-- FORMULARIO -->
  <main class="flex-grow flex justify-center items-center px-6 py-12">
    <div class="bg-white p-10 rounded-2xl shadow-xl w-full max-w-lg border border-stone-200">
      <h2 class="text-3xl font-extrabold text-center text-stone-800 mb-8">Registro de Carpintero</h2>

      <form id="formCarpintero" action="solicitud_carpenter.php" method="post" enctype="multipart/form-data" novalidate onsubmit="return false;">

        <!-- PASO 1 -->
        <div class="step" id="step-1">
          <h3 class="text-xl font-bold mb-4">InformaciÃ³n personal</h3>

          <label class="block font-semibold text-stone-700">Nombre completo</label>
          <input type="text" name="nombre" required class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 mb-4">

          <label class="block font-semibold text-stone-700">Correo electrÃ³nico</label>
          <input type="email" name="email" required class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 mb-4">

          <label class="block font-semibold text-stone-700">TelÃ©fono</label>
          <input type="text" name="telefono" required class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 mb-4">

          <label class="block font-semibold text-stone-700">Ciudad</label>
          <input type="text" name="ciudad" required class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500">

          <button type="button" onclick="nextStep(2)" class="w-full mt-6 bg-amber-600 hover:bg-amber-700 text-white font-semibold py-3 px-4 rounded-lg shadow-lg">
            Siguiente
          </button>
        </div>


        <!-- PASO 2 -->
        <div class="step hidden" id="step-2">
          <h3 class="text-xl font-bold mb-4">Perfil profesional</h3>

          <label class="block font-semibold text-stone-700">Especialidad</label>
          <input type="text" name="especialidad" required class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 mb-4">

          <label class="block font-semibold text-stone-700">AÃ±os de experiencia</label>
          <input type="number" name="experiencia" min="0" class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 mb-4">

          <label class="block font-semibold text-stone-700">Hoja de vida (PDF)</label>
          <input type="file" name="hoja_vida" accept="application/pdf" class="w-full text-sm text-stone-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-600 file:text-white hover:file:bg-amber-700 cursor-pointer mb-4">

          <div class="flex justify-between mt-6">
            <button type="button" onclick="prevStep(1)" class="bg-stone-400 hover:bg-stone-500 text-white font-semibold py-3 px-4 rounded-lg shadow-lg">
              AtrÃ¡s
            </button>

            <button type="button" onclick="nextStep(3)" class="bg-amber-600 hover:bg-amber-700 text-white font-semibold py-3 px-4 rounded-lg shadow-lg">
              Siguiente
            </button>
          </div>
        </div>


        <!-- PASO 3 -->
        <div class="step hidden" id="step-3">
          <h3 class="text-xl font-bold mb-4">Seguridad</h3>

          <label class="block font-semibold text-stone-700">ContraseÃ±a</label>
          <input type="password" name="password" required class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500">

          <div class="flex justify-between mt-6">
            <button type="button" onclick="prevStep(2)" class="bg-stone-400 hover:bg-stone-500 text-white font-semibold py-3 px-4 rounded-lg shadow-lg">
              AtrÃ¡s
            </button>

            <button type="button" onclick="submitForm()" class="bg-amber-600 hover:bg-amber-700 text-white font-semibold py-3 px-4 rounded-lg shadow-lg">
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

    // Evitar enviar con Enter
    document.getElementById('formCarpintero').addEventListener('keydown', function(e) {
      if(e.key === 'Enter') e.preventDefault();
    });

    // FunciÃ³n para enviar solo con el botÃ³n
    function submitForm() {
      // ValidaciÃ³n bÃ¡sica: todos los campos requeridos del Ãºltimo paso
      const password = document.querySelector('#step-3 input[name="password"]');
      if(password.value.trim() === '') {
        alert('Debes ingresar una contraseÃ±a');
        return;
      }

      // Puedes agregar mÃ¡s validaciones por paso si quieres
      document.getElementById('formCarpintero').submit();
    }
  </script>

</body>
</html>
