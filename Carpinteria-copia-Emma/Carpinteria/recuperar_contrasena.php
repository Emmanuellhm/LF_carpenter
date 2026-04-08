<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperar ContraseÃ±a - LF Carpinter</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-amber-50 text-stone-800 font-sans min-h-screen flex flex-col">

  <!-- Header -->
  <header class="bg-white shadow-lg sticky top-0 z-50">
    <div class="container mx-auto px-6 py-2">
      <div class="flex justify-between items-center md:grid md:grid-cols-3">
        <!-- Logo (Izquierda) -->
        <div class="flex items-center justify-self-start"><img src="./img/Logo de CarpinterÃ­a LF.png" alt="LF Logo" class="h-16 w-16"></div>
        
        <!-- Navigation Links (Centro absoluto) -->
        <div class="hidden md:flex justify-center justify-self-center">
          <div class="flex space-x-8">
            <a href="index.php" class="text-gray-600 hover:text-amber-700 transition font-medium">PÃ¡gina de inicio</a>
            <a href="contactanos.php" class="text-gray-600 hover:text-amber-700 transition font-medium">ContÃ¡ctanos</a>
          </div>
        </div>
        
        <!-- CTA Button (Derecha) -->
        <div class="flex items-center justify-self-end">
          <a href="iniciar-sesion.php" class="bg-amber-700 text-white px-6 py-2 rounded-lg hover:bg-amber-800 transition shadow-md">
            Iniciar sesiÃ³n
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- Main -->
  <main class="px-6 md:px-20 py-16 flex-grow">
    <div class="max-w-md mx-auto">
      <!-- Encabezado -->
      <div class="text-center mb-8">
        <h2 class="text-3xl font-extrabold text-gray-800 mb-2">Recuperar ContraseÃ±a</h2>
        <p class="text-gray-600">Ingresa tu correo electrÃ³nico y te enviaremos un enlace para restablecer tu contraseÃ±a</p>
      </div>

      <!-- Formulario -->
      <div class="bg-white p-8 rounded-xl shadow-lg border border-amber-100">
        <form id="recuperar-form" class="space-y-6">
          
          <!-- Campo de correo -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Correo ElectrÃ³nico</label>
            <input type="email" id="email" name="email" placeholder="tu@correo.com" required
              class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-700 focus:border-amber-700 transition">
          </div>

          <!-- Selector de rol -->
          <div>
            <label for="rol" class="block text-sm font-medium text-gray-700 mb-2">Â¿CuÃ¡l es tu rol?</label>
            <select id="rol" name="rol" required
              class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-700 focus:border-amber-700 transition">
              <option value="">Selecciona tu rol</option>
              <option value="usuario">Usuario</option>
              <option value="carpintero">Carpintero</option>
            </select>
          </div>

          <!-- BotÃ³n de envÃ­o -->
          <button type="submit"
            class="w-full bg-amber-700 hover:bg-amber-800 text-white font-semibold py-3 rounded-lg shadow-md transition duration-200">
            Enviar Enlace de RecuperaciÃ³n
          </button>
        </form>

        <!-- Enlace de regreso -->
        <div class="text-center mt-6">
          <p class="text-gray-600">
            Â¿Recordaste tu contraseÃ±a?
            <a href="iniciar-sesion.php" class="text-amber-700 hover:text-amber-800 font-medium">Inicia sesiÃ³n aquÃ­</a>
          </p>
        </div>
      </div>

      <!-- SecciÃ³n de informaciÃ³n -->
      <div class="mt-8 bg-gradient-to-br from-amber-900 to-amber-700 text-white rounded-lg p-6 shadow-lg">
        <h3 class="font-semibold text-amber-100 mb-3 flex items-center">
          <i class="fas fa-info-circle mr-2"></i> Â¿CÃ³mo funciona?
        </h3>
        <ul class="text-sm text-amber-100 space-y-2">
          <li class="flex items-start gap-3">
            <span class="font-bold text-amber-300">1.</span>
            <span>Ingresa tu correo electrÃ³nico y selecciona tu rol</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="font-bold text-amber-300">2.</span>
            <span>RecibirÃ¡s un email con un enlace seguro</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="font-bold text-amber-300">3.</span>
            <span>Haz clic en el enlace para crear una nueva contraseÃ±a</span>
          </li>
        </ul>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-amber-950 text-amber-200 py-6 mt-16">
    <div class="container mx-auto px-6">
      <div class="border-t border-amber-800 pt-8 text-center text-amber-400">
        <p>&copy; 2025 LF CarpinterÃ­a. Todos los derechos reservados.</p>
      </div>
    </div>
  </footer>

  <script>
    document.getElementById('recuperar-form').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const email = document.getElementById('email').value;
      const rol = document.getElementById('rol').value;
      
      // AquÃ­ irÃ­a la lÃ³gica para enviar el formulario a un servidor
      // Por ahora mostramos un mensaje de Ã©xito
      alert(`Se enviÃ³ un enlace de recuperaciÃ³n a: ${email}\n\nRevisa tu correo en los prÃ³ximos 10 minutos.`);
      
      // Opcional: redirigir despuÃ©s de 2 segundos
      // setTimeout(() => window.location.href = 'iniciar-sesion.php', 2000);
    });
  </script>

</body>
</html>
