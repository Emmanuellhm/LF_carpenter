<?php
ini_set('display_errors', 0);
error_reporting(0);
include 'db_conexion.php';
session_start();
header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['error' => 'No autenticado']);
        exit;
    }

    $uid = $_SESSION['user_id'];
    $pname = trim($_POST['project_name'] ?? '');
    $pdesc = trim($_POST['project_description'] ?? '');
    $pprice = floatval($_POST['project_price'] ?? 0);

    if ($pname === '') {
        echo json_encode(['error' => 'El título es requerido']);
        exit;
    }

    // Validación básica de archivo
    $imagePath = null;
    if (!empty($_FILES['project_image']['tmp_name'])) {
        $tmp = $_FILES['project_image']['tmp_name'];
        $orig = basename($_FILES['project_image']['name']);
        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp','gif'];
        
        if (!in_array($ext, $allowed)) {
            echo json_encode(['error' => 'Tipo de imagen no permitido']);
            exit;
        }
        
        if (filesize($tmp) > 4 * 1024 * 1024) {
            echo json_encode(['error' => 'Imagen demasiado grande (máx 4MB)']);
            exit;
        }

        $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'projects';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $filename = uniqid('proj_') . '.' . $ext;
        $dest = $uploadDir . DIRECTORY_SEPARATOR . $filename;
        
        if (move_uploaded_file($tmp, $dest)) {
            $imagePath = 'uploads/projects/' . $filename;
        } else {
            echo json_encode(['error' => 'Error al mover la imagen']);
            exit;
        }
    }

    // Verificar/crear tabla portafolio
    $check = $conn->query("SHOW TABLES LIKE 'portafolio'");
    if (!$check || $check->num_rows === 0) {
        $create = "CREATE TABLE IF NOT EXISTS portafolio (
            project_id INT AUTO_INCREMENT PRIMARY KEY,
            carpenter_user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            image_path VARCHAR(255),
            price DECIMAL(12,2) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        if (!$conn->query($create)) {
            echo json_encode(['error' => 'No se pudo crear la tabla portafolio: ' . $conn->error]);
            exit;
        }
    }

    // Insertar proyecto
    $stmtP = $conn->prepare("INSERT INTO portafolio (carpenter_user_id, title, description, image_path, price, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    
    if (!$stmtP) {
        echo json_encode(['error' => 'Error al preparar consulta: ' . $conn->error]);
        exit;
    }
    
    $stmtP->bind_param('isssd', $uid, $pname, $pdesc, $imagePath, $pprice);
    
    if ($stmtP->execute()) {
        $insert_id = $conn->insert_id;
        
        // Obtener proyecto insertado
        $s = $conn->prepare("SELECT * FROM portafolio WHERE project_id = ? LIMIT 1");
        $s->bind_param('i', $insert_id);
        $s->execute();
        $r = $s->get_result();
        $proj = $r ? $r->fetch_assoc() : null;
        $s->close();

        echo json_encode([
            'ok' => true, 
            'project_id' => $insert_id, 
            'project' => $proj
        ]);
    } else {
        echo json_encode(['error' => 'Error al insertar: ' . $stmtP->error]);
    }
    
    $stmtP->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Excepción: ' . $e->getMessage()]);
}
?>
