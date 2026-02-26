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
  <meta name="description" content="Preguntas frecuentes sobre LF Carpinter. Resuelve tus dudas sobre cómo contratar carpinteros o registrarte como profesional.">
  <meta name="keywords" content="FAQ, preguntas frecuentes, carpintería, servicios, LF Carpinter">
  <title>Preguntas Frecuentes - LF Carpinter</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    .faq-item summary {
      cursor: pointer;
      list-style: none;
    }
    .faq-item summary::-webkit-details-marker {
      display: none;
    }
    .faq-item[open] summary i {
      transform: rotate(180deg);
    }
  </style>
</head>
<body class="bg-amber-50 text-stone-800 font-sans min-h-screen flex flex-col">

<?php include 'includes/header.php'; ?>

<main class="flex-grow">
  <!-- Hero Section -->
  <section class="relative bg-gradient-to-r from-amber-900 to-amber-700 text-white py-16">
    <div class="container mx-auto px-6 text-center">
      <h1 class="text-4xl md:text-5xl font-bold mb-4">Preguntas Frecuentes</h1>
      <p class="text-xl text-amber-100 max-w-2xl mx-auto">Encuentra respuestas a las dudas más comunes sobre nuestro servicio</p>
    </div>
  </section>

  <!-- FAQ Section -->
  <section class="py-16 bg-white">
    <div class="container mx-auto px-6 max-w-4xl">
      
      <!-- Para Clientes -->
      <div class="mb-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
          <i class="fas fa-user text-amber-700 mr-3"></i>
          Para Clientes
        </h2>
        <div class="space-y-4">
          <details class="faq-item bg-amber-50 rounded-lg border border-amber-200 overflow-hidden">
            <summary class="p-4 font-semibold text-gray-800 flex justify-between items-center hover:bg-amber-100 transition">
              <span>¿Cómo me registro como cliente?</span>
              <i class="fas fa-chevron-down text-amber-700 transition-transform"></i>
            </summary>
            <div class="px-4 pb-4 text-gray-600">
              <p>Haz clic en "Registrar" y selecciona la opción "Usuario". Completa el formulario con tu nombre, correo electrónico, teléfono y ciudad. Recibirás un correo de confirmación.</p>
            </div>
          </details>

          <details class="faq-item bg-amber-50 rounded-lg border border-amber-200 overflow-hidden">
            <summary class="p-4 font-semibold text-gray-800 flex justify-between items-center hover:bg-amber-100 transition">
              <span>¿Cómo busco un carpintero?</span>
              <i class="fas fa-chevron-down text-amber-700 transition-transform"></i>
            </summary>
            <div class="px-4 pb-4 text-gray-600">
              <p>Después de iniciar sesión, accede a la sección "Ver Carpinteros". Podrás explorar los perfiles, verificar su experiencia, portafolio y reseñas de otros clientes.</p>
            </div>
          </details>

          <details class="faq-item bg-amber-50 rounded-lg border border-amber-200 overflow-hidden">
            <summary class="p-4 font-semibold text-gray-800 flex justify-between items-center hover:bg-amber-100 transition">
              <span>¿Cómo solicito un servicio?</span>
              <i class="fas fa-chevron-down text-amber-700 transition-transform"></i>
            </summary>
            <div class="px-4 pb-4 text-gray-600">
              <p>Desde el perfil del carpintero, haz clic en "Solicitar Servicio". Describe tu proyecto, incluye detalles como medidas, materiales y fecha deseada. El carpintero recibirá tu solicitud y podrá aceptarla o rechazarla.</p>
            </div>
          </details>

          <details class="faq-item bg-amber-50 rounded-lg border border-amber-200 overflow-hidden">
            <summary class="p-4 font-semibold text-gray-800 flex justify-between items-center hover:bg-amber-100 transition">
              <span>¿Cuánto tiempo tarda en responder un carpintero?</span>
              <i class="fas fa-chevron-down text-amber-700 transition-transform"></i>
            </summary>
            <div class="px-4 pb-4 text-gray-600">
              <p>Los tiempos de respuesta varían según cada profesional. Puedes verificar el estado de tus solicitudes en la sección "Mis Solicitudes" de tu panel de usuario.</p>
            </div>
          </details>

          <details class="faq-item bg-amber-50 rounded-lg border border-amber-200 overflow-hidden">
            <summary class="p-4 font-semibold text-gray-800 flex justify-between items-center hover:bg-amber-100 transition">
              <span>¿Puedo calificar al carpintero después del servicio?</span>
              <i class="fas fa-chevron-down text-amber-700 transition-transform"></i>
            </summary>
            <div class="px-4 pb-4 text-gray-600">
              <p>Sí, una vez que el proyecto esté marcado como "completado", podrás dejar una calificación de 1 a 5 estrellas y un comentario sobre tu experiencia.</p>
            </div>
          </details>

          <details class="faq-item bg-amber-50 rounded-lg border border-amber-200 overflow-hidden">
            <summary class="p-4 font-semibold text-gray-800 flex justify-between items-center hover:bg-amber-100 transition">
              <span>¿Es gratis usar la plataforma?</span>
              <i class="fas fa-chevron-down text-amber-700 transition-transform"></i>
            </summary>
            <div class="px-4 pb-4 text-gray-600">
              <p>Sí, el registro y uso de la plataforma es completamente gratuito para los clientes. No cobramos comisión por la contratación de servicios.</p>
            </div>
          </details>
        </div>
      </div>

      <!-- Para Carpinteros -->
      <div class="mb-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
          <i class="fas fa-hammer text-amber-700 mr-3"></i>
          Para Carpinteros
        </h2>
        <div class="space-y-4">
          <details class="faq-item bg-amber-50 rounded-lg border border-amber-200 overflow-hidden">
            <summary class="p-4 font-semibold text-gray-800 flex justify-between items-center hover:bg-amber-100 transition">
              <span>¿Cómo me registro como carpintero?</span>
              <i class="fas fa-chevron-down text-amber-700 transition-transform"></i>
            </summary>
            <div class="px-4 pb-4 text-gray-600">
              <p>Haz clic en "Registrar" y selecciona "Carpintero". Completa el formulario con tus datos, experiencia, especialidad y adjunta tu currículum. Tu solicitud será revisada por un administrador.</p>
            </div>
          </details>

          <details class="faq-item bg-amber-50 rounded-lg border border-amber-200 overflow-hidden">
            <summary class="p-4 font-semibold text-gray-800 flex justify-between items-center hover:bg-amber-100 transition">
              <span>¿Cuánto tiempo tarda la verificación?</span>
              <i class="fas fa-chevron-down text-amber-700 transition-transform"></i>
            </summary>
            <div class="px-4 pb-4 text-gray-600">
              <p>El proceso de verificación puede tardar hasta <strong>72 horas hábiles</strong>. Recibirás una notificación por correo sobre el estado de tu solicitud.</p>
            </div>
          </details>

          <details class="faq-item bg-amber-50 rounded-lg border border-amber-200 overflow-hidden">
            <summary class="p-4 font-semibold text-gray-800 flex justify-between items-center hover:bg-amber-100 transition">
              <span>¿Qué debo incluir en mi perfil?</span>
              <i class="fas fa-chevron-down text-amber-700 transition-transform"></i>
            </summary>
            <div class="px-4 pb-4 text-gray-600">
              <p>Tu perfil debe incluir: descripción de tus servicios, años de experiencia, especialidad (muebles a medida, restauración, cocina integral, etc.), ubicación y fotos de trabajos realizados en tu portafolio.</p>
            </div>
          </details>

          <details class="faq-item bg-amber-50 rounded-lg border border-amber-200 overflow-hidden">
            <summary class="p-4 font-semibold text-gray-800 flex justify-between items-center hover:bg-amber-100 transition">
              <span>¿Cómo gestiono las solicitudes de servicio?</span>
              <i class="fas fa-chevron-down text-amber-700 transition-transform"></i>
            </summary>
            <div class="px-4 pb-4 text-gray-600">
              <p>En tu panel de carpintero you'll see las solicitudes recibidas. Puedes aceptarlas, rechazarlas o marcarlas como completadas. El cliente será notificado de cada cambio de estado.</p>
            </div>
          </details>

          <details class="faq-item bg-amber-50 rounded-lg border border-amber-200 overflow-hidden">
            <summary class="p-4 font-semibold text-gray-800 flex justify-between items-center hover:bg-amber-100 transition">
              <span>¿Puedo agregar más proyectos a mi portafolio?</span>
              <i class="fas fa-chevron-down text-amber-700 transition-transform"></i>
            </summary>
            <div class="px-4 pb-4 text-gray-600">
              <p>Sí, desde tu panel puedes agregar nuevos proyectos con imágenes, título, descripción y fecha. Esto te ayuda a mostrar tu trabajo a potenciales clientes.</p>
            </div>
          </details>
        </div>
      </div>

      <!-- Generales -->
      <div>
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
          <i class="fas fa-question-circle text-amber-700 mr-3"></i>
          Preguntas Generales
        </h2>
        <div class="space-y-4">
          <details class="faq-item bg-amber-50 rounded-lg border border-amber-200 overflow-hidden">
            <summary class="p-4 font-semibold text-gray-800 flex justify-between items-center hover:bg-amber-100 transition">
              <span>¿Qué pasa si tengo problemas con un servicio?</span>
              <i class="fas fa-chevron-down text-amber-700 transition-transform"></i>
            </summary>
            <div class="px-4 pb-4 text-gray-600">
              <p>Si tienes algún problema, primero comunica directamente con el carpintero. Si no se resuelve, puedes contactar a nuestro equipo de soporte a través de la sección de contacto.</p>
            </div>
          </details>

          <details class="faq-item bg-amber-50 rounded-lg border border-amber-200 overflow-hidden">
            <summary class="p-4 font-semibold text-gray-800 flex justify-between items-center hover:bg-amber-100 transition">
              <span>¿Cómo puedo recuperar mi contraseña?</span>
              <i class="fas fa-chevron-down text-amber-700 transition-transform"></i>
            </summary>
            <div class="px-4 pb-4 text-gray-600">
              <p>En la página de inicio de sesión, haz clic en "¿Olvidaste tu contraseña?" e ingresa tu correo. Te enviaremos un enlace para restablecer tu contraseña.</p>
            </div>
          </details>

          <details class="faq-item bg-amber-50 rounded-lg border border-amber-200 overflow-hidden">
            <summary class="p-4 font-semibold text-gray-800 flex justify-between items-center hover:bg-amber-100 transition">
              <span>¿En qué ciudades están disponibles?</span>
              <i class="fas fa-chevron-down text-amber-700 transition-transform"></i>
            </summary>
            <div class="px-4 pb-4 text-gray-600">
              <p>Actualmente operamos principalmente en Medellín y area metropolitana. Estamos trabajando para expandir nuestros servicios a otras ciudades de Colombia.</p>
            </div>
          </details>
        </div>
      </div>

    </div>
  </section>

  <!-- Contact CTA -->
  <section class="py-12 bg-amber-100">
    <div class="container mx-auto px-6 text-center">
      <h2 class="text-2xl font-bold text-gray-800 mb-4">¿No encontraste tu pregunta?</h2>
      <p class="text-gray-600 mb-6">Contáctanos y te responderemos lo antes posible</p>
      <a href="contactanos.php" class="inline-block bg-amber-700 text-white px-8 py-3 rounded-lg font-semibold hover:bg-amber-800 transition shadow-lg">
        <i class="fas fa-envelope mr-2"></i> Contáctanos
      </a>
    </div>
  </section>
</main>

<footer class="bg-stone-900 text-white py-8 mt-auto">
  <div class="container mx-auto px-6 text-center">
    <p>&copy; <?php echo date('Y'); ?> LF Carpinter. Todos los derechos reservados.</p>
  </div>
</footer>

</body>
</html>
