<?php
include 'auth.php';
require_admin();

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_payment'])) {
    $member_id = intval($_POST['member_id']);
    $amount = floatval($_POST['amount']);
    $payment_date = sanitize($_POST['payment_date']);
    $payment_type = sanitize($_POST['payment_type']);
    $status = sanitize($_POST['status']);
    
    $sql = "INSERT INTO payments (member_id, amount, payment_date, payment_type, status) 
            VALUES ($member_id, $amount, '$payment_date', '$payment_type', '$status')";
    
    if(mysqli_query($conn, $sql)) {
        $success = "Payment recorded successfully!";
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
    <title>Payment Management</title>
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
            
            <li><a href="payments.php" class="active">
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
            <h1>Payment Management</h1>
        </div>
        
        <?php
        if(isset($success)) echo "<div class='alert alert-success'>$success</div>";
        if(isset($error)) echo "<div class='alert alert-danger'>$error</div>";
        ?>
        
        <!-- Add Payment Form -->
        <div class="content-box">
            <h2>Record Payment</h2>
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
                
                <div class="form-group">
                    <label>Amount:</label>
                    <input type="number" name="amount" class="form-control" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label>Payment Date:</label>
                    <input type="date" name="payment_date" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Payment Type:</label>
                    <select name="payment_type" class="form-control" required>
                        <option value="Cash">Cash</option>
                        <option value="Card">Card</option>
                        <option value="Online">Online</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status" class="form-control" required>
                        <option value="Paid">Paid</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>
                
                <button type="submit" name="add_payment" class="btn btn-success">
                    <i class="fas fa-money-bill-wave"></i> Record Payment
                </button>
            </form>
        </div>
        
        <!-- Payments List -->
        <div class="content-box">
            <h2>All Payments</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT p.*, m.name 
                            FROM payments p 
                            JOIN members m ON p.member_id = m.id 
                            ORDER BY p.payment_date DESC";
                    $result = mysqli_query($conn, $sql);
                    
                    $total = 0;
                    
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            $total += $row['amount'];
                            echo "<tr>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>₱" . number_format($row['amount'], 2) . "</td>";
                            echo "<td>" . $row['payment_date'] . "</td>";
                            echo "<td>" . $row['payment_type'] . "</td>";
                            echo "<td>" . $row['status'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No payments found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            
            <div style="margin-top: 20px; padding: 10px; background-color: #f8f9fa; border-radius: 3px;">
                <strong>Total Payments: ₱<?php echo number_format($total, 2); ?></strong>
            </div>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>