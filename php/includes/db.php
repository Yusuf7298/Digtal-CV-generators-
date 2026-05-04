<?php
/**
 * Database Configuration and Connection
 * Schema matches database.sql — roles/permissions RBAC, proper column names
 */

// Disable error display to prevent HTML in JSON response
ini_set('display_errors', 0);
error_reporting(E_ALL);

define('DB_HOST', 'sql102.infinityfree.com');
define('DB_NAME', 'if0_41826190_digital_cv');
define('DB_USER', 'if0_41826190');
define('DB_PASS', 'MIDRBxzOwhmOp');
define('DB_CHARSET', 'utf8mb4');

function getDBConnection() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Database connection failed. Check that MySQL is running and database exists.']);
            exit;
        }
    }
    return $pdo;
}

/**
 * Get role_id from role_name
 */
function getRoleId($roleName) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT role_id FROM roles WHERE role_name = ?");
    $stmt->execute([$roleName]);
    $row = $stmt->fetch();
    return $row ? (int)$row['role_id'] : null;
}

/**
 * Get role_name from role_id
 */
function getRoleName($roleId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT role_name FROM roles WHERE role_id = ?");
    $stmt->execute([$roleId]);
    $row = $stmt->fetch();
    return $row ? $row['role_name'] : null;
}

/**
 * Check if user has a specific permission
 */
function userHasPermission($userId, $permissionName) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as cnt FROM role_permissions rp
        JOIN permissions p ON rp.permission_id = p.permission_id
        JOIN users u ON u.role_id = rp.role_id
        WHERE u.user_id = ? AND p.permission_name = ?
    ");
    $stmt->execute([$userId, $permissionName]);
    return (int)$stmt->fetch()['cnt'] > 0;
}

/**
 * Get system setting value
 */
function getSystemSetting($key, $default = null) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT setting_value FROM system_settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? $row['setting_value'] : $default;
}
