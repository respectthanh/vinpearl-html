<?php
// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the session management
require_once '../../includes/session.php';
require_once '../../includes/db_connect.php';

echo '<h1>Authentication Debug</h1>';

echo '<h2>Session Info</h2>';
echo '<pre>';
print_r($_SESSION);
echo '</pre>';

echo '<h2>Login Status</h2>';
echo 'Is Logged In: ' . (isLoggedIn() ? 'Yes' : 'No') . '<br>';
if (isLoggedIn()) {
    echo 'User: ' . $_SESSION['user_name'] . ' (ID: ' . $_SESSION['user_id'] . ')<br>';
    echo 'Role: ' . $_SESSION['user_role'] . '<br>';
}

echo '<h2>Database Connection</h2>';
if (isset($conn) && $conn instanceof mysqli && !$conn->connect_error) {
    echo 'Status: Connected<br>';
    echo 'Server Info: ' . $conn->server_info . '<br>';
    
    // Check users table
    echo '<h2>Users Table</h2>';
    $query = "SELECT id, name, email, role, created_at FROM users LIMIT 5";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        echo '<table border="1" cellpadding="5">';
        echo '<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Created At</th></tr>';
        
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['id'] . '</td>';
            echo '<td>' . htmlspecialchars($row['name']) . '</td>';
            echo '<td>' . htmlspecialchars($row['email']) . '</td>';
            echo '<td>' . htmlspecialchars($row['role']) . '</td>';
            echo '<td>' . $row['created_at'] . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
    } else {
        echo 'No users found or error in query.<br>';
        if ($conn->error) {
            echo 'Error: ' . $conn->error . '<br>';
        }
    }
} else {
    echo 'Status: Not Connected<br>';
    if (isset($conn->connect_error)) {
        echo 'Error: ' . $conn->connect_error . '<br>';
    }
}

echo '<p><a href="login.php">Go to Login Page</a> | <a href="../../index.php">Go to Homepage</a></p>';

if (isLoggedIn()) {
    echo '<p><a href="logout.php">Logout</a></p>';
}
?> 