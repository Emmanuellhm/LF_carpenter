<?php
include 'db_conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    
    // Check users table
    $sql1 = "SELECT email FROM users WHERE email = ? LIMIT 1";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("s", $email);
    $stmt1->execute();
    $res1 = $stmt1->get_result();
    
    if ($res1->num_rows > 0) {
        echo json_encode(["exists" => true]);
        exit;
    }
    
    // Check carpenters table
    $sql2 = "SELECT email FROM carpenters WHERE email = ? LIMIT 1";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s", $email);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    
    if ($res2->num_rows > 0) {
        echo json_encode(["exists" => true]);
        exit;
    }
    
    echo json_encode(["exists" => false]);
    exit;
}
?>
