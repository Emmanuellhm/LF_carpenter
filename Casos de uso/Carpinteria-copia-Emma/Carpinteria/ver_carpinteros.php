<?php
session_start();
include 'db_conexion.php';

// Verificar si es un usuario autenticado de tipo 'user'
$showSidebar = isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'user';

// Cargar todos los carpinteros aprobados con datos de contacto de users
$query = "
  SELECT 
    c.carpenter_id,
    c.carpenter_name,
    c.specialties,
    c.experience_years,
    c.description,
    c.cv_file,
    c.email as carpenter_email,
    u.email as user_email,
    u.phone as user_phone,
    u.city as user_city
  FROM carpenters c
  LEFT JOIN users u ON c.email = u.email
  WHERE c.approved = 1
  ORDER BY c.carpenter_name ASC
";

$result = $conn->query($query);
$carpenters = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Contar proyectos del carpintero
        $count_query = "SELECT COUNT(*) as total FROM portafolio WHERE carpenter_user_id = ?";
        $stmt = $conn->prepare($count_query);
        $stmt->bind_param('i', $row['carpenter_id']);
        $stmt->execute();
        $count_result = $stmt->get_result();
        $count_row = $count_result->fetch_assoc();
        $row['project_count'] = $count_row['total'] ?? 0;
        $stmt->close();
        
        $carpenters[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Carpinteros - LF CarpinterÃ­a</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="<?php echo $showSidebar ? 'flex h-screen overflow-hidden' : ''; ?> bg-gradient-to-br from-amber-50 to-stone-100 min-h-screen">

<?php if ($showSidebar): ?>
  <!-- Sidebar -->
  <aside class="w-64 h-full bg-white shadow-xl flex flex-col justify-between border-r border-stone-200 overflow-y-auto">
    <div>
      <div class="flex flex-col items-center py-8 border-b border-stone-200">
        <div class="w-28 h-28 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white text-4xl font-bold shadow-lg border-4 border-amber-600">
          <?php 
            $initials = '';
            $name = $_SESSION['user_name'] ?? 'Usuario';
            $names = explode(' ', $name);
            foreach ($names as $i => $n) {
              if ($i < 2) $initials .= strtoupper(substr($n, 0, 1));
            }
            echo htmlspecialchars($initials);
          ?>
        </div>
        <span class="mt-3 font-bold text-stone-800 text-lg text-center px-4">
          <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?>
        </span>
        <span class="text-xs text-stone-500 mt-1">Usuario</span>
      </div>
      <nav class="flex flex-col space-y-2 px-6 mt-6">
        <a href="panel_usuario.php" class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-home w-5"></i>
          <span>Panel Principal</span>
        </a>
        <a href="mis_solicitudes.php" class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-clipboard-list w-5"></i>
          <span>Mis Solicitudes</span>
        </a>
        <a href="ver_carpinteros.php" class="bg-amber-100 text-amber-700 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-search w-5"></i>
          <span>Buscar Carpinteros</span>
        </a>
        <a href="ver_proyectos.php" class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-folder-open w-5"></i>
          <span>Ver Proyectos</span>
        </a>
      </nav>
    </div>
    <a href="logout.php"
       class="text-center py-5 text-stone-500 hover:text-amber-600 cursor-pointer font-medium border-t border-stone-200 flex items-center justify-center gap-2">
       <i class="fas fa-sign-out-alt"></i>
       <span>Cerrar sesiÃ³n</span>
    </a>
  </aside>

  <div class="flex-1 flex flex-col h-full overflow-hidden">
<?php endif; ?>

  <?php if (!$showSidebar): ?>
  <!-- Header for non-authenticated users -->
  <header class="bg-white border-b border-stone-200 px-8 py-5 shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      <a href="index.php">
        <img src="./img/Logo de CarpinterÃ­a LF.png" alt="Logo" class="h-16 w-auto">
      </a>
      <nav class="flex items-center gap-6">
        <a href="index.php" class="font-semibold text-stone-700 hover:text-amber-600">Inicio</a>
        <a href="ver_proyectos.php" class="font-semibold text-stone-700 hover:text-amber-600">Ver Proyectos</a>
        <a href="contactanos.php" class="font-semibold text-stone-700 hover:text-amber-600">ContÃ¡ctanos</a>
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="<?php echo $_SESSION['role'] === 'carpenter' ? 'carpintero.php' : 'panel_usuario.php'; ?>" 
             class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg font-semibold">
            Mi Panel
          </a>
        <?php else: ?>
          <a href="iniciar-sesion.php" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg font-semibold">
            Iniciar SesiÃ³n
          </a>
        <?php endif; ?>
      </nav>
    </div>
  </header>
  <?php else: ?>
  <!-- Header for authenticated users -->
  <header class="flex items-center justify-between bg-white border-b border-stone-200 px-6 h-20 shadow-sm flex-shrink-0">
    <div class="logo">
      <img src="./img/Logo de CarpinterÃ­a LF.png" alt="Logo" class="h-16 w-auto">
    </div>
    <nav class="flex items-center gap-4">
      <a href="index.php" class="font-semibold text-stone-700 hover:text-amber-600">Inicio</a>
      <a href="contactanos.php" class="font-semibold text-stone-700 hover:text-amber-600">ContÃ¡ctanos</a>
    </nav>
  </header>
  <?php endif; ?>

  <!-- Mensajes de Ã©xito/error -->
  <?php if (isset($_GET['success'])): ?>
  <div id="mensaje-exito" class="fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    <?php
      if ($_GET['success'] === 'solicitud_enviada') {
        echo 'âœ… Solicitud de proyecto enviada correctamente';
      }
    ?>
  </div>
  <script>
    setTimeout(() => {
      const msg = document.getElementById('mensaje-exito');
      if (msg) msg.remove();
    }, 4000);
  </script>
  <?php endif; ?>

  <?php if (isset($_GET['error'])): ?>
  <div id="mensaje-error" class="fixed top-4 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    <?php
      $error_msg = match($_GET['error']) {
        'datos_incompletos' => 'âŒ Por favor completa todos los campos requeridos',
        'carpintero_no_existe' => 'âŒ El carpintero seleccionado no existe',
        'carpintero_sin_usuario' => 'âŒ Error al procesar la solicitud',
        'error_servidor' => 'âŒ Error del servidor, intenta nuevamente',
        'error_al_enviar' => 'âŒ Error al enviar la solicitud',
        default => 'âŒ Ha ocurrido un error'
      };
      echo $error_msg;
    ?>
  </div>
  <script>
    setTimeout(() => {
      const msg = document.getElementById('mensaje-error');
      if (msg) msg.remove();
    }, 4000);
  </script>
  <?php endif; ?>

  <!-- Main Content -->
  <?php if ($showSidebar): ?>
  <div class="flex-1 overflow-y-auto">
  <?php endif; ?>
    <main class="<?php echo !$showSidebar ? 'max-w-7xl mx-auto' : ''; ?> px-8 py-12">
      <div class="mb-8">
        <h1 class="text-4xl font-extrabold text-stone-800 mb-2">Nuestros Carpinteros</h1>
        <p class="text-stone-600">Conoce a los profesionales que hacen realidad tus proyectos</p>
      </div>

      <?php if (empty($carpenters)): ?>
        <div class="bg-white p-12 rounded-xl shadow border border-stone-200 text-center">
          <p class="text-stone-500 text-lg">No hay carpinteros registrados aÃºn.</p>
        </div>
      <?php else: ?>
        <!-- Grid de Carpinteros -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php foreach ($carpenters as $c): ?>
            <div class="bg-white border border-stone-200 rounded-xl p-6 shadow-sm hover:shadow-lg transition">
              <!-- Foto de perfil placeholder -->
              <div class="w-24 h-24 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 mx-auto mb-4 flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                <?php 
                  $initials = '';
                  $names = explode(' ', $c['carpenter_name']);
                  foreach ($names as $i => $name) {
                    if ($i < 2) $initials .= strtoupper(substr($name, 0, 1));
                  }
                  echo htmlspecialchars($initials);
                ?>
              </div>

              <!-- Info -->
              <h3 class="font-bold text-xl text-stone-800 text-center mb-2">
                <?php echo htmlspecialchars($c['carpenter_name']); ?>
              </h3>

              <div class="space-y-2 mb-4">
                <div class="bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                  <p class="text-xs font-semibold text-stone-500 uppercase">Especialidad</p>
                  <p class="text-stone-800 font-medium"><?php echo htmlspecialchars($c['specialties'] ?: 'General'); ?></p>
                </div>

                <div class="bg-stone-50 border border-stone-200 rounded-lg px-3 py-2">
                  <p class="text-xs font-semibold text-stone-500 uppercase">Experiencia</p>
                  <p class="text-stone-800 font-medium"><?php echo htmlspecialchars($c['experience_years']); ?> aÃ±os</p>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg px-3 py-2">
                  <p class="text-xs font-semibold text-stone-500 uppercase">Proyectos</p>
                  <p class="text-stone-800 font-medium"><?php echo $c['project_count']; ?> publicados</p>
                </div>
              </div>

              <!-- Botones -->
              <div class="flex gap-2">
                <?php if ($c['project_count'] > 0): ?>
                  <a href="ver_proyectos_carpintero.php?id=<?php echo $c['carpenter_id']; ?>" 
                     class="flex-1 bg-amber-600 hover:bg-amber-700 text-white text-center py-2 rounded-lg font-semibold text-sm">
                    Ver Proyectos
                  </a>
                <?php else: ?>
                  <button disabled class="flex-1 bg-stone-300 text-stone-500 text-center py-2 rounded-lg font-semibold text-sm cursor-not-allowed">
                    Sin Proyectos
                  </button>
                <?php endif; ?>
                
                <button onclick="openModal('modal-<?php echo $c['carpenter_id']; ?>')" 
                        class="flex-1 bg-stone-600 hover:bg-stone-700 text-white py-2 rounded-lg font-semibold text-sm">
                  Ver Perfil
                </button>
              </div>
            </div>

            <!-- Modal de Perfil -->
            <div id="modal-<?php echo $c['carpenter_id']; ?>" 
                 class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center hidden z-50 backdrop-blur-sm"
                 onclick="closeModal('modal-<?php echo $c['carpenter_id']; ?>')">
              <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 p-8" onclick="event.stopPropagation()">
                <!-- Foto grande -->
                <div class="w-32 h-32 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 mx-auto mb-6 flex items-center justify-center text-white text-5xl font-bold shadow-xl">
                  <?php 
                    $initials = '';
                    $names = explode(' ', $c['carpenter_name']);
                    foreach ($names as $i => $name) {
                      if ($i < 2) $initials .= strtoupper(substr($name, 0, 1));
                    }
                    echo htmlspecialchars($initials);
                  ?>
                </div>

                <h2 class="text-3xl font-bold text-stone-800 text-center mb-6">
                  <?php echo htmlspecialchars($c['carpenter_name']); ?>
                </h2>

                <div class="grid grid-cols-2 gap-4 mb-6">
                  <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <p class="text-xs font-semibold text-stone-500 uppercase mb-1">Especialidad</p>
                    <p class="text-stone-800 font-bold text-lg"><?php echo htmlspecialchars($c['specialties'] ?: 'General'); ?></p>
                  </div>

                  <div class="bg-stone-50 border border-stone-200 rounded-lg p-4">
                    <p class="text-xs font-semibold text-stone-500 uppercase mb-1">Experiencia</p>
                    <p class="text-stone-800 font-bold text-lg"><?php echo htmlspecialchars($c['experience_years']); ?> aÃ±os</p>
                  </div>
                </div>

                <?php 
                // Priorizar datos de la tabla users, si no estÃ¡n, buscar en description
                $email = $c['user_email'] ?? '';
                $phone = $c['user_phone'] ?? '';
                $city = $c['user_city'] ?? '';
                
                // Si no hay datos de users, intentar parsear desde description
                if (empty($email) && empty($phone) && empty($city)) {
                  $desc = $c['description'] ?? '';
                  if (!empty($desc)) {
                    // Buscar Email (entre "Email:" y "|" o final de lÃ­nea)
                    if (preg_match('/Email:\s*([^|]+)/i', $desc, $m)) {
                      $email = trim($m[1]);
                    }
                    // Buscar TelÃ©fono (con o sin tilde, entre "Tel" y "|" o final)
                    if (preg_match('/Tele?fono?:\s*([^|]+)/i', $desc, $m2)) {
                      $phone = trim($m2[1]);
                    }
                    // Buscar Ciudad (entre "Ciudad:" y "|" o final)
                    if (preg_match('/Ciudad:\s*([^|]+)/i', $desc, $m3)) {
                      $city = trim($m3[1]);
                    }
                  }
                }
                
                // Limpiar valores vacÃ­os
                $email = !empty(trim($email)) ? trim($email) : '';
                $phone = !empty(trim($phone)) ? trim($phone) : '';
                $city = !empty(trim($city)) ? trim($city) : '';
                ?>

                <!-- Siempre mostrar informaciÃ³n de contacto -->
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                  <h3 class="font-bold text-stone-800 mb-3">ðŸ“ž InformaciÃ³n de Contacto</h3>
                  <div class="space-y-2 text-sm">
                    <p><strong>Email:</strong> 
                      <?php if (!empty($email)): ?>
                        <a href="mailto:<?php echo htmlspecialchars($email); ?>" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($email); ?></a>
                      <?php else: ?>
                        <span class="text-stone-400">No disponible</span>
                      <?php endif; ?>
                    </p>
                    <p><strong>TelÃ©fono:</strong> 
                      <?php if (!empty($phone)): ?>
                        <a href="tel:<?php echo htmlspecialchars($phone); ?>" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($phone); ?></a>
                      <?php else: ?>
                        <span class="text-stone-400">No disponible</span>
                      <?php endif; ?>
                    </p>
                    <p><strong>Ciudad:</strong> 
                      <?php if (!empty($city)): ?>
                        <?php echo htmlspecialchars($city); ?>
                      <?php else: ?>
                        <span class="text-stone-400">No disponible</span>
                      <?php endif; ?>
                    </p>
                  </div>
                </div>

                <div class="mb-6">
                  <h3 class="font-bold text-stone-800 mb-2">ðŸ’¼ Sobre mÃ­</h3>
                  <p class="text-stone-600 leading-relaxed">
                    Carpintero especializado en <strong><?php echo htmlspecialchars($c['specialties'] ?: 'trabajos generales'); ?></strong> 
                    con <strong><?php echo htmlspecialchars($c['experience_years']); ?> aÃ±os de experiencia</strong> en el sector. 
                    Comprometido con la calidad y la satisfacciÃ³n del cliente en cada proyecto.
                    <?php if ($c['project_count'] > 0): ?>
                      He completado y publicado <strong><?php echo $c['project_count']; ?> proyecto<?php echo $c['project_count'] > 1 ? 's' : ''; ?></strong> exitoso<?php echo $c['project_count'] > 1 ? 's' : ''; ?>.
                    <?php endif; ?>
                  </p>
                </div>

                <div class="flex justify-center gap-3">
                  <?php if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'user'): ?>
                    <button onclick="openRequestModal(<?php echo $c['carpenter_id']; ?>, '<?php echo htmlspecialchars($c['carpenter_name'], ENT_QUOTES); ?>')" 
                            class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center gap-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg>
                      Solicitar Proyecto
                    </button>
                  <?php endif; ?>
                  <button onclick="closeModal('modal-<?php echo $c['carpenter_id']; ?>')" 
                          class="bg-stone-600 hover:bg-stone-700 text-white px-8 py-3 rounded-lg font-semibold">
                    Cerrar
                  </button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </main>
  <?php if ($showSidebar): ?>
  </div>
  <?php endif; ?>

  <?php if (!$showSidebar): ?>
  <!-- Footer -->
  <footer class="bg-white border-t border-stone-200 mt-16 py-8">
    <div class="max-w-7xl mx-auto px-8 text-center text-stone-600">
      <p>&copy; <?php echo date('Y'); ?> LF CarpinterÃ­a. Todos los derechos reservados.</p>
    </div>
  </footer>
  <?php endif; ?>

<?php if ($showSidebar): ?>
</div>
<?php endif; ?>

  <!-- Modal de Solicitud de Proyecto Personalizado -->
  <div id="modal-request-project" class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center hidden z-50 backdrop-blur-sm" onclick="closeRequestModal()">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
      <!-- Header -->
      <div class="bg-gradient-to-r from-amber-600 to-amber-700 p-6 flex justify-between items-center sticky top-0 z-10">
        <h2 class="text-2xl font-bold text-white flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Solicitar Proyecto Personalizado
        </h2>
        <button onclick="closeRequestModal()" class="text-white hover:bg-white/20 rounded-full p-1 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Form -->
      <form id="request-project-form" action="solicitar_proyecto.php" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
        <input type="hidden" name="carpenter_id" id="request-carpenter-id" value="">
        
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
          <p class="text-sm text-amber-800">
            <strong>Carpintero:</strong> <span id="request-carpenter-name" class="font-semibold">-</span>
          </p>
        </div>

        <div>
          <label class="block font-semibold text-stone-700 mb-2">TÃ­tulo del Proyecto *</label>
          <input type="text" name="title" required placeholder="Ej: Mesa de comedor rÃºstica"
                 class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none">
        </div>

        <div>
          <label class="block font-semibold text-stone-700 mb-2">DescripciÃ³n Detallada *</label>
          <textarea name="description" rows="4" required placeholder="Describe tu proyecto con el mayor detalle posible..."
                    class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none"></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block font-semibold text-stone-700 mb-2">Presupuesto Estimado</label>
            <input type="number" name="budget" placeholder="Ej: 500000" min="0" step="1000"
                   class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none">
          </div>

          <div>
            <label class="block font-semibold text-stone-700 mb-2">Fecha Deseada</label>
            <input type="date" name="deadline"
                   class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none">
          </div>
        </div>

        <div>
          <label class="block font-semibold text-stone-700 mb-2">Medidas / Dimensiones</label>
          <input type="text" name="dimensions" placeholder="Ej: 2m x 1m x 0.8m"
                 class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none">
        </div>

        <div>
          <label class="block font-semibold text-stone-700 mb-2">Materiales Preferidos</label>
          <textarea name="materials" rows="2" placeholder="Ej: Madera de roble, acabado natural..."
                    class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none"></textarea>
        </div>

        <div>
          <label class="block font-semibold text-stone-700 mb-2">Imagen de Referencia (opcional)</label>
          <input type="file" name="reference_image" accept="image/*"
                 class="w-full text-sm text-stone-600 file:mr-4 file:py-2 file:px-4 
                        file:rounded-lg file:border-0 
                        file:text-sm file:font-semibold 
                        file:bg-amber-50 file:text-amber-700
                        hover:file:bg-amber-100 cursor-pointer border border-stone-300 rounded-lg">
          <p class="text-xs text-stone-500 mt-1">Puedes subir una imagen de referencia de lo que deseas</p>
        </div>

        <div class="flex gap-3 pt-4 border-t border-stone-200">
          <button type="button" onclick="closeRequestModal()" 
                  class="flex-1 px-5 py-3 rounded-lg text-stone-600 hover:bg-stone-100 font-medium transition border border-stone-300">
            Cancelar
          </button>
          <button type="submit" 
                  class="flex-1 px-5 py-3 rounded-lg bg-amber-600 text-white hover:bg-amber-700 font-bold shadow-lg transition">
            Enviar Solicitud
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openModal(id) {
      document.getElementById(id).classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
      document.getElementById(id).classList.add('hidden');
      document.body.style.overflow = 'auto';
    }

    function openRequestModal(carpenterId, carpenterName) {
      document.getElementById('request-carpenter-id').value = carpenterId;
      document.getElementById('request-carpenter-name').textContent = carpenterName;
      document.getElementById('modal-request-project').classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    function closeRequestModal() {
      document.getElementById('modal-request-project').classList.add('hidden');
      document.body.style.overflow = 'auto';
      document.getElementById('request-project-form').reset();
    }
  </script>
</body>
</html>
