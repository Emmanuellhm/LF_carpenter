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
  <?php if (isset($_GET["ok"])): ?>
  <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-green-600 text-white p-6 rounded-xl shadow-xl text-center w-80">
      <h2 class="text-xl font-bold mb-2">Â¡Registro exitoso!</h2>
      <p class="mb-4">Tu cuenta ha sido creada correctamente.</p>
      <a href="iniciar-sesion.php"
        class="block bg-white text-green-700 font-bold py-2 rounded-lg hover:bg-gray-100 mb-3">
        Iniciar sesiÃ³n
      </a>
      <p class="text-xs text-green-100">Redirigiendo automÃ¡ticamente en 5 segundos...</p>
    </div>
  </div>
  <script>
    setTimeout(() => {
      window.location.href = 'iniciar-sesion.php';
    }, 5000);
  </script>
  <?php endif; ?>
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
            <input type="text" id="nombre" name="nombre" required oninput="this.value = this.value.replace(/[^a-zA-ZÃ¡Ã©Ã­Ã³ÃºÃÃ‰ÃÃ“ÃšÃ±Ã‘\s]/g, '')"
              class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500">
          </div>

          <div>
            <label for="email" class="block font-semibold text-stone-700">Correo electrÃ³nico</label>
            <input type="email" id="email" name="email" required
              pattern="[a-zA-Z0-9._%+\\-]+@[a-zA-Z0-9.\\-]+\\.[a-zA-Z]{2,}" title="Debe ser un correo electrÃ³nico vÃ¡lido"
              class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500">
          </div>

          <button type="button" onclick="nextStep(2)"
            class="w-full mt-6 bg-amber-600 hover:bg-amber-700 text-white font-semibold py-3 px-4 rounded-lg shadow-lg">
            Siguiente
          </button>
        </div>

        <!-- Paso 2 -->
        <div class="step hidden" id="step-2">
          <h3 class="text-xl font-bold mb-4">Contacto y ubicaciÃ³n</h3>
          
          <div>
            <label for="telefono" class="block font-semibold text-stone-700">TelÃ©fono</label>
            <input type="tel" id="telefono" name="telefono" required
              pattern="[0-9]{10}" maxlength="10" minlength="10" title="Debe contener exactamente 10 dÃ­gitos numÃ©ricos" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
              class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500">
          </div>

          <div>
            <label for="ciudad" class="block font-semibold text-stone-700">Ciudad</label>
            <input type="text" id="ciudad" name="ciudad" required oninput="this.value = this.value.replace(/[^a-zA-ZÃ¡Ã©Ã­Ã³ÃºÃÃ‰ÃÃ“ÃšÃ±Ã‘\s]/g, '')"
              class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500">
          </div>

          <div class="flex justify-between mt-6">
            <button type="button" onclick="prevStep(1)"
              class="bg-stone-400 hover:bg-stone-500 text-white font-semibold py-3 px-4 rounded-lg shadow-lg">
              AtrÃ¡s
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
            <label for="password" class="block font-semibold text-stone-700">ContraseÃ±a</label>
            <input type="password" id="password" name="password" required minlength="8" title="Debe contener al menos 8 caracteres"
              class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500">
          </div>

          <div class="flex justify-between mt-6">
            <button type="button" onclick="prevStep(2)"
              class="bg-stone-400 hover:bg-stone-500 text-white font-semibold py-3 px-4 rounded-lg shadow-lg">
              AtrÃ¡s
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
    document.addEventListener('DOMContentLoaded', () => {
      const emailInput = document.querySelector('input[name="email"]');
      if (emailInput) {
        emailInput.addEventListener('blur', function() {
          if (this.value.trim() === '') return;
          
          fetch('api_check_email.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'email=' + encodeURIComponent(this.value)
          })
          .then(r => r.json())
          .then(data => {
            let errorMsg = document.getElementById('email-error');
            if (errorMsg) errorMsg.remove();

            if (data.exists) {
              this.setCustomValidity("Este correo ya estÃ¡ registrado");
              let msg = document.createElement('p');
              msg.id = 'email-error';
              msg.className = 'text-red-500 text-sm mt-1 font-medium';
              msg.innerText = 'âš ï¸ Este correo ya estÃ¡ registrado en nuestra plataforma.';
              this.parentNode.appendChild(msg);
              this.reportValidity();
            } else {
              this.setCustomValidity("");
            }
          })
          .catch(e => console.error('Error verificando correo:', e));
        });
      }
    });

    function nextStep(step) {
      let currentStep = step - 1;
      let div = document.getElementById(`step-${currentStep}`);
      if (div) {
        let inputs = div.querySelectorAll('input, select, textarea');
        for (let input of inputs) {
          if (!input.checkValidity()) {
            input.reportValidity();
            return;
          }
        }
      }
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
