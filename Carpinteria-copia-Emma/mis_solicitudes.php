<?php
session_start();
include 'db_conexion.php';

// Verificar que el usuario estÃ© autenticado
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: iniciar-sesion.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Obtener solicitudes del usuario
$query = "
    SELECT 
        pr.*,
        c.carpenter_name,
        c.specialties,
        u.email as carpenter_email,
        u.phone as carpenter_phone
    FROM project_requests pr
    JOIN users u ON pr.carpenter_user_id = u.user_id
    LEFT JOIN carpenters c ON u.email = c.email
    WHERE pr.user_id = ?
    ORDER BY pr.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$requests = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mis Solicitudes - LF CarpinterÃ­a</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="flex h-screen overflow-hidden bg-gradient-to-br from-amber-50 to-stone-100">
  
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
        <a href="mis_solicitudes.php" class="bg-amber-100 text-amber-700 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-clipboard-list w-5"></i>
          <span>Mis Solicitudes</span>
        </a>
        <a href="ver_carpinteros.php" class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
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
    <!-- Header -->
    <header class="flex items-center justify-between bg-white border-b border-stone-200 px-6 h-20 shadow-sm flex-shrink-0">
      <div class="logo">
        <img src="./img/Logo de CarpinterÃ­a LF.png" alt="Logo" class="h-16 w-auto">
      </div>
      <nav class="flex items-center gap-4">
        <a href="index.php" class="font-semibold text-stone-700 hover:text-amber-600">Inicio</a>
        <a href="contactanos.php" class="font-semibold text-stone-700 hover:text-amber-600">ContÃ¡ctanos</a>
      </nav>
    </header>

    <!-- Main Content -->
    <div class="flex-1 overflow-y-auto">
      <main class="p-10">
        <div class="mb-8">
          <h1 class="text-4xl font-extrabold text-stone-800 mb-2">Mis Solicitudes de Proyectos</h1>
          <p class="text-stone-600">Revisa el estado de tus solicitudes de proyectos personalizados</p>
        </div>

        <?php if (empty($requests)): ?>
          <div class="bg-white p-12 rounded-xl shadow border border-stone-200 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-stone-500 text-lg mb-4">No has enviado solicitudes de proyectos aÃºn.</p>
            <a href="ver_carpinteros.php" class="inline-block bg-amber-600 hover:bg-amber-700 text-white px-6 py-3 rounded-lg font-semibold">
              Buscar Carpinteros
            </a>
          </div>
        <?php else: ?>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($requests as $req): ?>
              <div class="bg-white border border-stone-200 rounded-xl p-6 shadow-sm hover:shadow-md transition">
                <div class="flex items-center gap-3 mb-4">
                  <div class="bg-amber-100 p-2 rounded-full text-amber-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </div>
                  <h3 class="font-bold text-lg text-stone-800 line-clamp-1"><?php echo htmlspecialchars($req['title']); ?></h3>
                </div>
                
                <div class="space-y-2 mb-4">
                  <p class="text-stone-600 text-sm">
                    <strong>Carpintero:</strong> 
                    <span class="text-amber-700 font-medium"><?php echo htmlspecialchars($req['carpenter_name'] ?? 'N/A'); ?></span>
                  </p>
                  <p class="text-stone-600 text-sm">
                    <strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($req['created_at'])); ?>
                  </p>
                </div>

                <div class="flex items-center justify-between mb-4">
                  <?php if (!empty($req['budget'])): ?>
                    <span class="text-amber-600 font-bold text-lg">
                      $<?php echo number_format($req['budget'], 0, ',', '.'); ?>
                    </span>
                  <?php else: ?>
                    <span class="text-stone-400 text-sm">Sin presupuesto</span>
                  <?php endif; ?>
                  
                  <span class="px-3 py-1 rounded-full text-xs font-bold 
                    <?php 
                      echo match($req['status']) {
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'accepted' => 'bg-green-100 text-green-800',
                        'rejected' => 'bg-red-100 text-red-800',
                        'completed' => 'bg-blue-100 text-blue-800',
                        default => 'bg-gray-100 text-gray-800'
                      };
                    ?>">
                    <?php 
                      echo match($req['status']) {
                        'pending' => 'Pendiente',
                        'accepted' => 'Aceptada',
                        'rejected' => 'Rechazada',
                        'completed' => 'Completada',
                        default => $req['status']
                      };
                    ?>
                  </span>
                </div>

                <button onclick="verDetalle(<?php echo $req['request_id']; ?>)" 
                        class="w-full bg-stone-100 hover:bg-stone-200 text-stone-700 py-2 rounded-lg font-semibold transition flex items-center justify-center gap-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  Ver Detalles
                </button>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </main>
    </div>
  </div>

  <!-- Modal de Detalle -->
  <div id="modal-detalle" class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center hidden z-50 backdrop-blur-sm" onclick="cerrarModal()">
    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
      <div class="bg-gradient-to-r from-amber-600 to-amber-700 p-6 flex justify-between items-center sticky top-0 z-10">
        <h2 class="text-2xl font-bold text-white">Detalle de Solicitud</h2>
        <button onclick="cerrarModal()" class="text-white hover:bg-white/20 rounded-full p-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <div class="p-8" id="modal-content">
        <!-- El contenido se llenarÃ¡ con JavaScript -->
      </div>
    </div>
  </div>

  <script>
    const requests = <?php echo json_encode($requests); ?>;

    function verDetalle(requestId) {
      const req = requests.find(r => r.request_id == requestId);
      if (!req) return;

      const statusMap = {
        'pending': { text: 'Pendiente', class: 'bg-yellow-100 text-yellow-800' },
        'accepted': { text: 'Aceptada', class: 'bg-green-100 text-green-800' },
        'rejected': { text: 'Rechazada', class: 'bg-red-100 text-red-800' },
        'completed': { text: 'Completada', class: 'bg-blue-100 text-blue-800' }
      };
      const statusInfo = statusMap[req.status] || { text: req.status, class: 'bg-gray-100 text-gray-800' };

      let html = `
        <div class="mb-6">
          <h3 class="text-2xl font-bold text-stone-800 mb-2">${req.title}</h3>
          <div class="flex items-center gap-4 mb-4">
            <span class="px-3 py-1 rounded-full text-sm font-bold ${statusInfo.class}">${statusInfo.text}</span>
            <span class="text-stone-500 text-sm">Solicitado: ${new Date(req.created_at).toLocaleDateString('es-ES')}</span>
          </div>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
          <p class="font-bold text-stone-800 mb-2">Carpintero Solicitado:</p>
          <p class="text-stone-700">${req.carpenter_name || 'N/A'}</p>
          ${req.carpenter_email ? `<p class="text-sm text-stone-600 mt-1">Email: ${req.carpenter_email}</p>` : ''}
          ${req.carpenter_phone ? `<p class="text-sm text-stone-600">TelÃ©fono: ${req.carpenter_phone}</p>` : ''}
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
          ${req.budget ? `
          <div class="bg-stone-50 border border-stone-200 rounded-lg p-4">
            <p class="text-xs font-bold text-stone-400 uppercase mb-1">Presupuesto</p>
            <p class="text-amber-600 font-bold text-lg">$${parseFloat(req.budget).toLocaleString('es-CO')}</p>
          </div>` : ''}
          ${req.deadline ? `
          <div class="bg-stone-50 border border-stone-200 rounded-lg p-4">
            <p class="text-xs font-bold text-stone-400 uppercase mb-1">Fecha Deseada</p>
            <p class="text-stone-800 font-medium">${new Date(req.deadline).toLocaleDateString('es-ES')}</p>
          </div>` : ''}
          ${req.dimensions ? `
          <div class="bg-stone-50 border border-stone-200 rounded-lg p-4">
            <p class="text-xs font-bold text-stone-400 uppercase mb-1">Dimensiones</p>
            <p class="text-stone-800 font-medium">${req.dimensions}</p>
          </div>` : ''}
        </div>

        <div class="mb-6">
          <p class="text-xs font-bold text-stone-400 uppercase mb-2">DescripciÃ³n</p>
          <p class="text-stone-700 bg-stone-50 p-4 rounded-lg border border-stone-100">${req.project_description.replace(/\n/g, '<br>')}</p>
        </div>

        ${req.materials ? `
        <div class="mb-6">
          <p class="text-xs font-bold text-stone-400 uppercase mb-2">Materiales Preferidos</p>
          <p class="text-stone-700 bg-amber-50 p-4 rounded-lg border border-amber-100">${req.materials.replace(/\n/g, '<br>')}</p>
        </div>` : ''}

        ${req.image_path ? `
        <div class="mb-6">
          <p class="text-xs font-bold text-stone-400 uppercase mb-2">Imagen de Referencia</p>
          <img src="${req.image_path}" alt="Referencia" class="max-w-full h-auto rounded-lg border border-stone-200 shadow-md">
        </div>` : ''}
      `;

      document.getElementById('modal-content').innerHTML = html;
      document.getElementById('modal-detalle').classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    function cerrarModal() {
      document.getElementById('modal-detalle').classList.add('hidden');
      document.body.style.overflow = 'auto';
    }
  </script>
</body>
</html>
