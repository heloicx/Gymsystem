<?php
// auth.php - session and authentication helpers
session_start();

include_once 'config.php';

// Ensure admins table exists and seed default admin if none exists (with role support)
function ensure_admin_table() {
    global $conn;
    $create = "CREATE TABLE IF NOT EXISTS admins (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(100) DEFAULT 'Administrator',
        role VARCHAR(30) NOT NULL DEFAULT 'admin',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    mysqli_query($conn, $create);

    // Seed default admin if table empty
    $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM admins");
    if($res) {
        $row = mysqli_fetch_assoc($res);
        if($row['cnt'] == 0) {
            $default_user = 'admin';
            $default_pass = password_hash('admin123', PASSWORD_DEFAULT);
            $default_role = 'admin';
            $stmt = mysqli_prepare($conn, "INSERT INTO admins (username, password, role) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'sss', $default_user, $default_pass, $default_role);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}

ensure_admin_table();

function is_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Check if current user has one of the given roles.
 * Accepts string role or array of roles.
 */
function has_role($roles) {
    if(!is_logged_in()) return false;
    $current = isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : '';
    if(is_array($roles)) {
        return in_array($current, $roles);
    }
    return $current === $roles;
}

/**
 * Require one or more roles (redirect to login if not logged in, show 403 if wrong role)
 */
function require_role($roles) {
    require_admin();
    if(!has_role($roles)) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
        echo '<h2>403 Forbidden</h2><p>You do not have permission to access this page.</p>';
        exit();
    }
}

/**
 * Create an admin user programmatically. Password will be hashed.
 */
function create_admin($username, $password, $role = 'staff', $name = 'User') {
    global $conn;
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn, "INSERT INTO admins (username, password, role, name) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'ssss', $username, $hash, $role, $name);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

function login_admin($username, $password) {
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT id, username, password, name, role FROM admins WHERE username = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $user, $hash, $name, $role);
    if(mysqli_stmt_fetch($stmt)) {
        mysqli_stmt_close($stmt);
        if(!empty($hash) && password_verify($password, $hash)) {
            // Successful
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $id;
            $_SESSION['admin_username'] = $user;
            $_SESSION['admin_name'] = $name;
            $_SESSION['admin_role'] = $role;
            return true;
        }
    } else {
        mysqli_stmt_close($stmt);
    }
    return false;
}

function require_admin() {
    if(!is_logged_in()) {
        header('Location: login.php');
        exit();
    }
}

function logout_admin() {
    // Unset session variables
    $_SESSION = array();
    if(session_id() != '' || isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-2592000, '/');
    }
    session_destroy();
}
?>