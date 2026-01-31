<?php
include 'auth.php';
require_admin();

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_trainer'])) {
    $trainer_id = sanitize($_POST['trainer_id']);
    $name = sanitize($_POST['name']);
    $specialization = sanitize($_POST['specialization']);
    $phone = sanitize($_POST['phone']);
    $salary = sanitize($_POST['salary']);
    $status = sanitize($_POST['status']);
    
    $sql = "INSERT INTO trainers (trainer_id, name, specialization, phone, salary, status) 
            VALUES ('$trainer_id', '$name', '$specialization', '$phone', '$salary', '$status')";
    
    if(mysqli_query($conn, $sql)) {
        $success = "Trainer added successfully!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainer Management</title>
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
            
            <li><a href="trainers.php" class="active">
                <i class="fas fa-user-tie"></i>
                <span>Trainers</span>
            </a></li>
            
            <li><a href="equipment.php">
                <i class="fas fa-dumbbell"></i>
                <span>Equipment</span>
            </a></li>
            
            <li><a href="attendance.php">
                <i class="fas fa-clipboard-check"></i>
                <span>Attendance</span>
            </a></li>
            
            <li><a href="payments.php">
                <i class="fas fa-credit-card"></i>
                <span>Payments</span>
            </a></li>
            
            <li><a href="workouts.php">
                <i class="fas fa-running"></i>
                <span>Workouts</span>
            </a></li>
            
            <?php if(is_logged_in() && has_role('admin')): ?>
            <li><a href="admin_users.php">
                <i class="fas fa-user-shield"></i>
                <span>Admin Users</span>
            </a></li>
            <?php endif; ?>
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

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-header">
            <h1>Trainer Management</h1>
        </div>
        
        <?php
        if(isset($success)) echo "<div class='alert alert-success'>$success</div>";
        if(isset($error)) echo "<div class='alert alert-danger'>$error</div>";
        ?>
        
        <!-- Add Trainer Form -->
        <div class="content-box">
            <h2>Add New Trainer</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Trainer ID:</label>
                    <input type="text" name="trainer_id" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Full Name:</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Specialization:</label>
                    <input type="text" name="specialization" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Phone:</label>
                    <input type="text" name="phone" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Monthly Salary:</label>
                    <input type="number" name="salary" class="form-control" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status" class="form-control" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                
                <button type="submit" name="add_trainer" class="btn btn-success">
                    <i class="fas fa-user-plus"></i> Add Trainer
                </button>
            </form>
        </div>
        
        <!-- Trainers List -->
        <div class="content-box">
            <h2>All Trainers</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Specialization</th>
                        <th>Phone</th>
                        <th>Salary</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM trainers ORDER BY id DESC";
                    $result = mysqli_query($conn, $sql);
                    
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['trainer_id'] . "</td>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['specialization'] . "</td>";
                            echo "<td>" . $row['phone'] . "</td>";
                            echo "<td>₱" . number_format($row['salary'], 2) . "</td>";
                            echo "<td><span class='status-" . strtolower($row['status']) . "'>" . $row['status'] . "</span></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No trainers found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>