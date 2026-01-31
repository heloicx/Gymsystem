<?php
include 'auth.php';
require_admin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Management System</title>
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
            <li><a href="index.php" class="active">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a></li>-
            
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
            <h1>Dashboard</h1>
        </div>
        
        <!-- Statistics Cards -->
        <div class="dashboard-cards">
            <?php
            // Get counts
            $counts = [];
            
            // Total Members
            $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM members");
            $row = mysqli_fetch_assoc($result);
            $counts['members'] = $row['count'];
            
            // Total Trainers
            $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM trainers");
            $row = mysqli_fetch_assoc($result);
            $counts['trainers'] = $row['count'];
            
            // Total Equipment
            $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM equipment");
            $row = mysqli_fetch_assoc($result);
            $counts['equipment'] = $row['count'];
            
            // Today's Attendance
            $today = date('Y-m-d');
            $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM attendance WHERE DATE(check_in) = '$today'");
            $row = mysqli_fetch_assoc($result);
            $counts['attendance'] = $row['count'];
            ?>
            
            <div class="card">
                <h3>Total Members</h3>
                <div class="count"><?php echo $counts['members']; ?></div>
            </div>
            
            <div class="card">
                <h3>Total Trainers</h3>
                <div class="count"><?php echo $counts['trainers']; ?></div>
            </div>
            
            <div class="card">
                <h3>Total Equipment</h3>
                <div class="count"><?php echo $counts['equipment']; ?></div>
            </div>
            
            <div class="card">
                <h3>Today's Attendance</h3>
                <div class="count"><?php echo $counts['attendance']; ?></div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="content-box">
            <h2>Recent Activities</h2>
            
            <!-- Recent Members -->
            <h3>Recent Members</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Member ID</th>
                        <th>Name</th>
                        <th>Join Date</th>
                        <th>Plan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM members ORDER BY id DESC LIMIT 5";
                    $result = mysqli_query($conn, $sql);
                    
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['member_id'] . "</td>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['join_date'] . "</td>";
                            echo "<td>" . $row['plan_type'] . "</td>";
                            echo "<td><span class='status-" . strtolower($row['status']) . "'>" . $row['status'] . "</span></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No members found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Today's Attendance -->
            <h3>Today's Attendance</h3>
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
                    $sql = "SELECT a.*, m.name 
                            FROM attendance a 
                            JOIN members m ON a.member_id = m.id 
                            WHERE DATE(a.check_in) = '$today' 
                            ORDER BY a.check_in DESC";
                    $result = mysqli_query($conn, $sql);
                    
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            $check_in = date('h:i A', strtotime($row['check_in']));
                            $check_out = $row['check_out'] ? date('h:i A', strtotime($row['check_out'])) : '-';
                            
                            if($row['check_out']) {
                                $diff = strtotime($row['check_out']) - strtotime($row['check_in']);
                                $hours = floor($diff / 3600);
                                $minutes = floor(($diff % 3600) / 60);
                                $duration = $hours . "h " . $minutes . "m";
                            } else {
                                $duration = '-';
                            }
                            
                            echo "<tr>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $check_in . "</td>";
                            echo "<td>" . $check_out . "</td>";
                            echo "<td>" . $duration . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No attendance today</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>