<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['agent_logged_in'])) {
    header("Location: agent_login.php");
    exit;
}

$agent_id = $_SESSION['agent_id'];

// ✅ FETCH: Bookings approved by Farmer ('approved') 
// The Farm must be assigned to THIS agent (f.agent_id = $agent_id)
$sql = "SELECT b.booking_id, t.name as visitor, t.phone, f.farm_name, b.visit_date, b.total_amount, b.status
        FROM bookings b
        JOIN tourists t ON b.tourist_id = t.tourist_id
        JOIN farms f ON b.farm_id = f.farm_id
        WHERE f.agent_id = ? 
        AND b.status = 'approved' 
        ORDER BY b.booking_id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $agent_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agent Requests</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; margin: 0; display: flex; background: #f9f9f9; }
        .sidebar { width: 260px; background: #2e7d32; height: 100vh; color: white; padding: 25px; position: fixed; }
        .sidebar a { display: block; color: white; text-decoration: none; padding: 12px; margin: 5px 0; border-radius: 8px; transition: 0.3s; }
        .sidebar a:hover { background: #1b5e20; }
        .main { margin-left: 310px; padding: 40px; width: calc(100% - 350px); }
        .card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-bottom: 20px; border-left: 5px solid #ff9800; display:flex; justify-content:space-between; align-items:center; }
        .btn-accept { background: #2e7d32; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; border: none; cursor: pointer; }
        .btn-accept:hover { background: #1b5e20; }
        .logout { background: #c62828 !important; margin-top: 40px !important; text-align: center; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Agent Control</h2>
        <a href="agent_dashboard.php">Dashboard</a>
        <a href="agent_booking_requests.php" style="background:#1b5e20;">Booking Requests</a>
        
        <a href="agent_my_farms.php">My Active Farms</a>
        
        <a href="admin_logout.php" class="logout">Logout</a>
    </div>

    <div class="main">
        <h2>🔔 New Booking Requests</h2>
        <p>Bookings approved by Farmers. You must <b>Accept</b> them to allow payment.</p>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
            <div class="card">
                <div>
                    <h3> Booking #<?= $row['booking_id'] ?> - <?= htmlspecialchars($row['farm_name']) ?></h3>
                    <p><strong>Tourist:</strong> <?= htmlspecialchars($row['visitor']) ?> (<?= htmlspecialchars($row['phone']) ?>)</p>
                    <p><strong>Date:</strong> <?= htmlspecialchars($row['visit_date']) ?> | <strong>Amount:</strong> ৳<?= htmlspecialchars($row['total_amount']) ?></p>
                    <p style="color:#e65100; font-weight:bold;">⚠ Waiting for Agent Confirmation</p>
                </div>
                
                <form method="POST" action="agent_accept_booking.php">
                    <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">
                    <button type="submit" name="accept" class="btn-accept">✅ Accept & Assign</button>
                </form>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No new requests found. (Make sure the Farmer has APPROVED the booking, and the Farm is assigned to YOU).</p>
        <?php endif; ?>
    </div>

</body>
</html>