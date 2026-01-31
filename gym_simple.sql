-- ============================================
-- SIMPLE GYM SYSTEM - 8 TABLES ONLY
-- ============================================

CREATE DATABASE IF NOT EXISTS gym_simple;
USE gym_simple;

-- 1. MEMBERS TABLE
CREATE TABLE members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    member_id VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(15),
    join_date DATE NOT NULL,
    plan_type ENUM('Basic', 'Premium', 'VIP') DEFAULT 'Basic',
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. TRAINERS TABLE
CREATE TABLE trainers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    trainer_id VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100),
    phone VARCHAR(15),
    salary DECIMAL(10,2),
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. EQUIPMENT TABLE
CREATE TABLE equipment (
    id INT PRIMARY KEY AUTO_INCREMENT,
    equipment_id VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    purchase_date DATE,
    status ENUM('Working', 'Under Repair') DEFAULT 'Working',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. WORKOUT_PLANS TABLE
CREATE TABLE workout_plans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    plan_name VARCHAR(100) NOT NULL,
    description TEXT,
    trainer_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trainer_id) REFERENCES trainers(id)
);

-- 5. MEMBER_WORKOUTS TABLE
CREATE TABLE member_workouts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT,
    plan_id INT,
    assigned_date DATE,
    status ENUM('Active', 'Completed') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (plan_id) REFERENCES workout_plans(id)
);

-- 6. ATTENDANCE TABLE
CREATE TABLE attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT,
    check_in DATETIME NOT NULL,
    check_out DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id)
);

-- 7. PAYMENTS TABLE
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_type ENUM('Cash', 'Card', 'Online') DEFAULT 'Cash',
    status ENUM('Paid', 'Pending') DEFAULT 'Paid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id)
);

-- 8. EQUIPMENT_USAGE TABLE
CREATE TABLE equipment_usage (
    id INT PRIMARY KEY AUTO_INCREMENT,
    equipment_id INT,
    member_id INT,
    usage_date DATE,
    duration_minutes INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id),
    FOREIGN KEY (member_id) REFERENCES members(id)
);

-- ============================================
-- INSERT SAMPLE DATA
-- ============================================

-- Insert Members
INSERT INTO members (member_id, name, email, phone, join_date, plan_type) VALUES
('MEM001', 'John Smith', 'john@email.com', '09123456789', '2024-01-15', 'Premium'),
('MEM002', 'Maria Garcia', 'maria@email.com', '09234567890', '2024-02-20', 'Basic'),
('MEM003', 'David Lee', 'david@email.com', '09345678901', '2024-03-10', 'VIP'),
('MEM004', 'Sarah Johnson', 'sarah@email.com', '09456789012', '2024-01-25', 'Premium'),
('MEM005', 'Michael Brown', 'mike@email.com', '09567890123', '2024-02-05', 'Basic');

-- Insert Trainers
INSERT INTO trainers (trainer_id, name, specialization, phone, salary) VALUES
('TRN001', 'Alex Chen', 'Weight Training', '09111222333', 30000),
('TRN002', 'Lisa Park', 'Yoga & Cardio', '09222333444', 28000),
('TRN003', 'Carlos Reyes', 'CrossFit', '09333444555', 32000);

-- Insert Equipment
INSERT INTO equipment (equipment_id, name, category, purchase_date) VALUES
('EQ001', 'Treadmill', 'Cardio', '2023-01-10'),
('EQ002', 'Dumbbell Set', 'Weights', '2023-02-15'),
('EQ003', 'Bench Press', 'Weights', '2023-03-20'),
('EQ004', 'Exercise Bike', 'Cardio', '2023-04-25'),
('EQ005', 'Leg Press Machine', 'Machines', '2023-05-30');

-- Insert Workout Plans
INSERT INTO workout_plans (plan_name, description, trainer_id) VALUES
('Weight Loss Program', 'Cardio focused plan for weight loss', 1),
('Muscle Building', 'Strength training for muscle growth', 2),
('Beginner Fitness', 'Basic workout for beginners', 3);

-- Insert Member Workouts
INSERT INTO member_workouts (member_id, plan_id, assigned_date) VALUES
(1, 1, '2024-03-01'),
(2, 2, '2024-03-05'),
(3, 3, '2024-03-10'),
(4, 1, '2024-03-15'),
(5, 2, '2024-03-20');

-- Insert Attendance
INSERT INTO attendance (member_id, check_in, check_out) VALUES
(1, '2024-03-20 09:00:00', '2024-03-20 11:00:00'),
(2, '2024-03-20 10:00:00', '2024-03-20 12:00:00'),
(1, '2024-03-21 08:30:00', '2024-03-21 10:30:00'),
(3, '2024-03-21 15:00:00', '2024-03-21 17:00:00'),
(4, '2024-03-22 07:00:00', '2024-03-22 09:00:00');

-- Insert Payments
INSERT INTO payments (member_id, amount, payment_date, payment_type) VALUES
(1, 1500.00, '2024-03-01', 'Online'),
(2, 1000.00, '2024-03-05', 'Cash'),
(3, 2000.00, '2024-03-10', 'Card'),
(4, 1500.00, '2024-03-15', 'Online'),
(5, 1000.00, '2024-03-20', 'Cash');

-- Insert Equipment Usage
INSERT INTO equipment_usage (equipment_id, member_id, usage_date, duration_minutes) VALUES
(1, 1, '2024-03-20', 30),
(2, 1, '2024-03-20', 20),
(3, 2, '2024-03-20', 45),
(1, 3, '2024-03-21', 40),
(4, 4, '2024-03-22', 35);

-- ============================================
-- CREATE DATABASE USER
-- ============================================
CREATE USER IF NOT EXISTS 'gym_user'@'localhost' IDENTIFIED BY 'gym123';
GRANT ALL PRIVILEGES ON gym_simple.* TO 'gym_user'@'localhost';
FLUSH PRIVILEGES;