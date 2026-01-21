<?php 
session_start();
if(!isset($_SESSION['tourist_id'])){ header("Location: tourist_login.php"); exit; }
require_once __DIR__ . "/../config/db.php";

if(!isset($_GET['farm_id'])){ die("Farm ID missing."); }
$farm_id = intval($_GET['farm_id']);
$tourist_id = $_SESSION['tourist_id'];
$booking_done = false;

// ✅ FIX: WHERE farm_id=?
$stmt = $conn->prepare("SELECT * FROM farms WHERE farm_id=?");
$stmt->bind_param("i", $farm_id);
$stmt->execute();
$farm = $stmt->get_result()->fetch_assoc();

if(isset($_POST['book_farm'])){
    $date = $_POST['visit_date'];
    $people = (int)$_POST['people'];
    $total = $people * $farm['entry_fee'];

    // Insert booking
    $stmt = $conn->prepare("INSERT INTO bookings (tourist_id, farm_id, visit_date, people, total_amount, status) VALUES (?,?,?,?,?,'pending')");
    $stmt->bind_param("iisid", $tourist_id, $farm_id, $date, $people, $total);
    if($stmt->execute()){ $booking_done = true; }
}
?>
<!DOCTYPE html>
<html>
<head><title>Book Farm</title>
<style>body{font-family:sans-serif;background:#f4f6f8;display:flex;justify-content:center;align-items:center;height:100vh;} .box{background:white;padding:30px;border-radius:10px;width:400px;box-shadow:0 10px 25px rgba(0,0,0,0.1);} input{width:100%;padding:10px;margin:10px 0;box-sizing:border-box;} button{width:100%;padding:10px;background:#2e7d32;color:white;border:none;cursor:pointer;}</style>
</head>
<body>
<div class="box">
    <h2>Book: <?= htmlspecialchars($farm['farm_name']) ?></h2>
    <p>Fee:৳<?= $farm['entry_fee'] ?> / person</p>
    
    <?php if($booking_done): ?>
        <p style="color:green;font-weight:bold;">✅ Booking sent! Waiting for approval.</p>
        <a href="tourist_dashboard.php">Back to Dashboard</a>
    <?php else: ?>
        <form method="POST">
            <input type="date" name="visit_date" required>
            <input type="number" name="people" min="1" placeholder="People" required>
            <button name="book_farm">Confirm Booking</button>
        </form>
        <br><a href="tourist_dashboard.php">Cancel</a>
    <?php endif; ?>
</div>
</body>
</html>