<?php
// Test login API directly
header('Content-Type: application/json');

// Simulate POST request
$_POST['email'] = 'manager@digitalcv.infinityfree.me';
$_POST['password'] = 'demo1234';

// Include auth functions
require_once 'php/includes/db.php';
require_once 'php/includes/functions.php';

// Test database connection
$pdo = getDBConnection();
if (!$pdo) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Test user lookup
$stmt = $pdo->prepare("SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.role_id WHERE u.email = ?");
$stmt->execute(['manager@digitalcv.infinityfree.me']);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode(['error' => 'User not found']);
    exit;
}

// Test password verification
if (password_verify('demo1234', $user['password_hash'])) {
    echo json_encode([
        'success' => true,
        'user' => [
            'user_id' => $user['user_id'],
            'email' => $user['email'],
            'role_name' => $user['role_name'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name']
        ]
    ]);
} else {
    echo json_encode(['error' => 'Password verification failed']);
}
?>
