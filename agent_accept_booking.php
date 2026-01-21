<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['agent_logged_in']) || !isset($_POST['accept'])) {
    header("Location: agent_login.php");
    exit;
}

$booking_id = intval($_POST['booking_id']);
$agent_id = $_SESSION['agent_id'];

// Update status to 'confirmed'
// This status triggers the "Pay Now" button for the Tourist
$stmt = $conn->prepare("
    UPDATE bookings b
    JOIN farms f ON b.farm_id = f.farm_id
    SET b.status = 'confirmed'
    WHERE b.booking_id = ? AND f.agent_id = ?
");

$stmt->bind_param("ii", $booking_id, $agent_id);

if ($stmt->execute()) {
    // Success: Go back to list
    header("Location: agent_booking_requests.php?msg=accepted");
} else {
    echo "Error: " . $conn->error;
}
exit;
?>