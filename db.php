<?php
$host = 'localhost';
$user = 'root'; // default XAMPP username
$pass = '';     // default password is empty
$db   = 'movie_watchlist';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
