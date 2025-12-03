<?php
session_start();
include 'db_conexion.php';

// Validar sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: iniciar-seccion.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = '';

// Manejo de POST para actualizar perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? '');

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, city = ? WHERE user_id = ?");
    $stmt->bind_param('ssssi', $full_name, $email, $phone, $city, $user_id);
    if ($stmt->execute()) {
        $_SESSION['user_name'] = $full_name;
        $msg = 'Perfil actualizado correctamente.';
    } else {
        $msg = 'Error al actualizar perfil: ' . $conn->error;
    }
    $stmt->close();
}

// Cargar datos del usuario
$stmt = $conn->prepare("SELECT full_name, email, phone, city FROM users WHERE user_id = ? LIMIT 1");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Cliente - LF Carpinter</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="flex h-screen overflow-hidden bg-gradient-to-br from-amber-50 to-stone-100 font-sans text-stone-800">

  <!-- Sidebar -->
  <aside class="w-64 h-full bg-white shadow-xl flex flex-col justify-between border-r border-stone-200 overflow-y-auto hidden md:flex z-20 relative">
    <div>
      <div class="flex flex-col items-center py-8 border-b border-stone-200 bg-gradient-to-b from-white to-amber-50">
        <div class="relative group">
            <img src="img/fotoP.jpg" alt="Foto Perfil"
                class="w-24 h-24 rounded-full border-4 border-amber-600 object-cover shadow-md transition transform group-hover:scale-105">
            <button onclick="toggleProfile()" class="absolute bottom-0 right-0 bg-amber-600 text-white p-1.5 rounded-full shadow hover:bg-amber-700 transition" title="Editar perfil">
                <i class="fas fa-pencil-alt text-xs"></i>
            </button>
        </div>
        <span class="mt-3 font-bold text-stone-800 text-lg px-4 text-center truncate w-full">
            <?php echo htmlspecialchars($user['full_name']); ?>
        </span>
        <span class="text-xs text-stone-500 font-medium bg-stone-100 px-3 py-1 rounded-full mt-1">Cliente</span>
      </div>
      
      <nav class="flex flex-col space-y-1 px-4 mt-6">
        <a href="#" onclick="showSection('inicio')" class="nav-item flex items-center px-4 py-3 text-stone-700 hover:bg-amber-100 hover:text-amber-800 rounded-lg font-medium transition group active-nav">
            <i class="fas fa-home w-6 text-stone-400 group-hover:text-amber-600 transition"></i>
            <span>Inicio</span>
        </a>
        <a href="#" onclick="showSection('carpinteros')" class="nav-item flex items-center px-4 py-3 text-stone-700 hover:bg-amber-100 hover:text-amber-800 rounded-lg font-medium transition group">
            <i class="fas fa-users w-6 text-stone-400 group-hover:text-amber-600 transition"></i>
            <span>Buscar Carpinteros</span>
        </a>
        <a href="#" onclick="showSection('solicitudes')" class="nav-item flex items-center px-4 py-3 text-stone-700 hover:bg-amber-100 hover:text-amber-800 rounded-lg font-medium transition group">
            <i class="fas fa-clipboard-list w-6 text-stone-400 group-hover:text-amber-600 transition"></i>
            <span>Mis Solicitudes</span>
        </a>
        <a href="#" onclick="showSection('historial')" class="nav-item flex items-center px-4 py-3 text-stone-700 hover:bg-amber-100 hover:text-amber-800 rounded-lg font-medium transition group">
            <i class="fas fa-history w-6 text-stone-400 group-hover:text-amber-600 transition"></i>
            <span>Historial</span>
        </a>
      </nav>
    </div>
    
    <div class="p-4 border-t border-stone-200">
        <a href="logout.php" class="flex items-center justify-center gap-2 w-full py-2.5 text-stone-600 hover:text-red-600 hover:bg-red-50 rounded-lg font-medium transition">
            <i class="fas fa-sign-out-alt"></i>
            <span>Cerrar sesión</span>
        </a>
    </div>
  </aside>

  <!-- Main Content -->
  <div class="flex-1 flex flex-col h-full overflow-hidden relative">
    <!-- Mobile Header -->
    <header class="md:hidden flex items-center justify-between bg-white border-b border-stone-200 px-4 h-16 shadow-sm z-30">
      <div class="flex items-center gap-3">
        <button class="text-stone-600 hover:text-amber-700 p-1">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <img src="./img/Logo de Carpintería LF.png" alt="Logo" class="h-10 w-auto">
      </div>
      <div class="flex items-center gap-3">
        <span class="font-bold text-stone-700 text-sm truncate max-w-[120px]"><?php echo htmlspecialchars($user['full_name']); ?></span>
        <img src="img/fotoP.jpg" alt="Profile" class="h-8 w-8 rounded-full border border-stone-300">
      </div>
    </header>

    <!-- Desktop Header (Search & Actions) -->
    <header class="hidden md:flex items-center justify-between bg-white border-b border-stone-200 px-8 h-20 shadow-sm z-10">
      <div class="flex items-center gap-4 w-1/3">
        <div class="relative w-full">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-stone-400"></i>
            <input type="text" placeholder="Buscar carpinteros, proyectos..." 
                class="w-full pl-10 pr-4 py-2 border border-stone-200 rounded-full bg-stone-50 focus:bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition text-sm">
        </div>
      </div>
      
      <div class="flex items-center gap-6">
        <button class="relative text-stone-500 hover:text-amber-600 transition p-2 rounded-full hover:bg-stone-100">
            <i class="fas fa-bell text-xl"></i>
            <span class="absolute top-1 right-1 h-2.5 w-2.5 bg-red-500 rounded-full border-2 border-white"></span>
        </button>
        <div class="h-8 w-px bg-stone-200"></div>
        <a href="contactanos.php" class="font-semibold text-stone-600 hover:text-amber-700 transition flex items-center gap-2">
            <i class="fas fa-envelope"></i> Contáctanos
        </a>
      </div>
    </header>

    <!-- Scrollable Content Area -->
    <main class="flex-1 overflow-y-auto bg-stone-50 p-6 md:p-10 relative scroll-smooth">
        
        <?php if (!empty($msg)): ?>
            <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex justify-between items-center animate-fade-in-down">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <p><?php echo htmlspecialchars($msg); ?></p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900"><i class="fas fa-times"></i></button>
            </div>
        <?php endif; ?>

        <!-- SECCIÓN: INICIO -->
        <div id="section-inicio" class="content-section space-y-8">
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-amber-700 to-amber-900 rounded-2xl p-8 text-white shadow-lg relative overflow-hidden">
                <div class="absolute top-0 right-0 opacity-10 transform translate-x-10 -translate-y-10">
                    <i class="fas fa-hammer text-9xl"></i>
                </div>
                <div class="relative z-10 max-w-2xl">
                    <h1 class="text-3xl font-bold mb-2">¡Hola, <?php echo explode(' ', $user['full_name'])[0]; ?>! 👋</h1>
                    <p class="text-amber-100 text-lg mb-6">¿Qué proyecto tienes en mente hoy? Encuentra a los mejores carpinteros para hacerlo realidad.</p>
                    <button onclick="showSection('carpinteros')" class="bg-white text-amber-800 px-6 py-3 rounded-lg font-bold shadow hover:bg-amber-50 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                        <i class="fas fa-search"></i> Buscar Profesionales
                    </button>
                </div>
            </div>

            <!-- Featured Categories -->
            <div>
                <h2 class="text-xl font-bold text-stone-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-star text-amber-500"></i> Categorías Populares
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-stone-200 hover:shadow-md hover:border-amber-300 transition cursor-pointer group text-center">
                        <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-amber-600 group-hover:text-white transition">
                            <i class="fas fa-chair text-xl"></i>
                        </div>
                        <h3 class="font-semibold text-stone-700 group-hover:text-amber-700">Muebles</h3>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-stone-200 hover:shadow-md hover:border-amber-300 transition cursor-pointer group text-center">
                        <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-amber-600 group-hover:text-white transition">
                            <i class="fas fa-tools text-xl"></i>
                        </div>
                        <h3 class="font-semibold text-stone-700 group-hover:text-amber-700">Reparaciones</h3>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-stone-200 hover:shadow-md hover:border-amber-300 transition cursor-pointer group text-center">
                        <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-amber-600 group-hover:text-white transition">
                            <i class="fas fa-couch text-xl"></i>
                        </div>
                        <h3 class="font-semibold text-stone-700 group-hover:text-amber-700">Restauración</h3>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-stone-200 hover:shadow-md hover:border-amber-300 transition cursor-pointer group text-center">
                        <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-amber-600 group-hover:text-white transition">
                            <i class="fas fa-home text-xl"></i>
                        </div>
                        <h3 class="font-semibold text-stone-700 group-hover:text-amber-700">Instalaciones</h3>
                    </div>
                </div>
            </div>

            <!-- Recent Projects Feed (Mockup) -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-stone-800 flex items-center gap-2">
                        <i class="fas fa-fire text-orange-500"></i> Proyectos Recientes
                    </h2>
                    <a href="#" class="text-amber-600 hover:text-amber-700 font-medium text-sm">Ver todos</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="featured-projects">
                    <!-- Projects will be loaded via JS -->
                    <div class="col-span-full text-center py-10">
                        <i class="fas fa-spinner fa-spin text-3xl text-amber-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN: BUSCAR CARPINTEROS -->
        <div id="section-carpinteros" class="content-section hidden space-y-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                <h2 class="text-2xl font-bold text-stone-800">Encuentra tu Carpintero Ideal</h2>
                <div class="flex gap-2 w-full md:w-auto">
                    <div class="relative flex-1 md:w-64">
                        <input type="text" id="search-carpenter" placeholder="Buscar por nombre o ciudad..." 
                            class="w-full pl-10 pr-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-stone-400"></i>
                    </div>
                    <button onclick="searchCarpenters()" class="bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700 transition">
                        Buscar
                    </button>
                </div>
            </div>

            <div id="carpenter-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Carpenters loaded via JS -->
            </div>
        </div>

        <!-- SECCIÓN: SOLICITUDES -->
        <div id="section-solicitudes" class="content-section hidden">
            <h2 class="text-2xl font-bold text-stone-800 mb-6">Mis Solicitudes de Proyecto</h2>
            <div class="bg-white rounded-xl shadow border border-stone-200 overflow-hidden">
                <div class="p-8 text-center text-stone-500">
                    <i class="fas fa-folder-open text-4xl mb-3 text-stone-300"></i>
                    <p>No tienes solicitudes activas en este momento.</p>
                    <button onclick="showSection('carpinteros')" class="mt-4 text-amber-600 font-medium hover:underline">
                        Buscar un carpintero para iniciar un proyecto
                    </button>
                </div>
            </div>
        </div>

        <!-- SECCIÓN: HISTORIAL -->
        <div id="section-historial" class="content-section hidden">
            <h2 class="text-2xl font-bold text-stone-800 mb-6">Historial de Contrataciones</h2>
            <div class="bg-white rounded-xl shadow border border-stone-200 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-stone-50 border-b border-stone-200">
                        <tr>
                            <th class="px-6 py-3 font-semibold text-stone-600 text-sm">Carpintero</th>
                            <th class="px-6 py-3 font-semibold text-stone-600 text-sm">Proyecto</th>
                            <th class="px-6 py-3 font-semibold text-stone-600 text-sm">Fecha</th>
                            <th class="px-6 py-3 font-semibold text-stone-600 text-sm">Estado</th>
                            <th class="px-6 py-3 font-semibold text-stone-600 text-sm">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        <!-- Example row -->
                        <tr class="hover:bg-stone-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-700 font-bold text-xs">JP</div>
                                    <span class="font-medium text-stone-800">Juan Pérez</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-stone-600">Mesa de Comedor</td>
                            <td class="px-6 py-4 text-stone-500 text-sm">15/10/2025</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Completado</span>
                            </td>
                            <td class="px-6 py-4">
                                <button class="text-amber-600 hover:text-amber-800 text-sm font-medium">Ver detalles</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
  </div>

  <!-- Modal Editar Perfil -->
  <div id="modal-perfil" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center backdrop-blur-sm transition-opacity opacity-0 pointer-events-none">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300 p-6 relative">
        <button onclick="toggleProfile()" class="absolute top-4 right-4 text-stone-400 hover:text-stone-600">
            <i class="fas fa-times text-xl"></i>
        </button>
        
        <h2 class="text-2xl font-bold text-stone-800 mb-6 text-center">Editar Perfil</h2>
        
        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="update_profile">
            
            <div class="flex justify-center mb-6">
                <div class="relative group cursor-pointer">
                    <img src="img/fotoP.jpg" class="w-24 h-24 rounded-full border-4 border-stone-100 object-cover shadow-sm group-hover:opacity-75 transition">
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                        <i class="fas fa-camera text-white text-2xl drop-shadow-md"></i>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-1">Nombre Completo</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-stone-400"></i>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" 
                        class="w-full pl-10 pr-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-1">Correo Electrónico</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-stone-400"></i>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" 
                        class="w-full pl-10 pr-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1">Teléfono</label>
                    <div class="relative">
                        <i class="fas fa-phone absolute left-3 top-1/2 transform -translate-y-1/2 text-stone-400"></i>
                        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" 
                            class="w-full pl-10 pr-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1">Ciudad</label>
                    <div class="relative">
                        <i class="fas fa-map-marker-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-stone-400"></i>
                        <input type="text" name="city" value="<?php echo htmlspecialchars($user['city']); ?>" 
                            class="w-full pl-10 pr-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-3 rounded-lg shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5 mt-4">
                Guardar Cambios
            </button>
        </form>
    </div>
  </div>

  <!-- Modal Perfil Carpintero (Dinámico) -->
  <div id="modal-carpintero" class="fixed inset-0 bg-black bg-opacity-60 hidden z-50 flex justify-center items-center backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <!-- Header con imagen de fondo -->
        <div class="h-32 bg-gradient-to-r from-stone-800 to-stone-900 relative">
            <button onclick="closeCarpProfile()" class="absolute top-4 right-4 bg-black/30 hover:bg-black/50 text-white p-2 rounded-full backdrop-blur-sm transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Contenido Scrollable -->
        <div class="overflow-y-auto flex-1 p-8 pt-0 relative">
            <!-- Info Principal -->
            <div class="flex flex-col md:flex-row gap-6 -mt-12 mb-8 items-start">
                <img src="img/fotoP.jpg" class="w-32 h-32 rounded-full border-4 border-white shadow-lg bg-white object-cover">
                <div class="pt-14 md:pt-12 flex-1">
                    <h2 id="cp-name" class="text-3xl font-bold text-stone-800">Nombre Carpintero</h2>
                    <div class="flex flex-wrap gap-4 mt-2 text-stone-600">
                        <span class="flex items-center gap-1"><i class="fas fa-map-marker-alt text-amber-600"></i> <span id="cp-city">Ciudad</span></span>
                        <span class="flex items-center gap-1"><i class="fas fa-star text-yellow-500"></i> <span>4.8 (24 reseñas)</span></span>
                        <span class="flex items-center gap-1"><i class="fas fa-briefcase text-stone-500"></i> <span id="cp-exp">5 años exp.</span></span>
                    </div>
                </div>
                <div class="pt-14 md:pt-12">
                    <button class="bg-amber-600 text-white px-6 py-2 rounded-lg font-bold shadow hover:bg-amber-700 transition">
                        Contactar
                    </button>
                </div>
            </div>

            <!-- Tabs -->
            <div class="border-b border-stone-200 mb-6">
                <nav class="flex gap-8">
                    <button class="pb-3 border-b-2 border-amber-600 text-amber-700 font-semibold">Portafolio</button>
                    <button class="pb-3 border-b-2 border-transparent text-stone-500 hover:text-stone-700">Reseñas</button>
                    <button class="pb-3 border-b-2 border-transparent text-stone-500 hover:text-stone-700">Información</button>
                </nav>
            </div>

            <!-- Grid Proyectos -->
            <div id="cp-projects" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Proyectos cargados dinámicamente -->
            </div>
        </div>
    </div>
  </div>
<!-- CONTINUATION_MARKER -->
