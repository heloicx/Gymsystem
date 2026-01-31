<?php
include 'auth.php';
require_admin();

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['add_member'])) {
        $member_id = sanitize($_POST['member_id']);
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $join_date = sanitize($_POST['join_date']);
        $plan_type = sanitize($_POST['plan_type']);
        $status = sanitize($_POST['status']);
        
        $sql = "INSERT INTO members (member_id, name, email, phone, join_date, plan_type, status) 
                VALUES ('$member_id', '$name', '$email', '$phone', '$join_date', '$plan_type', '$status')";
        
        if(mysqli_query($conn, $sql)) {
            $success = "Member added successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}

// Handle delete
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM members WHERE id = $id");
    header("Location: members.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Management</title>
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
            
            <li><a href="members.php" class="active">
                <i class="fas fa-users"></i>
                <span>Members</span>
            </a></li>
            
            <li><a href="trainers.php">
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
            <h1>Member Management</h1>
        </div>
        
        <?php
        if(isset($success)) echo "<div class='alert alert-success'>$success</div>";
        if(isset($error)) echo "<div class='alert alert-danger'>$error</div>";
        ?>
        
        <!-- Add Member Form -->
        <div class="content-box">
            <h2>Add New Member</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Member ID:</label>
                    <input type="text" name="member_id" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Full Name:</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Phone:</label>
                    <input type="text" name="phone" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Join Date:</label>
                    <input type="date" name="join_date" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Plan Type:</label>
                    <select name="plan_type" class="form-control" required>
                        <option value="Basic">Basic</option>
                        <option value="Premium">Premium</option>
                        <option value="VIP">VIP</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status" class="form-control" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                
                <button type="submit" name="add_member" class="btn btn-success">
                    <i class="fas fa-user-plus"></i> Add Member
                </button>
            </form>
        </div>
        
        <!-- Members List -->
        <div class="content-box">
            <h2>All Members</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Join Date</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM members ORDER BY id DESC";
                    $result = mysqli_query($conn, $sql);
                    
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['member_id'] . "</td>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['email'] . "</td>";
                            echo "<td>" . $row['phone'] . "</td>";
                            echo "<td>" . $row['join_date'] . "</td>";
                            echo "<td>" . $row['plan_type'] . "</td>";
                            echo "<td><span class='status-" . strtolower($row['status']) . "'>" . $row['status'] . "</span></td>";
                            echo "<td>";
                            echo "<a href='?delete=" . $row['id'] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure?\")'>
                                    <i class='fas fa-trash'></i> Delete
                                  </a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No members found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>