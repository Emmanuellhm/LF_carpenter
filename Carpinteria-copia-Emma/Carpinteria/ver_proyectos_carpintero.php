<?php
session_start();
include 'db_conexion.php';

// Obtener el ID del carpintero
$carpenter_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($carpenter_id <= 0) {
    header('Location: ver_carpinteros.php');
    exit;
}

// Obtener informaciÃ³n del carpintero
$carpenter_query = "SELECT * FROM carpenters WHERE carpenter_id = ? LIMIT 1";
$stmt = $conn->prepare($carpenter_query);
$stmt->bind_param('i', $carpenter_id);
$stmt->execute();
$carpenter_result = $stmt->get_result();
$carpenter = $carpenter_result->fetch_assoc();
$stmt->close();

if (!$carpenter) {
    header('Location: ver_carpinteros.php');
    exit;
}

// Cargar proyectos solo de este carpintero
$query = "
  SELECT 
    p.project_id,
    p.carpenter_user_id,
    p.title as project_name,
    p.description as project_description,
    p.image_path,
    p.price as project_price,
    p.created_at
  FROM portafolio p
  WHERE p.carpenter_user_id = ?
  ORDER BY p.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $carpenter_id);
$stmt->execute();
$result = $stmt->get_result();
$projects = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Proyectos de <?php echo htmlspecialchars($carpenter['carpenter_name']); ?> - LF CarpinterÃ­a</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-amber-50 to-stone-100 min-h-screen">
  
  <!-- Header -->
  <header class="bg-white border-b border-stone-200 px-8 py-5 shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      <a href="index.php">
        <img src="./img/Logo de CarpinterÃ­a LF.png" alt="Logo" class="h-16 w-auto">
      </a>
      <nav class="flex items-center gap-6">
        <a href="index.php" class="font-semibold text-stone-700 hover:text-amber-600">Inicio</a>
        <a href="ver_carpinteros.php" class="font-semibold text-stone-700 hover:text-amber-600">Ver Carpinteros</a>
        <a href="ver_proyectos.php" class="font-semibold text-stone-700 hover:text-amber-600">Todos los Proyectos</a>
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

  <!-- Main Content -->
  <main class="max-w-7xl mx-auto px-8 py-12">
    <!-- InformaciÃ³n del Carpintero -->
    <div class="mb-8 bg-white rounded-xl shadow-lg p-6 border border-stone-200">
      <div class="flex items-center gap-6">
        <!-- Avatar -->
        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
          <?php 
            $initials = '';
            $names = explode(' ', $carpenter['carpenter_name']);
            foreach ($names as $i => $name) {
              if ($i < 2) $initials .= strtoupper(substr($name, 0, 1));
            }
            echo htmlspecialchars($initials);
          ?>
        </div>
        
        <!-- Info -->
        <div class="flex-1">
          <h1 class="text-3xl font-extrabold text-stone-800 mb-1">
            <?php echo htmlspecialchars($carpenter['carpenter_name']); ?>
          </h1>
          <div class="flex gap-4 text-stone-600">
            <span><strong>Especialidad:</strong> <?php echo htmlspecialchars($carpenter['specialties'] ?? 'General'); ?></span>
            <span><strong>Experiencia:</strong> <?php echo htmlspecialchars($carpenter['experience_years']); ?> aÃ±os</span>
            <span><strong>Proyectos:</strong> <?php echo count($projects); ?> publicados</span>
          </div>
        </div>

        <!-- BotÃ³n volver -->
        <a href="ver_carpinteros.php" class="bg-stone-600 hover:bg-stone-700 text-white px-6 py-3 rounded-lg font-semibold">
          â† Volver
        </a>
      </div>
    </div>

    <div class="mb-6">
      <h2 class="text-2xl font-bold text-stone-800">Proyectos de <?php echo htmlspecialchars($carpenter['carpenter_name']); ?></h2>
    </div>

    <?php if (empty($projects)): ?>
      <div class="bg-white p-12 rounded-xl shadow border border-stone-200 text-center">
        <p class="text-stone-500 text-lg">Este carpintero aÃºn no ha publicado proyectos.</p>
      </div>
    <?php else: ?>
      <!-- Grid de Proyectos -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach ($projects as $p): ?>
          <div class="bg-white border border-stone-200 rounded-xl p-4 shadow-sm hover:shadow-md transition cursor-pointer group"
               onclick="openModal('modal-<?php echo $p['project_id']; ?>')">
            <!-- Imagen -->
            <?php if (!empty($p['image_path'])): ?>
              <div class="w-full h-56 rounded-lg mb-4 overflow-hidden bg-stone-100">
                <img src="<?php echo htmlspecialchars($p['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($p['project_name'] ?? 'Proyecto'); ?>"
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
              </div>
            <?php else: ?>
              <div class="w-full h-56 bg-stone-200 rounded-lg mb-4 flex items-center justify-center">
                <span class="text-stone-400">Sin imagen</span>
              </div>
            <?php endif; ?>

            <!-- Info -->
            <h3 class="font-bold text-lg text-stone-800 mb-2 line-clamp-1">
              <?php echo htmlspecialchars($p['project_name'] ?? 'Sin nombre'); ?>
            </h3>

            <p class="text-stone-600 text-sm line-clamp-2 mb-3">
              <?php echo htmlspecialchars($p['project_description'] ?? ''); ?>
            </p>

            <p class="text-amber-700 font-bold text-xl">
              <?php if (isset($p['project_price']) && $p['project_price'] > 0): ?>
                $<?php echo number_format($p['project_price'], 0, ',', '.'); ?>
              <?php else: ?>
                <span class="text-stone-400">Precio no disponible</span>
              <?php endif; ?>
            </p>
          </div>

          <!-- Modal para ver detalles -->
          <div id="modal-<?php echo $p['project_id']; ?>" 
               class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center hidden z-50 backdrop-blur-sm"
               onclick="closeModal('modal-<?php echo $p['project_id']; ?>')">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 overflow-hidden" onclick="event.stopPropagation()">
              <?php if (!empty($p['image_path'])): ?>
                <img src="<?php echo htmlspecialchars($p['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($p['project_name'] ?? 'Proyecto'); ?>"
                     class="w-full max-h-96 object-contain bg-stone-100">
              <?php endif; ?>
              
              <div class="p-8">
                <h2 class="text-3xl font-bold text-stone-800 mb-2">
                  <?php echo htmlspecialchars($p['project_name'] ?? 'Sin nombre'); ?>
                </h2>

                <p class="text-stone-700 mb-6 leading-relaxed">
                  <?php echo nl2br(htmlspecialchars($p['project_description'] ?? '')); ?>
                </p>

                <div class="flex items-center justify-between">
                  <p class="text-amber-700 font-bold text-3xl">
                    <?php if (isset($p['project_price']) && $p['project_price'] > 0): ?>
                      $<?php echo number_format($p['project_price'], 0, ',', '.'); ?>
                    <?php else: ?>
                      <span class="text-stone-400 text-xl">Precio no disponible</span>
                    <?php endif; ?>
                  </p>
                  <button onclick="closeModal('modal-<?php echo $p['project_id']; ?>')" 
                          class="bg-stone-600 hover:bg-stone-700 text-white px-6 py-3 rounded-lg font-semibold">
                    Cerrar
                  </button>
                </div>
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
      <p>&copy; <?php echo date('Y'); ?> LF CarpinterÃ­a. Todos los derechos reservados.</p>
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
