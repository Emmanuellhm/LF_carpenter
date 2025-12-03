<?php
session_start();
include 'db_conexion.php';

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
  <title>Carpinteros - LF Carpintería</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-amber-50 to-stone-100 min-h-screen">
  
  <!-- Header -->
  <header class="bg-white border-b border-stone-200 px-8 py-5 shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      <a href="index.php">
        <img src="./img/Logo de Carpintería LF.png" alt="Logo" class="h-16 w-auto">
      </a>
      <nav class="flex items-center gap-6">
        <a href="index.php" class="font-semibold text-stone-700 hover:text-amber-600">Inicio</a>
        <a href="ver_proyectos.php" class="font-semibold text-stone-700 hover:text-amber-600">Ver Proyectos</a>
        <a href="contactanos.php" class="font-semibold text-stone-700 hover:text-amber-600">Contáctanos</a>
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="<?php echo $_SESSION['role'] === 'carpenter' ? 'carpintero.php' : 'panel_usuario.php'; ?>" 
             class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg font-semibold">
            Mi Panel
          </a>
        <?php else: ?>
          <a href="iniciar-seccion.php" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg font-semibold">
            Iniciar Sesión
          </a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <!-- Main Content -->
  <main class="max-w-7xl mx-auto px-8 py-12">
    <div class="mb-8">
      <h1 class="text-4xl font-extrabold text-stone-800 mb-2">Nuestros Carpinteros</h1>
      <p class="text-stone-600">Conoce a los profesionales que hacen realidad tus proyectos</p>
    </div>

    <?php if (empty($carpenters)): ?>
      <div class="bg-white p-12 rounded-xl shadow border border-stone-200 text-center">
        <p class="text-stone-500 text-lg">No hay carpinteros registrados aún.</p>
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
                <p class="text-stone-800 font-medium"><?php echo htmlspecialchars($c['experience_years']); ?> años</p>
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
                  <p class="text-stone-800 font-bold text-lg"><?php echo htmlspecialchars($c['experience_years']); ?> años</p>
                </div>
              </div>

              <?php 
              // Priorizar datos de la tabla users, si no están, buscar en description
              $email = $c['user_email'] ?? '';
              $phone = $c['user_phone'] ?? '';
              $city = $c['user_city'] ?? '';
              
              // Si no hay datos de users, intentar parsear desde description
              if (empty($email) && empty($phone) && empty($city)) {
                $desc = $c['description'] ?? '';
                if (!empty($desc)) {
                  // Buscar Email (entre "Email:" y "|" o final de línea)
                  if (preg_match('/Email:\s*([^|]+)/i', $desc, $m)) {
                    $email = trim($m[1]);
                  }
                  // Buscar Teléfono (con o sin tilde, entre "Tel" y "|" o final)
                  if (preg_match('/Tele?fono?:\s*([^|]+)/i', $desc, $m2)) {
                    $phone = trim($m2[1]);
                  }
                  // Buscar Ciudad (entre "Ciudad:" y "|" o final)
                  if (preg_match('/Ciudad:\s*([^|]+)/i', $desc, $m3)) {
                    $city = trim($m3[1]);
                  }
                }
              }
              
              // Limpiar valores vacíos
              $email = !empty(trim($email)) ? trim($email) : '';
              $phone = !empty(trim($phone)) ? trim($phone) : '';
              $city = !empty(trim($city)) ? trim($city) : '';
              ?>

              <!-- Siempre mostrar información de contacto -->
              <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-bold text-stone-800 mb-3">📞 Información de Contacto</h3>
                <div class="space-y-2 text-sm">
                  <p><strong>Email:</strong> 
                    <?php if (!empty($email)): ?>
                      <a href="mailto:<?php echo htmlspecialchars($email); ?>" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($email); ?></a>
                    <?php else: ?>
                      <span class="text-stone-400">No disponible</span>
                    <?php endif; ?>
                  </p>
                  <p><strong>Teléfono:</strong> 
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
                <h3 class="font-bold text-stone-800 mb-2">💼 Sobre mí</h3>
                <p class="text-stone-600 leading-relaxed">
                  Carpintero especializado en <strong><?php echo htmlspecialchars($c['specialties'] ?: 'trabajos generales'); ?></strong> 
                  con <strong><?php echo htmlspecialchars($c['experience_years']); ?> años de experiencia</strong> en el sector. 
                  Comprometido con la calidad y la satisfacción del cliente en cada proyecto.
                  <?php if ($c['project_count'] > 0): ?>
                    He completado y publicado <strong><?php echo $c['project_count']; ?> proyecto<?php echo $c['project_count'] > 1 ? 's' : ''; ?></strong> exitoso<?php echo $c['project_count'] > 1 ? 's' : ''; ?>.
                  <?php endif; ?>
                </p>
              </div>

              <div class="flex justify-center">
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

  <!-- Footer -->
  <footer class="bg-white border-t border-stone-200 mt-16 py-8">
    <div class="max-w-7xl mx-auto px-8 text-center text-stone-600">
      <p>&copy; <?php echo date('Y'); ?> LF Carpintería. Todos los derechos reservados.</p>
    </div>
  </footer>

  <script>
    function openModal(id) {
      document.getElementById(id).classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
      document.getElementById(id).classList.add('hidden');
      document.body.style.overflow = 'auto';
    }
  </script>
</body>
</html>
