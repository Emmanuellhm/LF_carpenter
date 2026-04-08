<?php
session_start();
include 'db_conexion.php';

// Seguridad: solo admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: iniciar-sesion.php?error=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Administrador - LF CarpinterÃ­a</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="flex h-screen bg-gradient-to-br from-amber-50 to-stone-100">

  <!-- Sidebar -->
  <aside class="w-64 h-full bg-white shadow-xl flex flex-col justify-between border-r border-stone-200 overflow-y-auto">
    
    <div>
      <div class="flex flex-col items-center py-8 border-b border-stone-200">
        <div class="w-28 h-28 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-4xl font-bold shadow-lg border-4 border-red-700">
          AD
        </div>
        <span class="mt-3 font-bold text-stone-800 text-lg">Administrador</span>
        <span class="text-xs text-stone-500 mt-1">Admin</span>
      </div>

      <nav class="flex flex-col space-y-2 px-6 mt-6">
        <button onclick="mostrarSeccion('dashboard')" class="seccion-btn text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-home w-5"></i>
          <span>Panel Principal</span>
        </button>
        <button onclick="mostrarSeccion('solicitudes')" class="seccion-btn text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-user-check w-5"></i>
          <span>Solicitudes Carpinteros</span>
        </button>
        <button onclick="mostrarSeccion('gestionar_carpinteros')" class="seccion-btn text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-users-cog w-5"></i>
          <span>Gestionar Carpinteros</span>
        </button>
        <button onclick="mostrarSeccion('reportes')" class="seccion-btn text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-exclamation-triangle w-5"></i>
          <span>Reportes de Clientes</span>
        </button>
        <button onclick="mostrarSeccion('sugerencias')" class="seccion-btn text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-lightbulb w-5"></i>
          <span>Sugerencias</span>
        </button>
        <button onclick="mostrarSeccion('notificaciones')" class="seccion-btn text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-3 font-medium text-left flex items-center gap-3 transition">
          <i class="fas fa-bell w-5"></i>
          <span>Notificaciones</span>
        </button>
      </nav>
    </div>

    <!-- Cerrar sesiÃ³n -->
    <a href="logout.php"
       class="text-center py-5 text-stone-500 hover:text-amber-600 cursor-pointer font-medium border-t border-stone-200 flex items-center justify-center gap-2">
       <i class="fas fa-sign-out-alt"></i>
       <span>Cerrar sesiÃ³n</span>
    </a>
  </aside>

  <!-- Main content -->
  <div class="flex-1 flex flex-col h-full overflow-hidden">

    <header class="flex items-center justify-between bg-white border-b border-stone-200 px-6 h-20 shadow-sm flex-shrink-0">
      <div class="logo">
        <img src="img/Logo de CarpinterÃ­a LF.png" alt="Logo" class="h-16 w-auto">
      </div>
      <nav>
        <a href="contacto.php" class="font-semibold text-stone-700 hover:text-amber-600">ContÃ¡ctanos</a>
      </nav>
    </header>

    <!-- Contenedor con scroll para el contenido principal -->
    <div class="flex-1 overflow-y-auto">
      <main class="p-10 space-y-10">

      <!-- Dashboard / Panel Principal -->
      <section id="dashboard" class="seccion">
        <h1 class="text-4xl font-bold text-stone-800 mb-2">Panel de Administrador</h1>
        <p class="text-stone-600 mb-8">Bienvenido al panel de control administrativo</p>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- Solicitudes Pendientes -->
          <div class="bg-white p-6 rounded-xl shadow-lg border border-stone-200 hover:shadow-xl transition">
            <div class="flex items-center gap-4 mb-4">
              <div class="bg-amber-100 p-3 rounded-lg">
                <i class="fas fa-user-check text-2xl text-amber-600"></i>
              </div>
              <h2 class="text-xl font-bold text-stone-800">Solicitudes</h2>
            </div>
            <p class="text-stone-600 mb-4">Revisa solicitudes de carpinteros.</p>
            <button onclick="mostrarSeccion('solicitudes')" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-semibold px-6 py-3 rounded-lg shadow w-full text-center">
              Ver Solicitudes
            </button>
          </div>

          <!-- Reportes -->
          <div class="bg-white p-6 rounded-xl shadow-lg border border-stone-200 hover:shadow-xl transition">
            <div class="flex items-center gap-4 mb-4">
              <div class="bg-orange-100 p-3 rounded-lg">
                <i class="fas fa-exclamation-triangle text-2xl text-orange-600"></i>
              </div>
              <h2 class="text-xl font-bold text-stone-800">Reportes</h2>
            </div>
            <p class="text-stone-600 mb-4">Gestiona reportes de clientes.</p>
            <button onclick="mostrarSeccion('reportes')" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-semibold px-6 py-3 rounded-lg shadow w-full text-center">
              Ver Reportes
            </button>
          </div>

          <!-- Sugerencias -->
          <div class="bg-white p-6 rounded-xl shadow-lg border border-stone-200 hover:shadow-xl transition">
            <div class="flex items-center gap-4 mb-4">
              <div class="bg-stone-100 p-3 rounded-lg">
                <i class="fas fa-lightbulb text-2xl text-stone-600"></i>
              </div>
              <h2 class="text-xl font-bold text-stone-800">Sugerencias</h2>
            </div>
            <p class="text-stone-600 mb-4">Revisa sugerencias de usuarios.</p>
            <button onclick="mostrarSeccion('sugerencias')" class="inline-block bg-stone-600 hover:bg-stone-700 text-white font-semibold px-6 py-3 rounded-lg shadow w-full text-center">
              Ver Sugerencias
            </button>
          </div>

          <!-- Notificaciones -->
          <div class="bg-white p-6 rounded-xl shadow-lg border border-stone-200 hover:shadow-xl transition">
            <div class="flex items-center gap-4 mb-4">
              <div class="bg-stone-200 p-3 rounded-lg">
                <i class="fas fa-bell text-2xl text-stone-700"></i>
              </div>
              <h2 class="text-xl font-bold text-stone-800">Notificaciones</h2>
            </div>
            <p class="text-stone-600 mb-4">Revisa notificaciones del sistema.</p>
            <button onclick="mostrarSeccion('notificaciones')" class="inline-block bg-stone-600 hover:bg-stone-700 text-white font-semibold px-6 py-3 rounded-lg shadow w-full text-center">
              Ver Notificaciones
            </button>
          </div>
        </div>

        <!-- EstadÃ­sticas RÃ¡pidas -->
        <?php
        // Obtener estadÃ­sticas
        $pendientes_count = 0;
        $aprobados_count = 0;
        $total_carpinteros = 0;
        
        $stat_query = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN approved = 0 THEN 1 ELSE 0 END) as pendientes,
                        SUM(CASE WHEN approved = 1 THEN 1 ELSE 0 END) as aprobados
                       FROM carpenters";
        $stat_result = $conn->query($stat_query);
        if ($stat_result && $row = $stat_result->fetch_assoc()) {
          $total_carpinteros = $row['total'];
          $pendientes_count = $row['pendientes'];
          $aprobados_count = $row['aprobados'];
        }
        ?>
        <div class="mt-10 grid md:grid-cols-3 gap-6">
          <div class="bg-gradient-to-br from-amber-500 to-amber-600 p-6 rounded-xl shadow-lg text-white">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-amber-100 text-sm font-medium mb-1">Solicitudes Pendientes</p>
                <p class="text-4xl font-bold"><?php echo $pendientes_count; ?></p>
              </div>
              <div class="bg-white/20 p-3 rounded-lg">
                <i class="fas fa-user-clock text-3xl"></i>
              </div>
            </div>
          </div>

          <div class="bg-gradient-to-br from-stone-600 to-stone-700 p-6 rounded-xl shadow-lg text-white">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-stone-200 text-sm font-medium mb-1">Carpinteros Aprobados</p>
                <p class="text-4xl font-bold"><?php echo $aprobados_count; ?></p>
              </div>
              <div class="bg-white/20 p-3 rounded-lg">
                <i class="fas fa-user-check text-3xl"></i>
              </div>
            </div>
          </div>

          <div class="bg-gradient-to-br from-orange-600 to-orange-700 p-6 rounded-xl shadow-lg text-white">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-orange-100 text-sm font-medium mb-1">Total Carpinteros</p>
                <p class="text-4xl font-bold"><?php echo $total_carpinteros; ?></p>
              </div>
              <div class="bg-white/20 p-3 rounded-lg">
                <i class="fas fa-users text-3xl"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- InformaciÃ³n del Sistema -->
        <div class="mt-10 bg-white p-8 rounded-xl shadow-lg border border-stone-200">
          <h2 class="text-2xl font-bold text-stone-800 mb-6">InformaciÃ³n del Sistema</h2>
          <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-stone-50 p-4 rounded-lg border border-stone-100">
              <p class="text-xs font-bold text-stone-400 uppercase mb-1">Rol</p>
              <p class="text-stone-800 font-medium">Administrador</p>
            </div>
            <div class="bg-stone-50 p-4 rounded-lg border border-stone-100">
              <p class="text-xs font-bold text-stone-400 uppercase mb-1">Acceso</p>
              <p class="text-stone-800 font-medium">Control Total del Sistema</p>
            </div>
            <div class="bg-stone-50 p-4 rounded-lg border border-stone-100">
              <p class="text-xs font-bold text-stone-400 uppercase mb-1">Total Carpinteros</p>
              <p class="text-stone-800 font-medium"><?php echo $total_carpinteros; ?> registrados</p>
            </div>
            <div class="bg-stone-50 p-4 rounded-lg border border-stone-100">
              <p class="text-xs font-bold text-stone-400 uppercase mb-1">Pendientes</p>
              <p class="text-stone-800 font-medium"><?php echo $pendientes_count; ?> por revisar</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Solicitudes -->
      <section id="solicitudes" class="seccion hidden">
        <h1 class="text-3xl font-extrabold text-stone-800 mb-6">Solicitudes de Carpinteros Pendientes</h1>

        <?php
        $sql = "SELECT * FROM carpenters WHERE approved = 0";
        $result = $conn->query($sql);

        if (!$result) {
            echo "<p class='text-red-600 font-semibold'>Error en la consulta: {$conn->error}</p>";
        } elseif ($result->num_rows > 0) {
            echo "<div class='grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6'>";

            while ($row = $result->fetch_assoc()) {
                // Parsear informaciÃ³n de la descripciÃ³n
                $desc = $row['description'] ?? '';
                $email_parsed = '';
                $phone_parsed = '';
                $city_parsed = '';
                
                if (preg_match('/Email:\s*([^|]+)/i', $desc, $m)) $email_parsed = trim($m[1]);
                if (preg_match('/Tel.fono:\s*([^|]+)/iu', $desc, $m2)) $phone_parsed = trim($m2[1]);
                if (preg_match('/Ciudad:\s*([^|]+)/i', $desc, $m3)) $city_parsed = trim($m3[1]);

                $carpenter_data = json_encode([
                    'id' => $row['carpenter_id'],
                    'name' => $row['carpenter_name'] ?? '-',
                    'email' => $row['email'] ?: $email_parsed,
                    'phone' => $phone_parsed,
                    'city' => $city_parsed,
                    'specialties' => $row['specialties'] ?? '-',
                    'experience' => $row['experience_years'] ?? 0,
                    'description' => $row['description'] ?? '-',
                    'cv_file' => $row['cv_file'] ?? '',
                    'rating' => $row['rating_avg'] ?? 0,
                    'verified' => $row['is_verified'] ?? 0,
                    'created_at' => $row['created_at'] ?? ''
                ], JSON_HEX_APOS | JSON_HEX_QUOT);

                echo "
                <div class='bg-white p-6 rounded-xl shadow-md border border-stone-200 hover:shadow-lg transition-shadow'>
                  <div class='flex items-center gap-3 mb-4'>
                    <div class='bg-amber-100 p-3 rounded-full'>
                      <svg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6 text-amber-600' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' />
                      </svg>
                    </div>
                    <div class='flex-1'>
                      <p class='font-bold text-lg text-stone-800'>" . htmlspecialchars($row['carpenter_name'] ?? '-') . "</p>
                      <p class='text-sm text-stone-500'>" . htmlspecialchars($row['specialties'] ?? 'Sin especialidad') . "</p>
                    </div>
                  </div>
                  
                  <div class='space-y-2 mb-4'>
                    <p class='text-sm text-stone-600'>
                      <span class='font-semibold'>Experiencia:</span> " . intval($row['experience_years'] ?? 0) . " aÃ±os
                    </p>
                    <p class='text-sm text-stone-600 line-clamp-2'>
                      <span class='font-semibold'>DescripciÃ³n:</span> " . htmlspecialchars(substr($row['description'] ?? '-', 0, 100)) . "...
                    </p>
                  </div>

                  <div class='flex gap-2 mt-4'>
                    <button onclick='verPerfilCarpintero(" . $carpenter_data . ")' 
                      class='flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold shadow transition'>
                      <span class='flex items-center justify-center gap-1'>
                        <svg xmlns='http://www.w3.org/2000/svg' class='h-4 w-4' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                          <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 12a3 3 0 11-6 0 3 3 0 016 0z' />
                          <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' />
                        </svg>
                        Ver Perfil
                      </span>
                    </button>
                  </div>
                  
                  <div class='flex gap-2 mt-3'>
                    <a href='aprobar.php?id=" . $row['carpenter_id'] . "' 
                       class='flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold shadow text-center transition'>Aceptar</a>

                     <button onclick='abrirModalRechazo(" . $row['carpenter_id'] . ")' 
                        class='flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold shadow text-center transition'>Rechazar</button>
                   </div>
                </div>
                ";
            }

            echo "</div>";

        } else {
            echo "<div class='bg-white p-8 rounded-xl shadow-md border border-stone-200 text-center'>
                    <svg xmlns='http://www.w3.org/2000/svg' class='h-16 w-16 mx-auto text-stone-300 mb-4' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                      <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' />
                    </svg>
                    <p class='text-stone-600 text-lg'>No hay solicitudes pendientes.</p>
                  </div>";
        }
        ?>
      </section>

      <!-- Reportes -->
      <section id="reportes" class="seccion hidden">
        <h1 class="text-3xl font-extrabold text-stone-800 mb-6">Reportes de Clientes</h1>
        <div class="bg-white p-6 rounded-xl shadow-md border border-stone-200">
          <p class="font-semibold">Reporte contra: Carpintero Luis GÃ³mez</p>
          <p class="text-stone-600">Cliente: MarÃ­a RodrÃ­guez</p>
          <p class="text-stone-600">Motivo: El proyecto no cumpliÃ³ con lo acordado.</p>
          <div class="flex gap-3 mt-4">
            <button onclick="abrirModalReporte('Luis GÃ³mez','MarÃ­a RodrÃ­guez','El proyecto no cumpliÃ³ lo acordado.')" class="bg-stone-600 hover:bg-stone-700 text-white px-4 py-2 rounded-lg font-semibold shadow">Ver detalle</button>
            <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold shadow">Eliminar</button>
            <button class="bg-stone-500 hover:bg-stone-600 text-white px-4 py-2 rounded-lg font-semibold shadow">Ignorar</button>
          </div>
        </div>
      </section>

      <!-- Sugerencias -->
      <section id="sugerencias" class="seccion hidden">
        <h1 class="text-3xl font-extrabold text-stone-800 mb-6">Sugerencias</h1>
        <div class="bg-white p-6 rounded-xl shadow-md border border-stone-200">
          <p class="font-semibold">Usuario: Pedro RamÃ­rez</p>
          <p class="text-stone-600">"SerÃ­a bueno tener chat con el carpintero."</p>
        </div>
      </section>

      <!-- Gestionar Carpinteros Aprobados -->
      <section id="gestionar_carpinteros" class="seccion hidden">
        <h1 class="text-3xl font-extrabold text-stone-800 mb-6">Gestionar Carpinteros</h1>
        
        <div class="bg-white rounded-xl shadow-md border border-stone-200 overflow-hidden">
          <table class="w-full text-left border-collapse">
            <thead class="bg-stone-50 border-b border-stone-200">
              <tr>
                <th class="px-6 py-4 font-bold text-stone-700">Nombre</th>
                <th class="px-6 py-4 font-bold text-stone-700">Especialidad</th>
                <th class="px-6 py-4 font-bold text-stone-700">Estado</th>
                <th class="px-6 py-4 font-bold text-stone-700">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
              <?php
              $sql_all = "SELECT * FROM carpenters WHERE approved = 1 ORDER BY created_at DESC";
              $res_all = $conn->query($sql_all);
              
              if($res_all && $res_all->num_rows > 0) {
                while($c = $res_all->fetch_assoc()) {
                  $status_val = $c['is_active'] ?? 1;
                  $status_label = ($status_val == 1) ? 'Activo' : 'Bloqueado';
                  $status_color = ($status_val == 1) ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100';
                  
                  // Parsear info de contacto de la descripciÃ³n
                  $desc = $c['description'] ?? '';
                  $phone_parsed = '';
                  $city_parsed = '';
                  if (preg_match('/Tel.fono:\s*([^|]+)/iu', $desc, $m2)) $phone_parsed = trim($m2[1]);
                  if (preg_match('/Ciudad:\s*([^|]+)/i', $desc, $m3)) $city_parsed = trim($m3[1]);

                  $carp_json = json_encode([
                    'id' => $c['carpenter_id'],
                    'name' => $c['carpenter_name'],
                    'specialties' => $c['specialties'],
                    'experience' => $c['experience_years'],
                    'email' => $c['email'],
                    'phone' => $phone_parsed,
                    'city' => $city_parsed,
                    'description' => $desc,
                    'cv_file' => $c['cv_file'] ?? '',
                    'rating' => $c['rating_avg'] ?? 0,
                    'verified' => $c['is_verified'] ?? 0,
                    'created_at' => $c['created_at'] ?? '',
                    'is_active' => $status_val
                  ], JSON_HEX_APOS | JSON_HEX_QUOT);
                  
                  echo "
                  <tr>
                    <td class='px-6 py-4 font-medium text-stone-800'>".htmlspecialchars($c['carpenter_name'])."</td>
                    <td class='px-6 py-4 text-stone-600'>".htmlspecialchars($c['specialties'])."</td>
                    <td class='px-6 py-4'>
                      <span class='px-3 py-1 rounded-full text-xs font-bold {$status_color}'>{$status_label}</span>
                    </td>
                    <td class='px-6 py-4 flex flex-wrap gap-2'>
                      <button onclick='verPerfilCarpintero({$carp_json}, true)' class='bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-semibold transition'>Ver Info</button>
                      
                      <button onclick='abrirModalEdicion({$carp_json})' class='bg-amber-600 hover:bg-amber-700 text-white px-3 py-1 rounded text-sm font-semibold transition'>Editar</button>
                      
                      <button onclick='toggleEstadoCarpintero({$c['carpenter_id']}, ".($status_val == 1 ? 0 : 1).")' 
                              class='".($status_val == 1 ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700')." text-white px-3 py-1 rounded text-sm font-semibold transition'>
                        ".($status_val == 1 ? 'Bloquear' : 'Activar')."
                      </button>
                    </td>
                  </tr>";
                }
              } else {
                echo "<tr><td colspan='4' class='px-6 py-10 text-center text-stone-500'>No hay carpinteros aprobados aÃºn.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Notificaciones -->
      <section id="notificaciones" class="seccion hidden">
        <h1 class="text-3xl font-extrabold text-stone-800 mb-6">Notificaciones</h1>
        <p class="text-stone-600">No tienes notificaciones nuevas.</p>
      </section>

    </main>
    </div>
  </div>

  <!-- Modal Perfil Completo del Carpintero -->
  <div id="modal-perfil-carpintero" class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center hidden z-50 backdrop-blur-sm">
    <div class="bg-white w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
      <!-- Header del Modal -->
      <div class="bg-gradient-to-r from-amber-600 to-amber-700 p-6 flex justify-between items-center sticky top-0 z-10">
        <h2 class="text-2xl font-bold text-white flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          Perfil Completo del Carpintero
        </h2>
        <button onclick="cerrarModal('modal-perfil-carpintero')" 
          class="text-white hover:bg-white/20 rounded-full p-2 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Contenido del Modal -->
      <div class="p-8">
        <!-- InformaciÃ³n Principal -->
        <div class="flex items-start gap-6 mb-6 pb-6 border-b border-stone-200">
          <div class="bg-stone-100 p-4 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-stone-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="flex-1">
            <h3 id="perfil-nombre" class="text-3xl font-bold text-stone-800 mb-2">Nombre del Carpintero</h3>
            <div class="flex gap-3 items-center mb-3">
              <span id="perfil-especialidad" class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-sm font-semibold">Especialidad</span>
              <span id="perfil-experiencia" class="text-stone-600 font-medium">0 aÃ±os de experiencia</span>
            </div>
            <div id="perfil-verificado-container" class="hidden">
              <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Verificado
              </span>
            </div>
          </div>
        </div>

        <!-- InformaciÃ³n de Contacto -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
          <div class="bg-stone-50 p-4 rounded-lg border border-stone-100">
            <p class="text-xs font-bold text-stone-400 uppercase tracking-wide mb-1">Email</p>
            <p id="perfil-email" class="text-stone-800 font-medium break-all">-</p>
          </div>
          <div class="bg-stone-50 p-4 rounded-lg border border-stone-100">
            <p class="text-xs font-bold text-stone-400 uppercase tracking-wide mb-1">TelÃ©fono</p>
            <p id="perfil-phone" class="text-stone-800 font-medium">-</p>
          </div>
          <div class="bg-stone-50 p-4 rounded-lg border border-stone-100">
            <p class="text-xs font-bold text-stone-400 uppercase tracking-wide mb-1">Ciudad</p>
            <p id="perfil-city" class="text-stone-800 font-medium">-</p>
          </div>
          <div class="bg-stone-50 p-4 rounded-lg border border-stone-100">
            <p class="text-xs font-bold text-stone-400 uppercase tracking-wide mb-1">CalificaciÃ³n Promedio</p>
            <p id="perfil-rating" class="text-amber-600 font-bold text-lg">â˜… 0.0</p>
          </div>
        </div>

        <!-- DescripciÃ³n -->
        <div class="mb-6">
          <p class="text-xs font-bold text-stone-400 uppercase tracking-wide mb-2">DescripciÃ³n / InformaciÃ³n Adicional</p>
          <div id="perfil-descripcion" class="text-stone-700 bg-stone-50 p-4 rounded-lg border border-stone-100 whitespace-pre-wrap">
            Sin descripciÃ³n disponible
          </div>
        </div>

        <!-- CV -->
        <div class="mb-6">
          <div id="perfil-cv-container" class="hidden bg-purple-50 p-4 rounded-lg border border-purple-100">
            <p class="text-xs font-bold text-purple-400 uppercase tracking-wide mb-2">Hoja de Vida (CV)</p>
            <a id="perfil-cv" href="#" target="_blank" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold transition shadow-md">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Descargar CV (PDF)
            </a>
          </div>
        </div>

        <!-- Fecha de Registro -->
        <div class="bg-stone-50 p-4 rounded-lg border border-stone-100 mb-6">
          <p class="text-xs font-bold text-stone-400 uppercase tracking-wide mb-1">Fecha de Registro</p>
          <p id="perfil-fecha" class="text-stone-800 font-medium">-</p>
        </div>

        <!-- Acciones -->
        <div class="flex gap-3 pt-4 border-t border-stone-200">
          <button onclick="cerrarModal('modal-perfil-carpintero')" 
            class="flex-1 px-5 py-3 rounded-lg text-stone-600 hover:bg-stone-100 font-medium transition border border-stone-300">
            Cerrar
          </button>
          <a id="perfil-btn-aprobar" href="#" 
            class="flex-1 px-6 py-3 rounded-lg bg-green-600 text-white hover:bg-green-700 font-bold shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5 text-center">
            Aprobar Carpintero
          </a>
          <button id="perfil-btn-rechazar" onclick="abrirModalRechazoConId()"
            class="flex-1 px-6 py-3 rounded-lg bg-red-600 text-white hover:bg-red-700 font-bold shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5 text-center">
            Rechazar
          </button>
        </div>
      </div>
    </div>
  </div>


  <!-- Modal EdiciÃ³n de Carpintero -->
  <div id="modal-editar-carpintero" class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center hidden z-50 backdrop-blur-sm">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl p-8">
      <h2 class="text-2xl font-bold text-stone-800 mb-6 pb-2 border-b border-stone-100">Editar InformaciÃ³n del Carpintero</h2>
      
      <form id="form-editar-carpintero" onsubmit="guardarCambiosCarpintero(event)">
        <input type="hidden" id="edit-id" name="id">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-bold text-stone-700 mb-1">Nombre Completo</label>
            <input type="text" id="edit-nombre" name="nombre" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-amber-500 outline-none transition" required>
          </div>
          <div>
            <label class="block text-sm font-bold text-stone-700 mb-1">Email</label>
            <input type="email" id="edit-email" name="email" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-amber-500 outline-none transition" required pattern="[a-zA-Z0-9._%+\\-]+@[a-zA-Z0-9.\\-]+\\.[a-zA-Z]{2,}" title="Debe ser un correo electrÃ³nico vÃ¡lido">
          </div>
          <div>
            <label class="block text-sm font-bold text-stone-700 mb-1">Especialidad</label>
            <input type="text" id="edit-especialidad" name="especialidad" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-amber-500 outline-none transition" required>
          </div>
          <div>
            <label class="block text-sm font-bold text-stone-700 mb-1">AÃ±os de Experiencia</label>
            <input type="number" id="edit-experiencia" name="experiencia" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-amber-500 outline-none transition" required>
          </div>
        </div>
        
        <div class="flex gap-3 mt-8">
          <button type="button" onclick="cerrarModal('modal-editar-carpintero')" class="flex-1 px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold rounded-lg transition">Cancelar</button>
          <button type="submit" class="flex-1 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-lg transition shadow-md">Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Rechazo con Motivo -->
  <div id="modal-rechazo" class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center hidden z-50 backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-8">
      <h2 class="text-xl font-bold text-stone-800 mb-2">Rechazar solicitud</h2>
      <p class="text-stone-500 text-sm mb-5">Escribe el motivo del rechazo. El carpintero lo recibirÃ¡ por correo electrÃ³nico.</p>
      <form id="form-rechazo" method="POST" action="rechazar.php">
        <input type="hidden" id="rechazo-id" name="id" value="">
        <textarea name="motivo" id="rechazo-motivo" required rows="4" placeholder="Explica el motivo del rechazo..."
          class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition resize-none mb-5"></textarea>
        <div class="flex gap-3">
          <button type="button" onclick="cerrarModal('modal-rechazo')"
            class="flex-1 px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold rounded-lg transition">Cancelar</button>
          <button type="submit"
            class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition shadow-md">Confirmar rechazo</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Reporte -->
  <div id="modal-reporte" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl p-8 relative">
      <button onclick="cerrarModal('modal-reporte')" 
        class="absolute top-4 right-4 bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg">Cerrar</button>

      <h2 class="text-2xl font-bold mb-4">Detalle del Reporte</h2>

      <p><strong>Carpintero:</strong> <span id="rep-carpintero"></span></p>
      <p><strong>Cliente:</strong> <span id="rep-cliente"></span></p>
      <p class="mt-2"><strong>DescripciÃ³n:</strong></p>
      <p id="rep-descripcion" class="text-stone-700"></p>
    </div>
  </div>

<script>
  function mostrarSeccion(id) {
    // Ocultar todas las secciones
    document.querySelectorAll('.seccion').forEach(sec => sec.classList.add('hidden'));
    
    // Mostrar la secciÃ³n seleccionada
    document.getElementById(id).classList.remove('hidden');
    
    // Actualizar botones activos
    document.querySelectorAll('.seccion-btn').forEach(btn => {
      btn.classList.remove('bg-amber-100', 'text-amber-700');
    });
    
    // Marcar botÃ³n activo
    const activeBtn = document.querySelector(`button[onclick="mostrarSeccion('${id}')"]`);
    if (activeBtn) {
      activeBtn.classList.add('bg-amber-100', 'text-amber-700');
    }
  }

  // Mostrar dashboard por defecto al cargar la pÃ¡gina
  window.addEventListener('DOMContentLoaded', () => {
    mostrarSeccion('dashboard');
  });

  function abrirModalReporte(carpintero, cliente, descripcion) {
    document.getElementById("rep-carpintero").textContent = carpintero;
    document.getElementById("rep-cliente").textContent = cliente;
    document.getElementById("rep-descripcion").textContent = descripcion;
    document.getElementById("modal-reporte").classList.remove("hidden");
  }

  function cerrarModal(id) {
    document.getElementById(id).classList.add("hidden");
  }

  function verPerfilCarpintero(data, esAprobado = false) {
    // Poblar informaciÃ³n bÃ¡sica
    document.getElementById('perfil-nombre').textContent = data.name || '-';
    document.getElementById('perfil-especialidad').textContent = data.specialties || 'Sin especialidad';
    document.getElementById('perfil-experiencia').textContent = (data.experience || 0) + ' aÃ±os de experiencia';
    
    // InformaciÃ³n de contacto
    document.getElementById('perfil-email').textContent = data.email || '-';
    document.getElementById('perfil-phone').textContent = data.phone || '-';
    document.getElementById('perfil-city').textContent = data.city || '-';
    document.getElementById('perfil-rating').textContent = 'â˜… ' + (data.rating || 0).toFixed(1);
    
    // DescripciÃ³n
    document.getElementById('perfil-descripcion').textContent = data.description || 'Sin descripciÃ³n disponible';
    
    // Badge de verificado
    const verificadoContainer = document.getElementById('perfil-verificado-container');
    if (data.verified == 1) {
      verificadoContainer.classList.remove('hidden');
    } else {
      verificadoContainer.classList.add('hidden');
    }
    
    // CV
    const cvContainer = document.getElementById('perfil-cv-container');
    const cvLink = document.getElementById('perfil-cv');
    if (data.cv_file && data.cv_file.trim()) {
      cvLink.href = data.cv_file;
      const filename = data.cv_file.split('/').pop();
      cvLink.setAttribute('download', filename);
      cvContainer.classList.remove('hidden');
    } else {
      cvContainer.classList.add('hidden');
    }
    
    // Fecha de registro
    const fecha = data.created_at ? new Date(data.created_at).toLocaleDateString('es-ES', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    }) : '-';
    document.getElementById('perfil-fecha').textContent = fecha;
    
    // Botones de acciÃ³n
    const btnAprobar = document.getElementById('perfil-btn-aprobar');
    const btnRechazar = document.getElementById('perfil-btn-rechazar');
    
    if (esAprobado) {
      btnAprobar.classList.add('hidden');
      btnRechazar.classList.add('hidden');
    } else {
      btnAprobar.classList.remove('hidden');
      btnRechazar.classList.remove('hidden');
      btnAprobar.href = 'aprobar.php?id=' + data.id;
      // Guardar el ID para el modal de rechazo
      document.getElementById('rechazo-id').value = data.id;
    }
    
    // Mostrar modal
    document.getElementById('modal-perfil-carpintero').classList.remove('hidden');
  }

  function toggleEstadoCarpintero(id, nuevoEstado) {
    if(!confirm("Â¿Deseas ".concat(nuevoEstado == 0 ? "bloquear" : "activar", " este perfil?"))) return;
    
    const formData = new FormData();
    formData.append('id', id);
    formData.append('status', nuevoEstado);
    formData.append('action', 'toggle_status');
    
    fetch('admin_manage_carpenter.php', {
      method: 'POST',
      body: formData
    })
    .then(r => r.json())
    .then(data => {
      if(data.success) {
        location.reload();
      } else {
        alert("Error: " + data.message);
      }
    });
  }

  function abrirModalEdicion(data) {
    document.getElementById('edit-id').value = data.id;
    document.getElementById('edit-nombre').value = data.name;
    document.getElementById('edit-email').value = data.email;
    document.getElementById('edit-especialidad').value = data.specialties;
    document.getElementById('edit-experiencia').value = data.experience;
    document.getElementById('modal-editar-carpintero').classList.remove('hidden');
  }

  function guardarCambiosCarpintero(e) {
    e.preventDefault();
    const form = document.getElementById('form-editar-carpintero');
    const formData = new FormData(form);
    formData.append('action', 'update_info');
    
    fetch('admin_manage_carpenter.php', {
      method: 'POST',
      body: formData
    })
    .then(r => r.json())
    .then(data => {
      if(data.success) {
        alert("InformaciÃ³n actualizada con Ã©xito");
        location.reload();
      } else {
        alert("Error: " + data.message);
      }
    });
  }
  function abrirModalRechazo(id) {
    document.getElementById('rechazo-id').value = id;
    document.getElementById('rechazo-motivo').value = '';
    document.getElementById('modal-rechazo').classList.remove('hidden');
  }

  function abrirModalRechazoConId() {
    // Usa el ID ya guardado por verPerfilCarpintero
    document.getElementById('rechazo-motivo').value = '';
    cerrarModal('modal-perfil-carpintero');
    document.getElementById('modal-rechazo').classList.remove('hidden');
  }

</script>

</body>
</html>
