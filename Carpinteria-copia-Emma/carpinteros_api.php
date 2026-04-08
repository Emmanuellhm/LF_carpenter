<?php
ini_set('display_errors', 0);
error_reporting(0);
ob_start(); // Iniciar buffer de salida
include 'db_conexion.php';
session_start();
ob_clean(); // Limpiar cualquier output previo
header('Content-Type: application/json; charset=utf-8');

// Requiere estar autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$action = $_GET['action'] ?? 'list';

if ($action === 'list') {
    $q = $_GET['q'] ?? '';
    $param = '%' . $q . '%';
    
    // Obtener SOLO carpinteros de tabla users con role='carpenter' (ya aprobados por el admin)
    $stmt = $conn->prepare("SELECT user_id, full_name, city, phone, email FROM users WHERE role = 'carpenter' AND (full_name LIKE ? OR city LIKE ?) ORDER BY full_name ASC");
    $stmt->bind_param('ss', $param, $param);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $rows[] = $r;
    }
    $stmt->close();
    
    echo json_encode($rows);
    exit;
    
    
} elseif ($action === 'profile') {
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) { echo json_encode(['error' => 'ID inválido']); exit; }
    
    // Buscar en users primero
    $stmt = $conn->prepare("SELECT user_id, full_name, email, phone, city FROM users WHERE user_id = ? AND role = 'carpenter' LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        
        // Buscar información adicional en la tabla carpenters (especialidades, experiencia, etc.)
        $carpInfo = null;
        $stmtCarp = $conn->prepare("SELECT specialties, experience_years, description FROM carpenters WHERE carpenter_name = ? AND approved = 1 LIMIT 1");
        $stmtCarp->bind_param('s', $row['full_name']);
        $stmtCarp->execute();
        $resCarp = $stmtCarp->get_result();
        if ($resCarp && $resCarp->num_rows > 0) {
            $carpInfo = $resCarp->fetch_assoc();
        }
        $stmtCarp->close();
        
        // Agregar información adicional al perfil
        $row['specialties'] = $carpInfo['specialties'] ?? 'No especificada';
        $row['experience_years'] = $carpInfo['experience_years'] ?? 0;
        $row['work_zones'] = $row['city'] ?? 'No especificada';
        $row['availability'] = 'Disponible';
        
        // Cargar proyectos
        $row['projects'] = [];
        $chk = $conn->query("SHOW TABLES LIKE 'portafolio'");
        if ($chk && $chk->num_rows > 0) {
            $sp = $conn->prepare("SELECT project_id, title, description, image_path, price, created_at FROM portafolio WHERE carpenter_user_id = ? ORDER BY created_at DESC");
            $sp->bind_param('i', $id);
            $sp->execute();
            $rp = $sp->get_result();
            while ($pp = $rp->fetch_assoc()) {
                if (!empty($pp['image_path'])) {
                    $pp['image_path'] = str_replace('\\', '/', $pp['image_path']);
                }
                $row['projects'][] = $pp;
            }
            $sp->close();
        }
        echo json_encode($row);
        $stmt->close();
        exit;
    }
    $stmt->close();
    
    // Si no está en users, buscar en carpenters
    $stmt = $conn->prepare("SELECT carpenter_id as user_id, carpenter_name as full_name, specialties, experience_years, description FROM carpenters WHERE carpenter_id = ? AND approved = 1 LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        
        // Parsear información del campo description
        $city = 'No especificada';
        $phone = 'No disponible';
        $email = '';
        if (!empty($row['description'])) {
            if (preg_match('/Ciudad:\s*([^|]+)/i', $row['description'], $m)) {
                $city = trim($m[1]);
            }
            if (preg_match('/Tel(?:efo?n)?:\s*([^|]+)/i', $row['description'], $m2)) {
                $phone = trim($m2[1]);
            }
            if (preg_match('/Email:\s*([^|]+)/i', $row['description'], $m3)) {
                $email = trim($m3[1]);
            }
        }
        
        $row['city'] = $city;
        $row['phone'] = $phone;
        $row['email'] = $email;
        $row['work_zones'] = $city;
        $row['availability'] = 'Disponible';
        $row['projects'] = [];
        
        // Intentar encontrar un usuario vinculado por nombre completo (si existe)
        $linkedUserId = null;
        $chkUser = $conn->prepare("SELECT user_id FROM users WHERE full_name = ? LIMIT 1");
        $chkUser->bind_param('s', $row['full_name']);
        $chkUser->execute();
        $rchk = $chkUser->get_result();
        if ($rchk && $rchk->num_rows > 0) {
            $linkedUserId = $rchk->fetch_assoc()['user_id'];
        }
        $chkUser->close();

        // Si tenemos un user_id vinculado, buscar proyectos en portafolio usando ese user_id
        if ($linkedUserId) {
            $chk = $conn->query("SHOW TABLES LIKE 'portafolio'");
            if ($chk && $chk->num_rows > 0) {
                $sp = $conn->prepare("SELECT project_id, title, description, image_path, price, created_at FROM portafolio WHERE carpenter_user_id = ? ORDER BY created_at DESC");
                $sp->bind_param('i', $linkedUserId);
                $sp->execute();
                $rp = $sp->get_result();
                while ($pp = $rp->fetch_assoc()) {
                        if (!empty($pp['image_path'])) {
                            $pp['image_path'] = str_replace('\\', '/', $pp['image_path']);
                        }
                        $row['projects'][] = $pp;
                }
                $sp->close();
            }
        }
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'No encontrado']);
    }
    $stmt->close();
    exit;
    
} else {
    echo json_encode(['error' => 'Acción no soportada']);
    exit;
}

?>
