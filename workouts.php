<?php
include 'auth.php';
require_admin();

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['add_plan'])) {
        $plan_name = sanitize($_POST['plan_name']);
        $description = sanitize($_POST['description']);
        $trainer_id = intval($_POST['trainer_id']);
        
        $sql = "INSERT INTO workout_plans (plan_name, description, trainer_id) 
                VALUES ('$plan_name', '$description', $trainer_id)";
        
        if(mysqli_query($conn, $sql)) {
            $success = "Workout plan added successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
    
    if(isset($_POST['assign_workout'])) {
        $member_id = intval($_POST['member_id']);
        $plan_id = intval($_POST['plan_id']);
        $assigned_date = sanitize($_POST['assigned_date']);
        
        $sql = "INSERT INTO member_workouts (member_id, plan_id, assigned_date) 
                VALUES ($member_id, $plan_id, '$assigned_date')";
        
        if(mysqli_query($conn, $sql)) {
            $success = "Workout assigned successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workout Management</title>
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
            
            <li><a href="workouts.php" class="active">
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
            <h1>Workout Management</h1>
        </div>
        
        <?php
        if(isset($success)) echo "<div class='alert alert-success'>$success</div>";
        if(isset($error)) echo "<div class='alert alert-danger'>$error</div>";
        ?>
        
        <!-- Add Workout Plan -->
        <div class="content-box">
            <h2>Add Workout Plan</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Plan Name:</label>
                    <input type="text" name="plan_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Assigned Trainer:</label>
                    <select name="trainer_id" class="form-control" required>
                        <option value="">Select Trainer</option>
                        <?php
                        $sql = "SELECT * FROM trainers WHERE status = 'Active' ORDER BY name";
                        $result = mysqli_query($conn, $sql);
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='" . $row['id'] . "'>" . $row['name'] . " - " . $row['specialization'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <button type="submit" name="add_plan" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add Plan
                </button>
            </form>
        </div>
        
        <!-- Assign Workout to Member -->
        <div class="content-box">
            <h2>Assign Workout to Member</h2>
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
                    <label>Select Workout Plan:</label>
                    <select name="plan_id" class="form-control" required>
                        <option value="">Select Plan</option>
                        <?php
                        $sql = "SELECT wp.*, t.name as trainer_name 
                                FROM workout_plans wp 
                                LEFT JOIN trainers t ON wp.trainer_id = t.id 
                                ORDER BY wp.plan_name";
                        $result = mysqli_query($conn, $sql);
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='" . $row['id'] . "'>" . $row['plan_name'] . " (Trainer: " . $row['trainer_name'] . ")</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Assign Date:</label>
                    <input type="date" name="assigned_date" class="form-control" required>
                </div>
                
                <button type="submit" name="assign_workout" class="btn btn-success">
                    <i class="fas fa-user-check"></i> Assign Workout
                </button>
            </form>
        </div>
        
        <!-- Workout Plans List -->
        <div class="content-box">
            <h2>All Workout Plans</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Plan Name</th>
                        <th>Description</th>
                        <th>Trainer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT wp.*, t.name as trainer_name 
                            FROM workout_plans wp 
                            LEFT JOIN trainers t ON wp.trainer_id = t.id 
                            ORDER BY wp.id DESC";
                    $result = mysqli_query($conn, $sql);
                    
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['plan_name'] . "</td>";
                            echo "<td>" . $row['description'] . "</td>";
                            echo "<td>" . $row['trainer_name'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No workout plans found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <!-- Member Workouts -->
        <div class="content-box">
            <h2>Member Workouts</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Workout Plan</th>
                        <th>Assigned Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT mw.*, m.name as member_name, wp.plan_name 
                            FROM member_workouts mw 
                            JOIN members m ON mw.member_id = m.id 
                            JOIN workout_plans wp ON mw.plan_id = wp.id 
                            ORDER BY mw.assigned_date DESC";
                    $result = mysqli_query($conn, $sql);
                    
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['member_name'] . "</td>";
                            echo "<td>" . $row['plan_name'] . "</td>";
                            echo "<td>" . $row['assigned_date'] . "</td>";
                            echo "<td>" . $row['status'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No member workouts found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>