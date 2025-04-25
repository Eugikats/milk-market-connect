<?php
session_start();
include_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $capacity = mysqli_real_escape_string($conn, $_POST['processing_capacity']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO processing_plants (name, email, location, contact, processing_capacity, password) 
            VALUES ('$name', '$email', '$location', '$contact', '$capacity', '$password')";
    
    if (mysqli_query($conn, $sql)) {
        header('Location: dashboard.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Processing Plant</title>
    <link rel="stylesheet" href="../../css/dashboard.css">
</head>
<body>
    <div class="container">
        <h2>Add New Processing Plant</h2>
        <form method="POST" class="form">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Location:</label>
                <input type="text" name="location" required>
            </div>
            <div class="form-group">
                <label>Contact:</label>
                <input type="text" name="contact" required>
            </div>
            <div class="form-group">
                <label>Processing Capacity (liters/day):</label>
                <input type="number" name="processing_capacity" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-primary">Add Plant</button>
            <a href="dashboard.php" class="btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html> 