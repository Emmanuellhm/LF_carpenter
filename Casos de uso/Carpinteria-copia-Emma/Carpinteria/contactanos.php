<?php 
// Asegurarse de que la sesiÃ³n estÃ© iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ContÃ¡ctanos - LF Carpinter</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-amber-50 text-stone-800 font-sans min-h-screen flex flex-col">

  <?php include 'includes/header.php'; ?>

  <!-- Contact Section -->
  <main class="px-6 md:px-20 py-16 flex-grow">
    <h1 class="text-4xl font-extrabold text-center text-gray-800 mb-12">ContÃ¡ctanos</h1>

    <div class="grid md:grid-cols-2 gap-12 max-w-6xl mx-auto">
      <!-- Formulario -->
      <div class="bg-white p-8 rounded-xl shadow-lg border border-amber-100">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">EnvÃ­anos un mensaje</h2>
        <form class="space-y-5">
          <div>
            <label for="nombre" class="block font-medium text-gray-700 mb-1">Nombre</label>
            <input type="text" id="nombre" placeholder="Tu nombre"
              class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-700 focus:border-amber-700">
          </div>
          <div>
            <label for="correo" class="block font-medium text-gray-700 mb-1">Correo</label>
            <input type="email" id="correo" placeholder="tucorreo@example.com"
              class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-700 focus:border-amber-700">
          </div>
          <div>
            <label for="mensaje" class="block font-medium text-gray-700 mb-1">Mensaje</label>
            <textarea id="mensaje" rows="4" placeholder="Escribe tu mensaje aquÃ­..."
              class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-700 focus:border-amber-700"></textarea>
          </div>
          <button type="submit"
            class="w-full bg-amber-700 hover:bg-amber-800 text-white font-semibold py-3 rounded-lg shadow-md transition">Enviar
            mensaje</button>
        </form>
      </div>

      <!-- InformaciÃ³n de contacto -->
      <div class="flex flex-col justify-center bg-gradient-to-br from-amber-900 to-amber-700 text-white rounded-xl p-8 shadow-lg">
        <h2 class="text-2xl font-bold mb-6">InformaciÃ³n de contacto</h2>
        <p class="text-amber-100 mb-6">Puedes comunicarte con nosotros por los siguientes medios:</p>
        <ul class="space-y-4">
          <li class="flex items-start">
            <i class="fas fa-map-marker-alt text-amber-300 mt-1 mr-3"></i>
            <span><strong>DirecciÃ³n:</strong> Calle 123, MedellÃ­n, Colombia</span>
          </li>
          <li class="flex items-start">
            <i class="fas fa-phone text-amber-300 mt-1 mr-3"></i>
            <span><strong>TelÃ©fono:</strong> +57 300 123 4567</span>
          </li>
          <li class="flex items-start">
            <i class="fas fa-envelope text-amber-300 mt-1 mr-3"></i>
            <span><strong>Correo:</strong> lfcarpinter@gmail.com</span>
          </li>
        </ul>
        <div class="mt-6">
          <iframe class="w-full h-60 rounded-lg border border-amber-800"
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3976.954471310789!2d-75.57982!3d6.244203!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e4429a69b0d9e23%3A0xabc123!2sMedell%C3%ADn!5e0!3m2!1ses!2sco!4v1694039692923"
            allowfullscreen="" loading="lazy"></iframe>
        </div>
      </div>
    </div>
  </main>

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
