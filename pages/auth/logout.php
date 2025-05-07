<?php
// Include session management
include_once '../../includes/session.php';

// Destroy the session
session_start();
session_unset();
session_destroy();

// Redirect to homepage
header("Location: /vinpearl-html/index.html");
exit();
?>