<?php
include 'db.php';

// Preserve search and filter parameters for redirect
$redirect_params = [];
if (!empty($_GET['search'])) {
    $redirect_params['search'] = $_GET['search'];
}
if (!empty($_GET['filter'])) {
    $redirect_params['filter'] = $_GET['filter'];
}
$redirect_url = 'index.php';
if (!empty($redirect_params)) {
    $redirect_url .= '?' . http_build_query($redirect_params);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: " . $redirect_url);
exit;