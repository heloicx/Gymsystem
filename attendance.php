<?php
include 'auth.php';
require_admin();

// Handle check-in
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['check_in'])) {
    $member_id = intval($_POST['member_id']);
    $check_in = date('Y-m-d H:i:s');
    
    $sql = "INSERT INTO attendance (member_id, check_in) VALUES ($member_id, '$check_in')";
    
    if(mysqli_query($conn, $sql)) {
        $success = "Member checked in successfully!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Handle check-out
if(isset($_GET['checkout'])) {
    $id = intval($_GET['checkout']);
    $check_out = date('Y-m-d H:i:s');
    
    $sql = "UPDATE attendance SET check_out = '$check_out' WHERE id = $id";
    mysqli_query($conn, $sql);
    header("Location: attendance.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management</title>
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
            
            <li><a href="equipment.php">
                <i class="fas fa-dumbbell"></i>
                <span>Equipment</span>
            </a></li>
            
            <li><a href="attendance.php" class="active">
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
            <h1>Attendance Management</h1>
        </div>
        
        <?php
        if(isset($success)) echo "<div class='alert alert-success'>$success</div>";
        if(isset($error)) echo "<div class='alert alert-danger'>$error</div>";
        ?>
        
        <!-- Check-in Form -->
        <div class="content-box">
            <h2>Check-in Member</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Select Member:</label>
                    <select name="member_id" class="form-control" required>
                        <option value="">Select Member</option>
                        <?php
                        $sql = "SELECT * FROM members WHERE status = 'Active' ORDER BY name";
                        $result = mysqli_query($conn, $sql);
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='" . $row['id'] . "'>" . $row['name'] . " (" . $row['member_id'] . ")</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" name="check_in" class="btn btn-success">
                    <i class="fas fa-sign-in-alt"></i> Check In
                </button>
            </form>
        </div>
        
        <!-- Active Sessions -->
        <div class="content-box">
            <h2>Active Sessions (Checked in but not out)</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Check-in Time</th>
                        <th>Duration</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT a.*, m.name, m.member_id 
                            FROM attendance a 
                            JOIN members m ON a.member_id = m.id 
                            WHERE a.check_out IS NULL 
                            ORDER BY a.check_in DESC";
                    $result = mysqli_query($conn, $sql);
                    
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            $duration = time() - strtotime($row['check_in']);
                            $hours = floor($duration / 3600);
                            $minutes = floor(($duration % 3600) / 60);
                            
                            echo "<tr>";
                            echo "<td>" . $row['name'] . " (" . $row['member_id'] . ")</td>";
                            echo "<td>" . date('h:i A', strtotime($row['check_in'])) . "</td>";
                            echo "<td>" . $hours . "h " . $minutes . "m</td>";
                            echo "<td>";
                            echo "<a href='?checkout=" . $row['id'] . "' class='btn btn-primary'>
                                    <i class='fas fa-sign-out-alt'></i> Check Out
                                  </a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No active sessions</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <!-- Today's Attendance -->
        <div class="content-box">
            <h2>Today's Attendance</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $today = date('Y-m-d');
                    $sql = "SELECT a.*, m.name 
                            FROM attendance a 
                            JOIN members m ON a.member_id = m.id 
                            WHERE DATE(a.check_in) = '$today' AND a.check_out IS NOT NULL
                            ORDER BY a.check_in DESC";
                    $result = mysqli_query($conn, $sql);
                    
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            $check_in = date('h:i A', strtotime($row['check_in']));
                            $check_out = date('h:i A', strtotime($row['check_out']));
                            $duration = strtotime($row['check_out']) - strtotime($row['check_in']);
                            $hours = floor($duration / 3600);
                            $minutes = floor(($duration % 3600) / 60);
                            
                            echo "<tr>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $check_in . "</td>";
                            echo "<td>" . $check_out . "</td>";
                            echo "<td>" . $hours . "h " . $minutes . "m</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No attendance records for today</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>