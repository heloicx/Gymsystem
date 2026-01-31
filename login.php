<?php
include 'auth.php';

// If already logged in, send to dashboard
if(is_logged_in()) {
    header('Location: index.php');
    exit();
}

$error = '';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if($username === '' || $password === '') {
        $error = 'Please enter username and password.';
    } else {
        if(login_admin($username, $password)) {
            header('Location: index.php');
            exit();
        } else {
            $error = 'Invalid credentials.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
    <div class="login-box">
        <h2>Admin Login</h2>
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn">Login</button>
            </div>
        </form>
        <p><small>Default admin: <strong>admin</strong> / <strong>admin123</strong>. Change password after first login.</small></p>
    </div>
</body>
</html>