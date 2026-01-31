<?php
include 'auth.php';
require_role('admin');

// Handle create user
$success = '';
$error = '';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    $role = sanitize($_POST['role']);
    $name = sanitize($_POST['name']);

    if($username === '' || $password === '' || $role === '') {
        $error = 'Please fill all required fields.';
    } else {
        if(create_admin($username, $password, $role, $name)) {
            $success = 'User created successfully.';
        } else {
            $error = 'Error creating user (username may already exist).';
        }
    }
}

// Handle delete
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Prevent deleting self
    if($id == $_SESSION['admin_id']) {
        $error = 'You cannot delete your own account.';
    } else {
        mysqli_query($conn, "DELETE FROM admins WHERE id = $id");
        header('Location: admin_users.php');
        exit();
    }
}

// Fetch users
$result = mysqli_query($conn, "SELECT id, username, name, role, created_at FROM admins ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Users</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-dumbbell"></i>
            <span>Gym System</span>
        </div>
        
        <ul class="sidebar-nav">
            <li><a href="index.php">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a></li>
            
            <li><a href="members.php">
                <i class="fas fa-users"></i>
                <span>Members</span>
            </a></li>
            
            <li><a href="admin_users.php" class="active">
                <i class="fas fa-user-shield"></i>
                <span>Admin Users</span>
            </a></li>

            <li><a href="reports.php">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a></li>
            <?php if(is_logged_in()): ?>
            <li><a href="logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout (<?php echo htmlspecialchars($_SESSION['admin_username']); ?> — <?php echo htmlspecialchars($_SESSION['admin_role']); ?>)</span>
            </a></li>
            <?php else: ?>
            <li><a href="login.php">
                <i class="fas fa-sign-in-alt"></i>
                <span>Login</span>
            </a></li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="main-content">
        <div class="content-header">
            <h1>Admin Users</h1>
        </div>

        <?php
        if($success) echo "<div class='alert alert-success'>{$success}</div>";
        if($error) echo "<div class='alert alert-danger'>{$error}</div>";
        ?>

        <div class="content-box">
            <h2>Create New Admin User</h2>
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
                    <label>Full name</label>
                    <input type="text" name="name" class="form-control">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" class="form-control">
                        <option value="admin">Admin</option>
                        <option value="manager">Manager</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
                <div class="form-group">
                    <button class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>

        <div class="content-box">
            <h2>Existing Admins</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['role']); ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td>
                            <?php if($row['id'] != $_SESSION['admin_id']): ?>
                            <a href="admin_users.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete user?')">Delete</a>
                            <?php else: ?>
                            <em>Current</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>