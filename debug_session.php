<!DOCTYPE html>
<html>
<head>
    <title>Session Debug</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .info { background: #e3f2fd; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .warning { background: #fff3cd; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; padding: 15px; margin: 10px 0; border-radius: 5px; }
        pre { background: #f5f5f5; padding: 10px; overflow: auto; }
    </style>
</head>
<body>
    <h1>TDW Session Debug</h1>
    
    <?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    echo '<div class="info">';
    echo '<h3>Session Status</h3>';
    echo '<p>Session ID: ' . session_id() . '</p>';
    echo '<p>Session Status: ' . (session_status() == PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . '</p>';
    echo '</div>';
    
    echo '<div class="info">';
    echo '<h3>Session Data</h3>';
    if (!empty($_SESSION)) {
        echo '<pre>' . print_r($_SESSION, true) . '</pre>';
    } else {
        echo '<p class="warning">No session data</p>';
    }
    echo '</div>';
    
    echo '<div class="info">';
    echo '<h3>Current User</h3>';
    if (isset($_SESSION['admin'])) {
        echo '<p class="warning"><strong>Logged in as ADMIN:</strong></p>';
        echo '<pre>' . print_r($_SESSION['admin'], true) . '</pre>';
    } elseif (isset($_SESSION['user'])) {
        echo '<p class="success"><strong>Logged in as USER:</strong></p>';
        echo '<pre>' . print_r($_SESSION['user'], true) . '</pre>';
    } else {
        echo '<p>Not logged in</p>';
    }
    echo '</div>';
    
    echo '<div class="info">';
    echo '<h3>Actions</h3>';
    echo '<a href="index.php?router=logout" style="display:inline-block; padding:10px 20px; background:#dc3545; color:white; text-decoration:none; border-radius:5px; margin-right:10px;">Force Logout</a>';
    echo '<a href="index.php?router=login" style="display:inline-block; padding:10px 20px; background:#007bff; color:white; text-decoration:none; border-radius:5px;">Go to Login</a>';
    echo '</div>';
    ?>
    
    <p><a href="index.php">← Back to Home</a></p>
</body>
</html>
