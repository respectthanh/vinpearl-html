<?php
// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include_once '../../includes/db_connect.php';

echo "<h1>Database Connection Test</h1>";

// Check if connection is working
if ($conn && $conn->ping()) {
    echo "<p style='color:green'>✓ Database connection successful</p>";
} else {
    echo "<p style='color:red'>✗ Database connection failed: " . $conn->error . "</p>";
    die("Cannot continue testing without database connection");
}

// Check if database exists
$result = $conn->query("SELECT DATABASE()");
$row = $result->fetch_row();
echo "<p>Current database: <strong>" . $row[0] . "</strong></p>";

// Check if users table exists
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows > 0) {
    echo "<p style='color:green'>✓ Users table exists</p>";
    
    // Check structure of users table
    echo "<h2>Users Table Structure:</h2>";
    $result = $conn->query("DESCRIBE users");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check if any users exist
    $result = $conn->query("SELECT id, name, email, role, created_at FROM users LIMIT 5");
    echo "<h2>Sample Users (max 5):</h2>";
    if ($result->num_rows > 0) {
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
        echo "<p style='color:orange'>No users found in the database</p>";
    }
} else {
    echo "<p style='color:red'>✗ Users table does not exist!</p>";
    
    // Generate SQL to create users table
    echo "<h2>SQL to create users table:</h2>";
    echo "<pre>
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('USER','ADMIN') NOT NULL DEFAULT 'USER',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
</pre>";
}

$conn->close();
?> 