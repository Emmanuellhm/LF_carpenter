<?php
session_start();
include 'db_conexion.php';

// Seguridad: solo admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die(json_encode(['success' => false, 'message' => 'No autorizado']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $action = $_POST['action'];

    if ($action === 'toggle_status') {
        $new_status = intval($_POST['status']);
        $sql = "UPDATE carpenters SET is_active = ? WHERE carpenter_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $new_status, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
    } 
    
    elseif ($action === 'update_info') {
        $nombre = trim($_POST['nombre']);
        $especialidad = trim($_POST['especialidad']);
        $experiencia = intval($_POST['experiencia']);
        $email = trim($_POST['email']);
        
        $sql = "UPDATE carpenters SET carpenter_name = ?, specialties = ?, experience_years = ?, email = ? WHERE carpenter_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisi", $nombre, $especialidad, $experiencia, $email, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
    }
}
?>
