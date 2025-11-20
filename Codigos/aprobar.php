<?php
include 'db_conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "UPDATE carpenters SET approved = 1, is_verified = 1 WHERE carpenter_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Carpintero aprobado con éxito'); window.location.href='admin.php';</script>";
    } else {
        echo "❌ Error al aprobar: " . $conn->error;
    }
    $stmt->close();
}
$conn->close();
?>
