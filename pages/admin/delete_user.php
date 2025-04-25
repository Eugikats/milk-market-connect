<?php
session_start();
include_once '../../config/db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php?role=admin');
    exit;
}

if (isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $id = (int)$_GET['id'];
    
    if ($type === 'farmer') {
        $sql = "DELETE FROM farmers WHERE id = $id";
    } elseif ($type === 'plant') {
        $sql = "DELETE FROM processing_plants WHERE id = $id";
    }
    
    if (isset($sql) && mysqli_query($conn, $sql)) {
        header('Location: dashboard.php');
        exit;
    }
}

header('Location: dashboard.php'); 