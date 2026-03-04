<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="LF Carpinter - Plataforma de contratación de carpinteros profesionales. Encuentra los mejores carpinteros verificados en tu zona para proyectos de carpintería a medida.">
  <meta name="keywords" content="carpintería, carpinteros, muebles a medida, proyectos de madera, restauración de muebles,LF Carpinter">
  <meta name="author" content="LF Carpinter">
  <meta property="og:title" content="LF Carpinter - Carpintería de Calidad">
  <meta property="og:description" content="Encuentra carpinteros profesionales verificados para tus proyectos de carpintería">
  <meta property="og:type" content="website">
  <title>Sobre Nosotros - LF Carpinter</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-amber-50 text-stone-800 font-sans min-h-screen flex flex-col">

<?php include 'includes/header.php'; ?>

<main class="flex-grow">
  <!-- Hero Section -->
  <section class="relative bg-gradient-to-r from-amber-900 to-amber-700 text-white py-20">
    <div class="container mx-auto px-6 text-center">
      <h1 class="text-4xl md:text-5xl font-bold mb-4">Sobre Nosotros</h1>
      <p class="text-xl text-amber-100 max-w-2xl mx-auto">Conoce nuestra historia y el equipo detrás de LF Carpinter</p>
    </div>
  </section>

  <!-- Historia -->
  <section class="py-16 bg-white">
    <div class="container mx-auto px-6 max-w-4xl">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-800 mb-4">Nuestra Historia</h2>
        <div class="w-24 h-1 bg-amber-700 mx-auto"></div>
      </div>
      <div class="prose max-w-none text-gray-600 space-y-4">
        <p class="text-lg"><strong>LF Carpinter</strong> nació con la visión de conectar a personas que necesitan servicios de carpintería con profesionales calificados y confiables.</p>
        <p>Nuestra plataforma surge de la necesidad de facilitar el proceso de contratación de servicios de carpintería, garantizando calidad, transparencia y seguridad tanto para clientes como para profesionales.</p>
        <p>Creemos en el valor del trabajo artesanal y en la importancia de preservar oficios tradicionales como la carpintería, al mismo tiempo que abrazamos la tecnología para hacer más eficiente la conexión entre oferta y demanda.</p>
      </div>
    </div>
  </section>

  <!-- Misión y Visión -->
  <section class="py-16 bg-amber-100">
    <div class="container mx-auto px-6">
      <div class="grid md:grid-cols-2 gap-12 max-w-5xl mx-auto">
        <div class="bg-white p-8 rounded-xl shadow-lg border border-amber-200">
          <div class="w-16 h-16 bg-amber-700 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-bullseye text-white text-2xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-gray-800 mb-4">Misión</h3>
          <p class="text-gray-600">Facilitar la contratación de servicios de carpintería de calidad, conectando a clientes con carpinteros profesionales verificados, garantizando transparencia, seguridad y satisfacción en cada proyecto.</p>
        </div>
        <div class="bg-white p-8 rounded-xl shadow-lg border border-amber-200">
          <div class="w-16 h-16 bg-amber-700 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-eye text-white text-2xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-gray-800 mb-4">Visión</h3>
          <p class="text-gray-600">Ser la plataforma líder en Colombia para la contratación de servicios de carpintería, promoviendo el trabajo artesanal y facilitando el crecimiento profesional de los carpinteros asociados.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Valores -->
  <section class="py-16 bg-white">
    <div class="container mx-auto px-6">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-800 mb-4">Nuestros Valores</h2>
        <div class="w-24 h-1 bg-amber-700 mx-auto"></div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
        <div class="text-center p-6">
          <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-handshake text-amber-700 text-2xl"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-800 mb-2">Confianza</h3>
          <p class="text-gray-600">Verificamos cada carpintero para garantizar profesionales confiables</p>
        </div>
        <div class="text-center p-6">
          <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-award text-amber-700 text-2xl"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-800 mb-2">Calidad</h3>
          <p class="text-gray-600">Promovemos el trabajo bien hecho y la excelencia artesanal</p>
        </div>
        <div class="text-center p-6">
          <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-users text-amber-700 text-2xl"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-800 mb-2">Comunidad</h3>
          <p class="text-gray-600">Construimos relaciones duraderas entre clientes y carpinteros</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Equipo -->
  <section class="py-16 bg-amber-50">
    <div class="container mx-auto px-6">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-800 mb-4">Nuestro Equipo</h2>
        <p class="text-gray-600 max-w-2xl mx-auto">Somos un equipo comprometido con el desarrollo de soluciones tecnológicas para el sector de la carpintería</p>
        <div class="w-24 h-1 bg-amber-700 mx-auto mt-4"></div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
        <div class="bg-white p-6 rounded-xl shadow-lg text-center">
          <div class="w-24 h-24 bg-amber-200 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-user-tie text-amber-700 text-3xl"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-800 mb-1">Lilliana Fernández</h3>
          <p class="text-amber-700 font-medium mb-2">Fundadora y Desarrolladora</p>
          <p class="text-gray-500 text-sm">Responsable del desarrollo y mantenimiento de la plataforma</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg text-center">
          <div class="w-24 h-24 bg-amber-200 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-user-shield text-amber-700 text-3xl"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-800 mb-1">Equipo de Administración</h3>
          <p class="text-amber-700 font-medium mb-2">Gestión y Verificación</p>
          <p class="text-gray-500 text-sm">Encargado de validar y aprobar perfiles de carpinteros</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg text-center">
          <div class="w-24 h-24 bg-amber-200 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-tools text-amber-700 text-3xl"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-800 mb-1">Carpinteros Asociados</h3>
          <p class="text-amber-700 font-medium mb-2">Profesionales Verificados</p>
          <p class="text-gray-500 text-sm">Expertos en diferentes especialidades de carpintería</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="py-16 bg-gradient-to-r from-amber-800 to-amber-600 text-white">
    <div class="container mx-auto px-6 text-center">
      <h2 class="text-3xl font-bold mb-4">¿Listo para trabajar con nosotros?</h2>
      <p class="text-xl text-amber-100 mb-8 max-w-2xl mx-auto">Únete a nuestra comunidad de carpinteros profesionales o encuentra el experto ideal para tu proyecto</p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="registro_C.php" class="bg-white text-amber-800 px-8 py-3 rounded-lg font-semibold hover:bg-amber-100 transition shadow-lg">
          <i class="fas fa-hammer mr-2"></i> Soy Carpintero
        </a>
        <a href="registro_U.php" class="bg-amber-900 text-white px-8 py-3 rounded-lg font-semibold hover:bg-amber-800 transition shadow-lg border border-amber-700">
          <i class="fas fa-user mr-2"></i> Soy Cliente
        </a>
      </div>
    </div>
  </section>
</main>

<footer class="bg-stone-900 text-white py-8 mt-auto">
  <div class="container mx-auto px-6 text-center">
    <p>&copy; <?php echo date('Y'); ?> LF Carpinter. Todos los derechos reservados.</p>
    <p class="text-stone-400 text-sm mt-2">Conectando calidad artesanal con soluciones modernas</p>
  </div>
</footer>

</body>
</html>
