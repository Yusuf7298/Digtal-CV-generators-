<?php
$host = 'sql102.infinityfree.com';
$dbname = 'if0_41826190_digital_cv';
$user = 'if0_41826190';
$pass = 'MIDRBxzOwhmOp';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    echo "Database connection successful!";
    
    // Check if manager exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = 'manager@digitalcv.infinityfree.me'");
    $stmt->execute();
    $manager = $stmt->fetch();
    
    if ($manager) {
        echo "Manager account found!";
    } else {
        echo "Manager account NOT found!";
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>