<?php
session_start();
include 'db_conexion.php';

// Seguridad: solo admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: iniciar-seccion.php?error=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Administrador - LF Carpinter</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex min-h-screen bg-gradient-to-br from-amber-50 to-stone-100">

  <!-- Sidebar -->
  <aside class="w-64 bg-white shadow-xl flex flex-col justify-between border-r border-stone-200">
    
    <div>
      <div class="flex flex-col items-center py-8 border-b border-stone-200">
        <img src="img/fotoP.jpg" class="w-28 h-28 rounded-full border-4 border-amber-600 object-cover shadow-md">
        <span class="mt-3 font-bold text-stone-800 text-lg">Administrador</span>
      </div>

      <nav class="flex flex-col space-y-2 px-6 mt-6">
        <button onclick="mostrarSeccion('solicitudes')" class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-2 font-medium text-left">Solicitudes Carpinteros</button>
        <button onclick="mostrarSeccion('reportes')" class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-2 font-medium text-left">Reportes de Clientes</button>
        <button onclick="mostrarSeccion('sugerencias')" class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-2 font-medium text-left">Sugerencias</button>
        <button onclick="mostrarSeccion('notificaciones')" class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-2 font-medium text-left">Notificaciones</button>
      </nav>
    </div>

    <!-- Cerrar sesión -->
    <a href="logout.php"
       class="text-center py-5 text-stone-500 hover:text-amber-600 cursor-pointer font-medium border-t border-stone-200">
      Cerrar sesión
    </a>
  </aside>

  <!-- Main content -->
  <div class="flex-1 flex flex-col">

    <header class="flex items-center justify-between bg-white border-b border-stone-200 px-6 h-20 shadow-sm">
      <img src="img/Logo de Carpintería LF.png" class="h-16">
      <nav>
        <a href="contacto.php" class="font-semibold text-stone-700 hover:text-amber-600">Contáctanos</a>
      </nav>
    </header>

    <main class="p-10 space-y-10">

      <!-- Solicitudes -->
      <section id="solicitudes" class="seccion">
        <h1 class="text-3xl font-extrabold text-stone-800 mb-6">Solicitudes de Carpinteros Pendientes</h1>

        <?php
        $sql = "SELECT * FROM carpenters WHERE approved = 0";
        $result = $conn->query($sql);

        if (!$result) {
            echo "<p class='text-red-600 font-semibold'>Error en la consulta: {$conn->error}</p>";
        } elseif ($result->num_rows > 0) {
            echo "<div class='grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6'>";

            while ($row = $result->fetch_assoc()) {
                // Parsear información de la descripción
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
                      <span class='font-semibold'>Experiencia:</span> " . intval($row['experience_years'] ?? 0) . " años
                    </p>
                    <p class='text-sm text-stone-600 line-clamp-2'>
                      <span class='font-semibold'>Descripción:</span> " . htmlspecialchars(substr($row['description'] ?? '-', 0, 100)) . "...
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

                    <a href='rechazar.php?id=" . $row['carpenter_id'] . "' 
                       class='flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold shadow text-center transition'>Rechazar</a>
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
          <p class="font-semibold">Reporte contra: Carpintero Luis Gómez</p>
          <p class="text-stone-600">Cliente: María Rodríguez</p>
          <p class="text-stone-600">Motivo: El proyecto no cumplió con lo acordado.</p>
          <div class="flex gap-3 mt-4">
            <button onclick="abrirModalReporte('Luis Gómez','María Rodríguez','El proyecto no cumplió lo acordado.')" class="bg-stone-600 hover:bg-stone-700 text-white px-4 py-2 rounded-lg font-semibold shadow">Ver detalle</button>
            <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold shadow">Eliminar</button>
            <button class="bg-stone-500 hover:bg-stone-600 text-white px-4 py-2 rounded-lg font-semibold shadow">Ignorar</button>
          </div>
        </div>
      </section>

      <!-- Sugerencias -->
      <section id="sugerencias" class="seccion hidden">
        <h1 class="text-3xl font-extrabold text-stone-800 mb-6">Sugerencias</h1>
        <div class="bg-white p-6 rounded-xl shadow-md border border-stone-200">
          <p class="font-semibold">Usuario: Pedro Ramírez</p>
          <p class="text-stone-600">"Sería bueno tener chat con el carpintero."</p>
        </div>
      </section>

      <!-- Notificaciones -->
      <section id="notificaciones" class="seccion hidden">
        <h1 class="text-3xl font-extrabold text-stone-800 mb-6">Notificaciones</h1>
        <p class="text-stone-600">No tienes notificaciones nuevas.</p>
      </section>

    </main>
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
        <!-- Información Principal -->
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
              <span id="perfil-experiencia" class="text-stone-600 font-medium">0 años de experiencia</span>
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

        <!-- Información de Contacto -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
          <div class="bg-stone-50 p-4 rounded-lg border border-stone-100">
            <p class="text-xs font-bold text-stone-400 uppercase tracking-wide mb-1">Email</p>
            <p id="perfil-email" class="text-stone-800 font-medium break-all">-</p>
          </div>
          <div class="bg-stone-50 p-4 rounded-lg border border-stone-100">
            <p class="text-xs font-bold text-stone-400 uppercase tracking-wide mb-1">Teléfono</p>
            <p id="perfil-phone" class="text-stone-800 font-medium">-</p>
          </div>
          <div class="bg-stone-50 p-4 rounded-lg border border-stone-100">
            <p class="text-xs font-bold text-stone-400 uppercase tracking-wide mb-1">Ciudad</p>
            <p id="perfil-city" class="text-stone-800 font-medium">-</p>
          </div>
          <div class="bg-stone-50 p-4 rounded-lg border border-stone-100">
            <p class="text-xs font-bold text-stone-400 uppercase tracking-wide mb-1">Calificación Promedio</p>
            <p id="perfil-rating" class="text-amber-600 font-bold text-lg">★ 0.0</p>
          </div>
        </div>

        <!-- Descripción -->
        <div class="mb-6">
          <p class="text-xs font-bold text-stone-400 uppercase tracking-wide mb-2">Descripción / Información Adicional</p>
          <div id="perfil-descripcion" class="text-stone-700 bg-stone-50 p-4 rounded-lg border border-stone-100 whitespace-pre-wrap">
            Sin descripción disponible
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
          <a id="perfil-btn-rechazar" href="#" 
            class="flex-1 px-6 py-3 rounded-lg bg-red-600 text-white hover:bg-red-700 font-bold shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5 text-center">
            Rechazar
          </a>
        </div>
      </div>
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
      <p class="mt-2"><strong>Descripción:</strong></p>
      <p id="rep-descripcion" class="text-stone-700"></p>
    </div>
  </div>

<script>
  function mostrarSeccion(id) {
    document.querySelectorAll('.seccion').forEach(sec => sec.classList.add('hidden'));
    document.getElementById(id).classList.remove('hidden');
  }

  function abrirModalReporte(carpintero, cliente, descripcion) {
    document.getElementById("rep-carpintero").textContent = carpintero;
    document.getElementById("rep-cliente").textContent = cliente;
    document.getElementById("rep-descripcion").textContent = descripcion;
    document.getElementById("modal-reporte").classList.remove("hidden");
  }

  function cerrarModal(id) {
    document.getElementById(id).classList.add("hidden");
  }

  function verPerfilCarpintero(data) {
    // Poblar información básica
    document.getElementById('perfil-nombre').textContent = data.name || '-';
    document.getElementById('perfil-especialidad').textContent = data.specialties || 'Sin especialidad';
    document.getElementById('perfil-experiencia').textContent = (data.experience || 0) + ' años de experiencia';
    
    // Información de contacto
    document.getElementById('perfil-email').textContent = data.email || '-';
    document.getElementById('perfil-phone').textContent = data.phone || '-';
    document.getElementById('perfil-city').textContent = data.city || '-';
    document.getElementById('perfil-rating').textContent = '★ ' + (data.rating || 0).toFixed(1);
    
    // Descripción
    document.getElementById('perfil-descripcion').textContent = data.description || 'Sin descripción disponible';
    
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
      const filename = data.cv_file.split('/').pop(); // Obtener nombre del archivo
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
    
    // Botones de acción
    document.getElementById('perfil-btn-aprobar').href = 'aprobar.php?id=' + data.id;
    document.getElementById('perfil-btn-rechazar').href = 'rechazar.php?id=' + data.id;
    
    // Mostrar modal
    document.getElementById('modal-perfil-carpintero').classList.remove('hidden');
  }
</script>

</body>
</html>