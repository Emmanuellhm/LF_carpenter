<?php
session_start();
include 'db_conexion.php';

// Cargar todos los proyectos de la tabla portafolio con sus carpinteros
$query = "
  SELECT 
    p.project_id,
    p.carpenter_user_id,
    p.title as project_name,
    p.description as project_description,
    p.image_path,
    p.price as project_price,
    p.created_at,
    c.carpenter_name,
    c.specialties
  FROM portafolio p
  LEFT JOIN carpenters c ON p.carpenter_user_id = c.carpenter_id
  ORDER BY p.created_at DESC
";

$result = $conn->query($query);
$projects = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Proyectos - LF Carpintería</title>
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
        <a href="ver_carpinteros.php" class="font-semibold text-stone-700 hover:text-amber-600">Ver Carpinteros</a>
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
      <h1 class="text-4xl font-extrabold text-stone-800 mb-2">Proyectos de Nuestros Carpinteros</h1>
      <p class="text-stone-600">Explora los mejores trabajos realizados por nuestros carpinteros profesionales</p>
    </div>

    <?php if (empty($projects)): ?>
      <div class="bg-white p-12 rounded-xl shadow border border-stone-200 text-center">
        <p class="text-stone-500 text-lg">Aún no hay proyectos publicados.</p>
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
            <h3 class="font-bold text-lg text-stone-800 mb-1 line-clamp-1">
              <?php echo htmlspecialchars($p['project_name'] ?? 'Sin nombre'); ?>
            </h3>
            
            <p class="text-sm text-stone-500 mb-2">
              Por: <span class="text-amber-700 font-semibold"><?php echo htmlspecialchars($p['carpenter_name'] ?? 'Carpintero'); ?></span>
            </p>

            <p class="text-stone-600 text-sm line-clamp-2 mb-3">
              <?php echo htmlspecialchars($p['project_description'] ?? ''); ?>
            </p>

            <p class="text-amber-700 font-bold text-xl">
              <?php if (isset($p['project_price']) && $p['project_price'] > 0): ?>
                $<?php echo number_format($p['project_price'], 0, ',', '.'); ?>
              <?php else: ?>
                <span class="text-stone-400">Precio no disponible</span>
              <?php endif; ?>
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
                
                <p class="text-stone-500 mb-4">
                  Carpintero: <span class="text-amber-700 font-semibold"><?php echo htmlspecialchars($p['carpenter_name'] ?? 'N/A'); ?></span>
                  <?php if (!empty($p['specialties'])): ?>
                    <span class="text-stone-400">| <?php echo htmlspecialchars($p['specialties']); ?></span>
                  <?php endif; ?>
                </p>

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
