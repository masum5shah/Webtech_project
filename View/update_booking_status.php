<?php
session_start();

if(!isset($_SESSION['farmer_id'])){
    header("Location: farmer_login.php");
    exit;
}

require_once __DIR__ . "/../config/db.php";

if(!isset($_POST['booking_id'])){
    header("Location: Received_bookings.php");
    exit;
}

$booking_id = intval($_POST['booking_id']);
$farmer_id  = $_SESSION['farmer_id'];

$status = isset($_POST['approve']) ? 'approved' : 'rejected';

/* Only allow farmer to update their own bookings */
$stmt = $conn->prepare("
    UPDATE bookings b
    JOIN farms f ON b.farm_id = f.id
    SET b.status = ?
    WHERE b.id = ? AND f.farmer_id = ?
");
$stmt->bind_param("sii", $status, $booking_id, $farmer_id);
$stmt->execute();

header("Location: Received_bookings.php");
exit;
