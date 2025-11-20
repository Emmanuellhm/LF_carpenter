<?php
include 'db_connection.php';
$result = $conn->query("
    SELECT c.carpenter_id, u.full_name, u.email, c.specialties, c.experience_years
    FROM carpenters c
    JOIN users u ON c.user_id = u.user_id
    WHERE c.approved = 0
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Administrador</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-50 p-10">
  <h1 class="text-3xl font-bold mb-8">Solicitudes de Carpinteros Pendientes</h1>
  <table class="table-auto w-full bg-white shadow rounded">
    <thead>
      <tr class="bg-amber-100 text-left">
        <th class="p-3">Nombre</th>
        <th class="p-3">Email</th>
        <th class="p-3">Especialidad</th>
        <th class="p-3">Experiencia</th>
        <th class="p-3 text-center">Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $result->fetch_assoc()): ?>
        <tr class="border-b">
          <td class="p-3"><?= $row['full_name'] ?></td>
          <td class="p-3"><?= $row['email'] ?></td>
          <td class="p-3"><?= $row['specialties'] ?></td>
          <td class="p-3"><?= $row['experience_years'] ?> años</td>
          <td class="p-3 text-center">
            <a href="aprobar_carpintero.php?id=<?= $row['carpenter_id'] ?>" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">Aprobar</a>
            <a href="rechazar_carpintero.php?id=<?= $row['carpenter_id'] ?>" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Rechazar</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</body>
</html>
