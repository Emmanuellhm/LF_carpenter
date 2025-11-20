<?php
include 'db_conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM carpenters WHERE carpenter_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('❌ Solicitud eliminada'); window.location.href='admin.php';</script>";
    } else {
        echo "❌ Error al eliminar: " . $conn->error;
    }
    $stmt->close();
}
$conn->close();
?>
