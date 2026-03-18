<?php
// Script de utilidad para gestionar carpinteros en la BD
// IMPORTANTE: Proteger este archivo en producción

session_start();
include 'db_conexion.php';

// Verificar si hay acción
$action = $_GET['action'] ?? '';
$carpenter_id = $_GET['id'] ?? 0;

if ($action === 'delete' && $carpenter_id > 0) {
    // Eliminar carpintero
    $stmt = $conn->prepare("DELETE FROM carpenters WHERE carpenter_id = ?");
    $stmt->bind_param('i', $carpenter_id);
    if ($stmt->execute()) {
        $message = "✅ Carpintero ID {$carpenter_id} eliminado correctamente.";
    } else {
        $message = "❌ Error al eliminar: " . $conn->error;
    }
    $stmt->close();
}

if ($action === 'approve' && $carpenter_id > 0) {
    // Aprobar carpintero
    $stmt = $conn->prepare("UPDATE carpenters SET approved = 1 WHERE carpenter_id = ?");
    $stmt->bind_param('i', $carpenter_id);
    if ($stmt->execute()) {
        $message = "✅ Carpintero ID {$carpenter_id} aprobado correctamente.";
    } else {
        $message = "❌ Error al aprobar: " . $conn->error;
    }
    $stmt->close();
}

if ($action === 'unapprove' && $carpenter_id > 0) {
    // Desaprobar carpintero
    $stmt = $conn->prepare("UPDATE carpenters SET approved = 0 WHERE carpenter_id = ?");
    $stmt->bind_param('i', $carpenter_id);
    if ($stmt->execute()) {
        $message = "✅ Carpintero ID {$carpenter_id} desaprobado correctamente.";
    } else {
        $message = "❌ Error al desaprobar: " . $conn->error;
    }
    $stmt->close();
}

// Cargar todos los carpinteros
$result = $conn->query("SELECT * FROM carpenters ORDER BY carpenter_id ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Carpinteros - BD</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        h1 { color: #333; }
        .message { padding: 15px; margin: 20px 0; border-radius: 5px; }
        .message.success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .message.error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #007bff; color: white; }
        tr:hover { background: #f8f9fa; }
        .btn { padding: 6px 12px; margin: 2px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 12px; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-approve { background: #28a745; color: white; }
        .btn-unapprove { background: #ffc107; color: black; }
        .btn:hover { opacity: 0.8; }
        .status-approved { color: #28a745; font-weight: bold; }
        .status-pending { color: #ffc107; font-weight: bold; }
    </style>
</head>
<body>
    <h1>🛠️ Gestión de Carpinteros en Base de Datos</h1>
    
    <?php if (isset($message)): ?>
        <div class="message <?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Especialidad</th>
                <th>Experiencia</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['carpenter_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['carpenter_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($row['specialties'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($row['experience_years'] ?? ''); ?> años</td>
                    <td>
                        <?php if ($row['approved'] == 1): ?>
                            <span class="status-approved">✅ Aprobado</span>
                        <?php else: ?>
                            <span class="status-pending">⏳ Pendiente</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['approved'] == 1): ?>
                            <a href="?action=unapprove&id=<?php echo $row['carpenter_id']; ?>" 
                               class="btn btn-unapprove"
                               onclick="return confirm('¿Desaprobar este carpintero?')">
                                Desaprobar
                            </a>
                        <?php else: ?>
                            <a href="?action=approve&id=<?php echo $row['carpenter_id']; ?>" 
                               class="btn btn-approve"
                               onclick="return confirm('¿Aprobar este carpintero?')">
                                Aprobar
                            </a>
                        <?php endif; ?>
                        
                        <a href="?action=delete&id=<?php echo $row['carpenter_id']; ?>" 
                           class="btn btn-delete"
                           onclick="return confirm('¿ELIMINAR permanentemente este carpintero?\n\nEsta acción NO se puede deshacer.')">
                            Eliminar
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <p style="margin-top: 20px; color: #666;">
        <strong>⚠️ ADVERTENCIA:</strong> Este es un script de utilidad para desarrollo. 
        En producción debe estar protegido con autenticación de administrador.
    </p>
</body>
</html>
