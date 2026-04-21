<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Historial de Contrataciones - LF Carpinter</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-screen bg-gradient-to-br from-amber-50 to-stone-100">

<!-- Sidebar -->
<aside class="w-64 bg-white shadow-xl flex flex-col justify-between border-r border-stone-200">
  <div>
    <div class="flex flex-col items-center py-8 border-b border-stone-200">
      <!-- Foto de perfil -->
      <img src="img/fotoP.jpg" alt="Foto Perfil"
           class="w-28 h-28 rounded-full border-4 border-amber-600 object-cover shadow-md" 
           id="foto-perfil">
      <span class="mt-3 font-bold text-stone-800 text-lg" id="nombre-usuario">Usuario</span>
    </div>
    <nav class="flex flex-col space-y-2 px-6 mt-6">
      <a href="cliente1.php" 
         class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-2 font-medium">Perfil</a>
      <a href="carpinteros_clientes.php" 
         class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-2 font-medium">Carpinteros</a>
      <a href="historial.php" 
         class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-2 font-medium">Historial Contrataciones</a>
      <a href="solicitudes.php" 
         class="text-stone-700 hover:bg-amber-100 rounded-lg px-4 py-2 font-medium">Solicitudes</a>
    </nav>
  </div>
  <div id="logout" class="text-center py-5 text-stone-500 hover:text-amber-600 cursor-pointer font-medium border-t border-stone-200">
    Cerrar sesiÃ³n
  </div>
</aside>


  <!-- Main -->
  <div class="flex-1 flex flex-col">
    <header class="flex items-center justify-between bg-white border-b border-stone-200 px-6 h-20 shadow-sm">
      <div class="logo">
        <img src="./img/Logo de CarpinterÃ­a LF.png" alt="Logo" class="h-16 w-auto">
      </div>
      <nav>
        <a href="#" class="font-semibold text-stone-700 hover:text-amber-600">ContÃ¡ctanos</a>
      </nav>
    </header>

    <main class="p-10">
      <h1 class="text-3xl font-extrabold text-stone-800 mb-6">Historial de Contrataciones</h1>

      <div id="historial-lista" class="space-y-4">
        <!-- Carpinteros contratados se agregarÃ¡n aquÃ­ -->
      </div>
    </main>
  </div>

  <script>
    // âœ… Cargar datos de la sesiÃ³n
    const userData = JSON.parse(localStorage.getItem("usuarioActivo"));
    if (!userData) {
      window.location.href = "iniciar-sesion.php";
    } else {
      document.getElementById("nombre-usuario").innerText = userData.nombre;
    }

    // âœ… Cerrar sesiÃ³n
    document.getElementById("logout").addEventListener("click", () => {
      localStorage.removeItem("usuarioActivo");
      window.location.href = "iniciar-sesion.php";
    });

    // âœ… Datos de ejemplo de carpinteros contratados (puedes reemplazarlo por datos reales)
    const historial = [
      {
        nombre: "Emmanuel Hincapie",
        proyecto: "Mesa de comedor personalizada",
        fecha: "08/09/2025",
        estado: "Completado âœ…"
      },
      {
        nombre: "AndrÃ©s LÃ³pez",
        proyecto: "Silla ergonÃ³mica",
        fecha: "05/09/2025",
        estado: "Completado âœ…"
      },
      {
        nombre: "Laura MartÃ­nez",
        proyecto: "RestauraciÃ³n de escritorio antiguo",
        fecha: "01/09/2025",
        estado: "Completado âœ…"
      }
    ];

    // âœ… Mostrar historial dinÃ¡micamente
    const lista = document.getElementById("historial-lista");
    historial.forEach(item => {
      const div = document.createElement("div");
      div.className = "bg-white p-6 rounded-xl shadow-lg border border-stone-200";
      div.innerHTML = `
        <p class="font-semibold text-stone-800">ðŸ”§ Carpintero: ${item.nombre}</p>
        <p class="text-stone-600 text-sm">Proyecto: ${item.proyecto}</p>
        <p class="text-stone-600 text-sm">Fecha de contrataciÃ³n: ${item.fecha}</p>
        <p class="text-stone-700 text-sm mt-2">Estado: ${item.estado}</p>
      `;
      lista.appendChild(div);
    });
  </script>
    <!-- Footer (opcional) -->
  <footer class="mt-16 py-8 text-center text-stone-600 border-t border-stone-200">
    <p>&copy; 2025 LF CarpinterÃ­a. Todos los derechos reservados.</p>
  </footer>


</body>
</html>
