<?php
include 'db_conexion.php';

echo "<h2>Gestión de Carpinteros - Borrar Registros</h2>";
echo "<style>
    body { font-family: Arial; padding: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #d97706; color: white; }
    .delete-btn { background: red; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; }
    .delete-btn:hover { background: darkred; }
    .warning { background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0; }
    .success { background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0; }
</style>";

// Si se solicita borrar
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $sql = "DELETE FROM carpenters WHERE carpenter_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "<div class='success'>✅ Carpintero ID $id eliminado correctamente</div>";
    } else {
        echo "<div class='warning'>❌ Error al eliminar: " . $conn->error . "</div>";
    }
}

// Botones de acciones masivas
echo "<div style='margin: 20px 0;'>";
echo "<a href='?delete_all_pending=1' onclick='return confirm(\"¿Borrar TODOS los carpinteros pendientes?\")' style='background: #ff6b6b; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Borrar Todos Pendientes</a>";
echo "<a href='?delete_all=1' onclick='return confirm(\"¿Borrar TODOS los carpinteros? Esta acción NO se puede deshacer.\")' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Borrar TODOS</a>";
echo "</div>";

// Borrar todos pendientes
if (isset($_GET['delete_all_pending'])) {
    $sql = "DELETE FROM carpenters WHERE approved = 0";
    if ($conn->query($sql)) {
        echo "<div class='success'>✅ Todos los carpinteros pendientes eliminados</div>";
    }
}

// Borrar todos
if (isset($_GET['delete_all'])) {
    $sql = "DELETE FROM carpenters";
    if ($conn->query($sql)) {
        echo "<div class='success'>✅ TODOS los carpinteros eliminados</div>";
    }
}

// Mostrar lista de carpinteros
$result = $conn->query("SELECT carpenter_id, carpenter_name, email, description, cv_file, approved, created_at FROM carpenters ORDER BY created_at DESC");

echo "<h3>Lista de Carpinteros Registrados:</h3>";

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Descripción</th>
            <th>CV</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th>Acción</th>
          </tr>";
    
    while ($row = $result->fetch_assoc()) {
        $estado = $row['approved'] == 1 ? '<span style="color: green;">✅ Aprobado</span>' : '<span style="color: orange;">⏳ Pendiente</span>';
        $cv = $row['cv_file'] ? '✅' : '❌';
        $desc = $row['description'] ? substr($row['description'], 0, 50) . '...' : '<span style="color: red;">SIN DATOS</span>';
        
        echo "<tr>";
        echo "<td>{$row['carpenter_id']}</td>";
        echo "<td>" . htmlspecialchars($row['carpenter_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>$desc</td>";
        echo "<td>$cv</td>";
        echo "<td>$estado</td>";
        echo "<td>{$row['created_at']}</td>";
        echo "<td><a href='?delete_id={$row['carpenter_id']}' class='delete-btn' onclick='return confirm(\"¿Eliminar este carpintero?\")'>Borrar</a></td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No hay carpinteros registrados.</p>";
}

$conn->close();
?>

<br><br>
<a href="admin.php" style="background: #d97706; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">← Volver al Panel Admin</a>
