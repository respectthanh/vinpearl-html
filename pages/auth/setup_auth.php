<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once '../../includes/db_connect.php';

echo "<h1>Authentication Setup</h1>";

// Check database connection
if (!$conn || $conn->connect_error) {
    die("<p style='color:red'>Database connection failed: " . ($conn ? $conn->error : "Connection object not created") . "</p>");
}

echo "<p style='color:green'>✓ Database connection successful</p>";

// Check if users table exists
$tables_result = $conn->query("SHOW TABLES LIKE 'users'");
if ($tables_result->num_rows == 0) {
    echo "<p style='color:red'>✗ Users table doesn't exist. Creating it now...</p>";
    
    // Create the users table
    $create_table_sql = "CREATE TABLE `users` (
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
    
    if ($conn->query($create_table_sql) === TRUE) {
        echo "<p style='color:green'>✓ Users table created successfully</p>";
    } else {
        echo "<p style='color:red'>✗ Error creating users table: " . $conn->error . "</p>";
        die("Cannot continue without users table");
    }
} else {
    echo "<p style='color:green'>✓ Users table exists</p>";
}

// Create or update test users
$test_users = [
    [
        'name' => 'Admin User',
        'email' => 'admin@vinpearl.com',
        'password' => 'admin123',
        'role' => 'ADMIN'
    ],
    [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'role' => 'USER'
    ]
];

foreach ($test_users as $user) {
    // Check if user exists
    $check_stmt = $conn->prepare("SELECT id, email FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $user['email']);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        // User exists, update password
        $user_data = $result->fetch_assoc();
        echo "<p>User {$user['email']} already exists (ID: {$user_data['id']})</p>";
        
        // Update password (in case we want to ensure it's correct)
        $password_hash = password_hash($user['password'], PASSWORD_DEFAULT);
        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update_stmt->bind_param("ss", $password_hash, $user['email']);
        
        if ($update_stmt->execute()) {
            echo "<p style='color:green'>✓ Password updated for {$user['email']}</p>";
        } else {
            echo "<p style='color:red'>✗ Failed to update password: " . $update_stmt->error . "</p>";
        }
        $update_stmt->close();
    } else {
        // User doesn't exist, create it
        $password_hash = password_hash($user['password'], PASSWORD_DEFAULT);
        $insert_stmt = $conn->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        $insert_stmt->bind_param("ssss", $user['name'], $user['email'], $password_hash, $user['role']);
        
        if ($insert_stmt->execute()) {
            echo "<p style='color:green'>✓ Created new user: {$user['email']} with role {$user['role']}</p>";
        } else {
            echo "<p style='color:red'>✗ Failed to create user: " . $insert_stmt->error . "</p>";
        }
        $insert_stmt->close();
    }
    $check_stmt->close();
}

echo "<h2>User Credentials for Testing:</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Role</th><th>Email</th><th>Password</th></tr>";

foreach ($test_users as $user) {
    echo "<tr>";
    echo "<td>{$user['role']}</td>";
    echo "<td>{$user['email']}</td>";
    echo "<td>{$user['password']}</td>";
    echo "</tr>";
}

echo "</table>";

// Count total users in database
$count_result = $conn->query("SELECT COUNT(*) AS total FROM users");
$count_data = $count_result->fetch_assoc();
echo "<p>Total users in database: {$count_data['total']}</p>";

// Show the latest 5 users
echo "<h2>Latest Users in Database:</h2>";
$latest_users = $conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5");

if ($latest_users->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Created At</th></tr>";
    
    while ($row = $latest_users->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['name']}</td>";
        echo "<td>{$row['email']}</td>";
        echo "<td>{$row['role']}</td>";
        echo "<td>{$row['created_at']}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No users found in database.</p>";
}

echo "<h2>Authentication System Status</h2>";
echo "<ul>";
echo "<li><a href='auth.php'>Go to Login/Register Page</a></li>";
echo "<li><a href='debug_auth.php'>Authentication Debugger</a></li>";
echo "</ul>";

$conn->close();
?>