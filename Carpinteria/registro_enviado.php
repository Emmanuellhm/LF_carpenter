<?php
// Si más adelante quieres mostrar mensajes dinámicos, puedes usar GET:
// Ejemplo: registro_enviado.php?ok=1
$ok = isset($_GET['ok']) ? true : false;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro enviado</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex flex-col items-center justify-center min-h-screen bg-amber-50">

  <div class="bg-white p-10 rounded-2xl shadow-lg text-center">

    <h1 class="text-2xl font-bold text-amber-700 mb-4">
      ✅ Solicitud enviada correctamente
    </h1>

    <p class="text-stone-700 mb-6">
      Tu solicitud como carpintero fue enviada al administrador.<br>
      Te notificaremos cuando sea aprobada.
    </p>

    <a href="iniciar-seccion.php" 
       class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-3 rounded-lg font-semibold">
      Volver al inicio
    </a>

  </div>

</body>
</html>
