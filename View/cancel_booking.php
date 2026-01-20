<?php
session_start();

if(!isset($_SESSION['tourist_id'])){
    header("Location: tourist_login.php");
    exit;
}

require_once __DIR__ . "/../config/db.php";

if(!isset($_GET['booking_id'])){
    header("Location: tourist_bookings.php");
    exit;
}

$booking_id = intval($_GET['booking_id']);
$tourist_id = $_SESSION['tourist_id'];

/* Check: own booking + unpaid */
$stmt = $conn->prepare("
    SELECT id 
    FROM bookings 
    WHERE id=? AND tourist_id=? AND payment_status='unpaid'
");
$stmt->bind_param("ii", $booking_id, $tourist_id);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows == 0){
    header("Location: tourist_bookings.php?error=invalid");
    exit;
}

/* Cancel booking */
$upd = $conn->prepare("
    UPDATE bookings 
    SET status='cancelled' 
    WHERE id=?
");
$upd->bind_param("i", $booking_id);
$upd->execute();

header("Location: tourist_bookings.php?msg=cancelled");
exit;
