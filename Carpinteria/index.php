<?php 
// Asegurarse de que la sesión esté iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LF Carpinter | Carpintería de Calidad</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-amber-50 text-stone-800 font-sans min-h-screen flex flex-col">

<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="relative bg-gradient-to-r from-amber-900 to-amber-700 text-white">
  <div class="container mx-auto px-6 py-16 flex flex-col md:flex-row md:gap-12 items-center">
    <div class="md:w-1/2 mb-10 md:mb-0">
      <small class="uppercase text-amber-300 font-semibold tracking-wide">Diseño y funcionalidad a tu medida</small>
      <h1 class="text-4xl md:text-5xl font-bold mt-3 mb-4">Carpintería de calidad para tu hogar y negocio</h1>
      <p class="text-xl mb-8 text-amber-100">
        Descubre la precisión y el detalle en cada proyecto. En <span class="font-bold text-amber-300">LF Carpinter</span>,
        transformamos tus ideas en realidad con madera de primera.
      </p>
      <div class="flex flex-wrap gap-4 mb-8">
        <span class="bg-amber-800 text-amber-100 px-4 py-2 rounded-lg shadow-sm flex items-center">
          <i class="fas fa-tools mr-2"></i> Atención personalizada
        </span>
        <span class="bg-amber-800 text-amber-100 px-4 py-2 rounded-lg shadow-sm flex items-center">
          <i class="fas fa-check-circle mr-2"></i> Garantía de calidad
        </span>
        <span class="bg-amber-800 text-amber-100 px-4 py-2 rounded-lg shadow-sm flex items-center">
          <i class="fas fa-map-marker-alt mr-2"></i> Servicio local
        </span>
      </div>

      <!-- 🔥 Registros debajo del texto -->
      <div class="flex flex-col sm:flex-row gap-6 mt-8">
        <!-- Carpintero -->
        <div class="bg-amber-800 p-6 rounded-xl shadow-lg border border-amber-700 w-full sm:w-64 hover:shadow-xl transition">
          <div class="w-12 h-12 flex items-center justify-center bg-amber-700 rounded-lg mb-3">
            <i class="fas fa-hammer text-amber-200 text-xl"></i>
          </div>
          <h3 class="text-lg font-bold text-white mb-2">Carpintero</h3>
          <p class="text-amber-100 text-sm mb-3">Únete a nuestra red de profesionales mediante una solicitud de ingreso.</p>
          <a href="registro_C.php" class="text-amber-300 font-semibold hover:underline flex items-center">
            Enviar solicitud <i class="fas fa-arrow-right ml-2"></i>
          </a>
        </div>

        <!-- Cliente -->
        <div class="bg-amber-800 p-6 rounded-xl shadow-lg border border-amber-700 w-full sm:w-64 hover:shadow-xl transition">
          <div class="w-12 h-12 flex items-center justify-center bg-amber-700 rounded-lg mb-3">
            <i class="fas fa-user text-amber-200 text-xl"></i>
          </div>
          <h3 class="text-lg font-bold text-white mb-2">Cliente</h3>
          <p class="text-amber-100 text-sm mb-3">Encuentra el mejor carpintero para tu proyecto.</p>
          <a href="registro_U.php" class="text-amber-300 font-semibold hover:underline flex items-center">
            Buscar servicios <i class="fas fa-arrow-right ml-2"></i>
          </a>
        </div>
      </div>
    </div>
    
    <!-- Columna derecha (Imágenes) -->
    <div class="md:w-1/2 md:pl-12">
      <div class="grid grid-cols-2 gap-4">
        <div class="bg-amber-800 rounded-2xl p-2 shadow-2xl transform rotate-1">
          <img src="./img/imagen1.jpeg" alt="Imagen 1" class="rounded-xl shadow-lg h-56 w-full object-cover">
        </div>
        <div class="bg-amber-800 rounded-2xl p-2 shadow-2xl transform -rotate-1 mt-8">
          <img src="./img/Sin título.jpeg" alt="Imagen 2" class="rounded-xl shadow-lg h-56 w-full object-cover">
        </div>
        <div class="bg-amber-800 rounded-2xl p-2 shadow-2xl transform -rotate-2">
          <img src="./img/imagen2.jpeg" alt="Imagen 3" class="rounded-xl shadow-lg h-56 w-full object-cover">
        </div>
        <div class="bg-amber-800 rounded-2xl p-2 shadow-2xl transform rotate-2 mt-8">
          <img src="./img/imagen3.jpeg" alt="Imagen 4" class="rounded-xl shadow-lg h-56 w-full object-cover">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Proyectos Section -->
<section class="py-20 bg-white">
  <div class="container mx-auto px-6">
    <div class="text-center mb-16">
      <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Proyectos Destacados</h2>
      <p class="text-gray-600 max-w-2xl mx-auto">Algunos ejemplos de nuestro trabajo artesanal</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Proyecto 1 -->
      <div class="bg-white rounded-xl shadow-lg border border-amber-100 overflow-hidden hover:shadow-xl transition group">
        <div class="overflow-hidden">
          <img src="img/silla_roble.jpeg" alt="Silla de Madera Roble" class="w-full h-48 object-cover group-hover:scale-110 transition duration-500">
        </div>
        <div class="p-6">
          <h3 class="text-xl font-bold text-gray-800 mb-2">Silla de Madera Roble</h3>
          <p class="text-gray-600">Diseño ergonómico con madera de roble y acabados profesionales.</p>
        </div>
      </div>
      
      <!-- Proyecto 2 -->
      <div class="bg-white rounded-xl shadow-lg border border-amber-100 overflow-hidden hover:shadow-xl transition group">
        <div class="overflow-hidden">
          <img src="img/estanteria.webp" alt="Estantería Minimalista" class="w-full h-48 object-cover group-hover:scale-110 transition duration-500">
        </div>
        <div class="p-6">
          <h3 class="text-xl font-bold text-gray-800 mb-2">Estantería Minimalista</h3>
          <p class="text-gray-600">Estilo moderno, ideal para espacios pequeños. Hecha en pino tratado.</p>
        </div>
      </div>
      
      <!-- Proyecto 3 -->
      <div class="bg-white rounded-xl shadow-lg border border-amber-100 overflow-hidden hover:shadow-xl transition group">
        <div class="overflow-hidden">
          <img src="img/comedor.jpeg" alt="Comedor para 6 personas" class="w-full h-48 object-cover group-hover:scale-110 transition duration-500">
        </div>
        <div class="p-6">
          <h3 class="text-xl font-bold text-gray-800 mb-2">Comedor para 6 personas</h3>
          <p class="text-gray-600">Mesa y sillas de nogal con barniz protector.</p>
        </div>
      </div>
      
      <!-- Proyecto 4 -->
      <div class="bg-white rounded-xl shadow-lg border border-amber-100 overflow-hidden hover:shadow-xl transition group">
        <div class="overflow-hidden">
          <img src="img/mesanoche.jpeg" alt="Mesa de noche" class="w-full h-48 object-cover group-hover:scale-110 transition duration-500">
        </div>
        <div class="p-6">
          <h3 class="text-xl font-bold text-gray-800 mb-2">Mesa de noche</h3>
          <p class="text-gray-600">Diseño clásico, barnizada a mano. Ideal para dormitorios.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Perfiles Section -->
<section class="py-20 bg-amber-100">
  <div class="container mx-auto px-6">
    <div class="text-center mb-16">
      <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Conoce a Nuestros Carpinteros</h2>
      <p class="text-gray-600 max-w-2xl mx-auto">Profesionales dedicados a crear muebles únicos con pasión y experiencia</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
      <!-- Carpintero 1 -->
      <div onclick="window.location.href='cliente2.php'" class="bg-white rounded-xl shadow-lg border border-amber-100 overflow-hidden hover:shadow-xl transition cursor-pointer group">
        <div class="overflow-hidden">
          <img src="img/fotoP.jpg" alt="Juan Pérez" class="w-full h-48 object-cover group-hover:scale-110 transition duration-500">
        </div>
        <div class="p-6">
          <h3 class="text-xl font-bold text-gray-800 mb-2">Juan Pérez</h3>
          <p class="text-gray-600">Especialista en muebles a medida con 10 años de experiencia.</p>
          <div class="mt-4 flex items-center text-amber-600">
            <span>Ver perfil</span>
            <i class="fas fa-arrow-right ml-2"></i>
          </div>
        </div>
      </div>
      
      <!-- Carpintero 2 -->
      <div class="bg-white rounded-xl shadow-lg border border-amber-100 overflow-hidden hover:shadow-xl transition cursor-pointer group">
        <div class="overflow-hidden">
          <img src="img/fotoP.jpg" alt="Laura Gómez" class="w-full h-48 object-cover group-hover:scale-110 transition duration-500">
        </div>
        <div class="p-6">
          <h3 class="text-xl font-bold text-gray-800 mb-2">Laura Gómez</h3>
          <p class="text-gray-600">Experta en restauración de muebles antiguos, atención personalizada.</p>
          <div class="mt-4 flex items-center text-amber-600">
            <span>Ver perfil</span>
            <i class="fas fa-arrow-right ml-2"></i>
          </div>
        </div>
      </div>
      
      <!-- Carpintero 3 -->
      <div class="bg-white rounded-xl shadow-lg border border-amber-100 overflow-hidden hover:shadow-xl transition cursor-pointer group">
        <div class="overflow-hidden">
          <img src="img/fotoP.jpg" alt="Carlos Ruiz" class="w-full h-48 object-cover group-hover:scale-110 transition duration-500">
        </div>
        <div class="p-6">
          <h3 class="text-xl font-bold text-gray-800 mb-2">Carlos Ruiz</h3>
          <p class="text-gray-600">Diseño moderno y funcional. Proyectos innovadores y a buen precio.</p>
          <div class="mt-4 flex items-center text-amber-600">
            <span>Ver perfil</span>
            <i class="fas fa-arrow-right ml-2"></i>
          </div>
        </div>
      </div>
      
      <!-- Carpintero 4 -->
      <div class="bg-white rounded-xl shadow-lg border border-amber-100 overflow-hidden hover:shadow-xl transition cursor-pointer group">
        <div class="overflow-hidden">
          <img src="img/fotoP.jpg" alt="Ana Torres" class="w-full h-48 object-cover group-hover:scale-110 transition duration-500">
        </div>
        <div class="p-6">
          <h3 class="text-xl font-bold text-gray-800 mb-2">Ana Torres</h3>
          <p class="text-gray-600">Trabaja con maderas recicladas y acabados sostenibles.</p>
          <div class="mt-4 flex items-center text-amber-600">
            <span>Ver perfil</span>
            <i class="fas fa-arrow-right ml-2"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="bg-amber-950 text-amber-200 py-6">
  <div class="container mx-auto px-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div>
        <div class="flex items-center mb-4">
          <img src="./img/Logo de Carpintería LF.png" alt="LF Logo" class="h-16 w-16">
        </div>
        <p class="text-amber-300">Creando muebles con alma. Calidad, tradición e innovación en cada proyecto.</p>
      </div>
      
      <div>
        <h4 class="text-base font-semibold text-white mb-3">Enlaces Rápidos</h4>
        <ul class="space-y-1 text-sm">
          <li><a href="index.php" class="hover:text-amber-400 transition">Página de inicio</a></li>
          <li><a href="contactanos.php" class="hover:text-amber-400 transition">Contáctanos</a></li>
          <li><a href="iniciar-seccion.php" class="hover:text-amber-400 transition">Iniciar sesión</a></li>
        </ul>
      </div>
      
      <div>
        <h4 class="text-base font-semibold text-white mb-3">Servicios</h4>
        <ul class="space-y-1 text-sm">
          <li><a href="#" class="hover:text-amber-400 transition">Muebles a Medida</a></li>
          <li><a href="#" class="hover:text-amber-400 transition">Restauración</a></li>
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
      <p>&copy; 2025 LF Carpintería. Todos los derechos reservados.</p>
    </div>
  </div>
</footer>

</body>
</html>
