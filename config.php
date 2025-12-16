<?php
// config.php
session_start();

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = ''; // ubah jika pakai password
$DB_NAME = 'netmon';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("DB connection error: " . $conn->connect_error);
}

// helper sederhana
function is_admin_logged_in() {
    return isset($_SESSION['admin_id']);
}
function is_student_logged_in() {
    return isset($_SESSION['student_id']);
}
?>
