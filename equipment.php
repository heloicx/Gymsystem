<?php
include 'auth.php';
require_admin();

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_equipment'])) {
    $equipment_id = sanitize($_POST['equipment_id']);
    $name = sanitize($_POST['name']);
    $category = sanitize($_POST['category']);
    $purchase_date = sanitize($_POST['purchase_date']);
    $status = sanitize($_POST['status']);
    
    $sql = "INSERT INTO equipment (equipment_id, name, category, purchase_date, status) 
            VALUES ('$equipment_id', '$name', '$category', '$purchase_date', '$status')";
    
    if(mysqli_query($conn, $sql)) {
        $success = "Equipment added successfully!";
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
    <title>Equipment Management</title>
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
            
            <li><a href="trainers.php">
                <i class="fas fa-user-tie"></i>
                <span>Trainers</span>
            </a></li>
            
            <li><a href="equipment.php" class="active">
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
            <h1>Equipment Management</h1>
        </div>
        
        <?php
        if(isset($success)) echo "<div class='alert alert-success'>$success</div>";
        if(isset($error)) echo "<div class='alert alert-danger'>$error</div>";
        ?>
        
        <!-- Add Equipment Form -->
        <div class="content-box">
            <h2>Add New Equipment</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Equipment ID:</label>
                    <input type="text" name="equipment_id" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Equipment Name:</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Category:</label>
                    <select name="category" class="form-control" required>
                        <option value="Cardio">Cardio</option>
                        <option value="Weights">Weights</option>
                        <option value="Machines">Machines</option>
                        <option value="Accessories">Accessories</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Purchase Date:</label>
                    <input type="date" name="purchase_date" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status" class="form-control" required>
                        <option value="Working">Working</option>
                        <option value="Under Repair">Under Repair</option>
                    </select>
                </div>
                
                <button type="submit" name="add_equipment" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add Equipment
                </button>
            </form>
        </div>
        
        <!-- Equipment List -->
        <div class="content-box">
            <h2>All Equipment</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Purchase Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM equipment ORDER BY id DESC";
                    $result = mysqli_query($conn, $sql);
                    
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['equipment_id'] . "</td>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['category'] . "</td>";
                            echo "<td>" . $row['purchase_date'] . "</td>";
                            echo "<td><span class='status-" . strtolower(str_replace(' ', '-', $row['status'])) . "'>" . $row['status'] . "</span></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No equipment found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>