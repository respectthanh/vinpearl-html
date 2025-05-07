<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters (without database name)
$host = "localhost";
$username = "root";
$password = "";
$database = "vinpearl_db";

echo "<h1>Vinpearl Database Setup</h1>";

// Step 1: Connect to MySQL without selecting a database
$conn = new mysqli($host, $username, $password);
if ($conn->connect_error) {
    die("<p style='color:red'>Connection failed: " . $conn->connect_error . "</p>");
}
echo "<p style='color:green'>Connected to MySQL server successfully.</p>";

// Step 2: Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql) === TRUE) {
    echo "<p style='color:green'>Database '$database' created or already exists.</p>";
} else {
    die("<p style='color:red'>Error creating database: " . $conn->error . "</p>");
}

// Step 3: Select the database
$conn->select_db($database);
echo "<p>Using database: $database</p>";

// Step 4: Create users table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,
    `role` enum('USER','ADMIN') NOT NULL DEFAULT 'USER',
    `created_at` datetime NOT NULL,
    `updated_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql) === TRUE) {
    echo "<p style='color:green'>Table 'users' created or already exists.</p>";
} else {
    die("<p style='color:red'>Error creating table: " . $conn->error . "</p>");
}

// Step 5: Check if admin user exists, create if not
$stmt = $conn->prepare("SELECT id FROM users WHERE email = 'admin@vinpearl.com'");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Create admin user
    $name = "Admin User";
    $email = "admin@vinpearl.com";
    $password = password_hash("admin123", PASSWORD_DEFAULT);
    $role = "ADMIN";
    
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $name, $email, $password, $role);
    
    if ($stmt->execute()) {
        echo "<p style='color:green'>Admin user created successfully.</p>";
        echo "<p>Email: admin@vinpearl.com<br>Password: admin123</p>";
    } else {
        echo "<p style='color:red'>Error creating admin user: " . $stmt->error . "</p>";
    }
} else {
    echo "<p>Admin user already exists.</p>";
}

// Step 6: Create a test user if no regular users exist
$stmt = $conn->prepare("SELECT id FROM users WHERE role = 'USER' LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Create test user
    $name = "Test User";
    $email = "test@example.com";
    $password = password_hash("password123", PASSWORD_DEFAULT);
    $role = "USER";
    
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $name, $email, $password, $role);
    
    if ($stmt->execute()) {
        echo "<p style='color:green'>Test user created successfully.</p>";
        echo "<p>Email: test@example.com<br>Password: password123</p>";
    } else {
        echo "<p style='color:red'>Error creating test user: " . $stmt->error . "</p>";
    }
} else {
    echo "<p>Regular users already exist.</p>";
}

// Show all users
$result = $conn->query("SELECT id, name, email, role, created_at FROM users");
if ($result->num_rows > 0) {
    echo "<h2>Current Users:</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Created At</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['role'] . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No users found in the database.</p>";
}

// Close the connection
$conn->close();

echo "<p>Database setup complete. <a href='/pages/auth/index.html'>Go to login page</a></p>";
?> 