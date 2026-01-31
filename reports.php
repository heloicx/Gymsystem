<?php
include 'auth.php';
require_role('admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
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
            
            <li><a href="reports.php" class="active">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a></li>            <?php if(is_logged_in()): ?>
            <li><a href="logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout (<?php echo htmlspecialchars($_SESSION['admin_username']); ?> — <?php echo htmlspecialchars($_SESSION['admin_role']); ?>)</span>
            </a></li>
            <?php else: ?>
            <li><a href="login.php">
                <i class="fas fa-sign-in-alt"></i>
                <span>Login</span>
            </a></li>
            <?php endif; ?>        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-header">
            <h1>Reports & Statistics</h1>
        </div>
        
        <!-- Statistics Summary -->
        <div class="dashboard-cards">
            <?php
            // Get statistics
            $stats = [];
            
            // Members by plan
            $result = mysqli_query($conn, "SELECT plan_type, COUNT(*) as count FROM members WHERE status='Active' GROUP BY plan_type");
            $members_by_plan = [];
            while($row = mysqli_fetch_assoc($result)) {
                $members_by_plan[$row['plan_type']] = $row['count'];
            }
            
            // Total revenue this month
            $current_month = date('Y-m');
            $result = mysqli_query($conn, "SELECT SUM(amount) as total FROM payments WHERE DATE_FORMAT(payment_date, '%Y-%m') = '$current_month' AND status='Paid'");
            $row = mysqli_fetch_assoc($result);
            $monthly_revenue = $row['total'] ?? 0;
            
            // Equipment status
            $result = mysqli_query($conn, "SELECT status, COUNT(*) as count FROM equipment GROUP BY status");
            $equipment_stats = [];
            while($row = mysqli_fetch_assoc($result)) {
                $equipment_stats[$row['status']] = $row['count'];
            }
            ?>
            
            <div class="card">
                <h3>Active Members</h3>
                <div class="count">
                    <?php
                    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM members WHERE status='Active'");
                    $row = mysqli_fetch_assoc($result);
                    echo $row['count'];
                    ?>
                </div>
            </div>
            
            <div class="card">
                <h3>Monthly Revenue</h3>
                <div class="count">₱<?php echo number_format($monthly_revenue, 2); ?></div>
            </div>
            
            <div class="card">
                <h3>Working Equipment</h3>
                <div class="count"><?php echo $equipment_stats['Working'] ?? 0; ?></div>
            </div>
            
            <div class="card">
                <h3>Active Trainers</h3>
                <div class="count">
                    <?php
                    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM trainers WHERE status='Active'");
                    $row = mysqli_fetch_assoc($result);
                    echo $row['count'];
                    ?>
                </div>
            </div>
        </div>
        
        <!-- Detailed Reports -->
        <div class="content-box">
            <h2>Detailed Reports</h2>
            
            <!-- Member Distribution -->
            <h3>Member Distribution by Plan</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Plan Type</th>
                        <th>Number of Members</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_members = array_sum($members_by_plan);
                    foreach($members_by_plan as $plan => $count) {
                        $percentage = $total_members > 0 ? round(($count / $total_members) * 100, 1) : 0;
                        echo "<tr>";
                        echo "<td>" . $plan . "</td>";
                        echo "<td>" . $count . "</td>";
                        echo "<td>" . $percentage . "%</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            
            <!-- Monthly Revenue -->
            <h3>Monthly Revenue (Last 6 Months)</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Total Revenue</th>
                        <th>Number of Payments</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT 
                            DATE_FORMAT(payment_date, '%Y-%m') as month,
                            SUM(amount) as total,
                            COUNT(*) as count
                            FROM payments 
                            WHERE status='Paid'
                            GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
                            ORDER BY month DESC
                            LIMIT 6";
                    $result = mysqli_query($conn, $sql);
                    
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['month'] . "</td>";
                            echo "<td>₱" . number_format($row['total'], 2) . "</td>";
                            echo "<td>" . $row['count'] . "</td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
            
            <!-- Attendance Report -->
            <h3>Attendance Report (Last 7 Days)</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Total Check-ins</th>
                        <th>Unique Members</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT 
                            DATE(check_in) as date,
                            COUNT(*) as total_checkins,
                            COUNT(DISTINCT member_id) as unique_members
                            FROM attendance 
                            WHERE check_in >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                            GROUP BY DATE(check_in)
                            ORDER BY date DESC";
                    $result = mysqli_query($conn, $sql);
                    
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['date'] . "</td>";
                            echo "<td>" . $row['total_checkins'] . "</td>";
                            echo "<td>" . $row['unique_members'] . "</td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
            
            <!-- Equipment Status -->
            <h3>Equipment Status Report</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Count</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_equipment = array_sum($equipment_stats);
                    foreach($equipment_stats as $status => $count) {
                        $percentage = $total_equipment > 0 ? round(($count / $total_equipment) * 100, 1) : 0;
                        echo "<tr>";
                        echo "<td>" . $status . "</td>";
                        echo "<td>" . $count . "</td>";
                        echo "<td>" . $percentage . "%</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <!-- Export Options -->
        <div class="content-box">
            <h2>Export Reports</h2>
            <div style="display: flex; gap: 15px;">
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print"></i> Print Report
                </button>
                <button class="btn btn-success" onclick="alert('Export feature would be implemented here')">
                    <i class="fas fa-file-csv"></i> Export as CSV
                </button>
                <button class="btn btn-success" onclick="alert('Export feature would be implemented here')">
                    <i class="fas fa-file-pdf"></i> Export as PDF
                </button>
            </div>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>