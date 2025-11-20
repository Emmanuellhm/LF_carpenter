<?php
include 'db_conexion.php';
session_start();

// Solo el administrador puede acceder
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: iniciar-seccion.html");
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
  <aside class="w-64 bg-white shadow-xl flex flex-col border-r border-stone-200 rounded-xl mt-6 mb-6 ml-4 h-auto max-h-[90vh] overflow-y-auto">
    <div>
      <div class="flex flex-col items-center py-8 border-b border-stone-200">
        <img src="img/fotoP.jpg" alt="Foto Admin" class="w-28 h-28 rounded-full border-4 border-amber-600 object-cover shadow-md">
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
    <div 
  onclick="cerrarSesion()" 
  class="text-center py-5 text-stone-500 hover:text-amber-600 cursor-pointer font-medium border-t border-stone-200">
  Cerrar sesión
</div>

<script>
  function cerrarSesion() {
    window.location.href = "logout.php";
  }
</script>
  </aside>

  <!-- Main -->
  <div class="flex-1 flex flex-col">
    <!-- Header -->
    <header class="flex items-center justify-between bg-white border-b border-stone-200 px-6 h-20 shadow-sm">
      <div class="logo">
        <img src="img/Logo de Carpintería LF.png" alt="Logo" class="h-16 w-auto">
      </div>
      <nav>
        <a href="contacto.html" class="font-semibold text-stone-700 hover:text-amber-600">Contáctanos</a>
      </nav>
    </header>

    <!-- Contenido -->
    <main class="p-10 space-y-10">

      <!-- 🧱 Sección: Solicitudes Carpinteros -->
      <section id="solicitudes" class="seccion">
        <h1 class="text-3xl font-extrabold text-stone-800 mb-6">Solicitudes de Carpinteros Pendientes</h1>

        <?php
        $sql = "SELECT carpenter_id, carpenter_name, specialties, experience_years, description 
                FROM carpenters WHERE approved = 0";
        $result = $conn->query($sql);

        if (!$result) {
            echo "<p class='text-red-600 font-semibold'>Error en la consulta: " . $conn->error . "</p>";
        } elseif ($result->num_rows > 0) {
            echo "<div class='space-y-4'>";
            while ($row = $result->fetch_assoc()) {
                echo "
                <div class='bg-white p-6 rounded-xl shadow-md border border-stone-200'>
                  <p class='font-semibold text-stone-800'>👷 Carpintero: " . htmlspecialchars($row['carpenter_name']) . "</p>
                  <p class='text-stone-600 text-sm'>Especialidad: " . htmlspecialchars($row['specialties']) . "</p>
                  <p class='text-stone-600 text-sm'>Experiencia: " . intval($row['experience_years']) . " años</p>
                  <p class='text-stone-600 text-sm mt-2'>" . htmlspecialchars($row['description']) . "</p>
                  <div class='flex gap-3 mt-4'>
                    <a href='aprobar.php?id=" . $row['carpenter_id'] . "' class='bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold shadow'>Aceptar</a>
                    <a href='rechazar.php?id=" . $row['carpenter_id'] . "' class='bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold shadow'>Rechazar</a>
                  </div>
                </div>
                ";
            }
            echo "</div>";
        } else {
            echo "<p class='text-stone-600'>No hay solicitudes pendientes.</p>";
        }
        ?>
      </section>

      <!-- 🧾 Sección: Reportes de Clientes -->
      <section id="reportes" class="seccion hidden">
        <h1 class="text-3xl font-extrabold text-stone-800 mb-6">Reportes de Clientes</h1>
        <div class="bg-white p-6 rounded-xl shadow-md border border-stone-200">
          <p class="font-semibold">Reporte contra: Carpintero Luis Gómez</p>
          <p class="text-stone-600">Cliente: María Rodríguez</p>
          <p class="text-stone-600">Motivo: El proyecto no cumplió con lo acordado.</p>
          <div class="flex gap-3 mt-4">
            <button onclick="abrirModalReporte('Luis Gómez','María Rodríguez','El proyecto no cumplió con lo acordado. Se entregó tarde y con defectos.')" class="bg-stone-600 hover:bg-stone-700 text-white px-4 py-2 rounded-lg font-semibold shadow">Ver detalle</button>
            <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold shadow">Eliminar Carpintero</button>
            <button class="bg-stone-500 hover:bg-stone-600 text-white px-4 py-2 rounded-lg font-semibold shadow">Ignorar</button>
          </div>
        </div>
      </section>

      <!-- 💬 Sección: Sugerencias -->
      <section id="sugerencias" class="seccion hidden">
        <h1 class="text-3xl font-extrabold text-stone-800 mb-6">Sugerencias de Usuarios</h1>
        <div class="bg-white p-6 rounded-xl shadow-md border border-stone-200">
          <p class="font-semibold">Usuario: Pedro Ramírez</p>
          <p class="text-stone-600">"Sería bueno agregar un chat directo con los carpinteros."</p>
        </div>
      </section>

      <!-- 🔔 Sección: Notificaciones -->
      <section id="notificaciones" class="seccion hidden">
        <h1 class="text-3xl font-extrabold text-stone-800 mb-6">Notificaciones</h1>
        <div id="lista-notificaciones" class="space-y-4">
          <p class="text-stone-600">No tienes notificaciones nuevas.</p>
        </div>
      </section>

    </main>
  </div>

  <!-- Modal de Reporte -->
  <div id="modal-reporte" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl p-8 relative">
      <button onclick="cerrarModal('modal-reporte')" class="absolute top-4 right-4 bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg">Cerrar</button>
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

    function cerrarSesion() {
      window.location.href = "index2.html";
    }
  </script>
</body>
</html>
