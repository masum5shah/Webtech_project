<?php
session_start();
require_once "../config/db.php";

// 1. SECURITY: Redirect if Agent is not logged in
if (!isset($_SESSION['agent_logged_in'])) {
    header("Location: agent_login.php");
    exit;
}

$agent_id = $_SESSION['agent_id'];

// 2. FETCH SUMMARY STATS
$farm_count = 0;
$pending_request_count = 0;

// Count farms managed by this agent
$farm_res = $conn->query("SELECT COUNT(*) as total FROM farms WHERE agent_id = '$agent_id'");
if($farm_res) { $farm_count = $farm_res->fetch_assoc()['total']; }

// Count bookings that are 'approved' (Waiting for Agent)
$book_res = $conn->query("
    SELECT COUNT(*) as total 
    FROM bookings b
    JOIN farms f ON b.farm_id = f.farm_id
    WHERE f.agent_id = '$agent_id' 
    AND b.status = 'approved'
");
if($book_res) { $pending_request_count = $book_res->fetch_assoc()['total']; }

// 3. FETCH RECENT REQUESTS (Farmer Approved, Waiting for Agent)
$sql = "SELECT b.booking_id, t.name as visitor, f.farm_name, b.status, b.total_amount
        FROM bookings b
        JOIN tourists t ON b.tourist_id = t.tourist_id
        JOIN farms f ON b.farm_id = f.farm_id
        WHERE f.agent_id = '$agent_id' 
        AND b.status = 'approved' 
        ORDER BY b.booking_id DESC LIMIT 5";

$bookings = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agent Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; margin: 0; display: flex; background: #f9f9f9; }
        .sidebar { width: 260px; background: #2e7d32; height: 100vh; color: white; padding: 25px; position: fixed; }
        .sidebar h2 { margin-bottom: 30px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; }
        .sidebar a { display: block; color: white; text-decoration: none; padding: 12px; margin: 5px 0; border-radius: 8px; transition: 0.3s; }
        .sidebar a:hover { background: #1b5e20; }
        
        .main { margin-left: 310px; padding: 40px; width: calc(100% - 350px); }
        .header { margin-bottom: 30px; }
        
        .stats { display: flex; gap: 20px; margin-bottom: 30px; }
        .card { background: white; padding: 25px; border-radius: 12px; flex: 1; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .card h3 { margin: 0; color: #666; font-size: 14px; text-transform: uppercase; }
        .card p { font-size: 28px; font-weight: bold; margin: 10px 0 0 0; color: #2e7d32; }
        
        .table-container { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f1f1f1; font-weight: 600; color: #333; }
        
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: capitalize; }
        .approved { background: #ff9800; color: white; } /* Orange for Action Needed */
        
        .action-btn { background: #2e7d32; color: white; text-decoration: none; padding: 5px 10px; border-radius: 5px; font-size: 12px; }

        .logout { background: #c62828 !important; margin-top: 40px !important; text-align: center; font-weight: 600; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Agent Control</h2>
        <a href="agent_dashboard.php">Dashboard</a>
        <a href="agent_booking_requests.php">Booking Requests</a> 
        <a href="agent_my_farms.php">My Active Farms</a>
        <a href="admin_logout.php" class="logout">Logout</a>
    </div>

    <div class="main">
        <div class="header">
            <h1>Welcome, <?= htmlspecialchars($_SESSION['agent_user']) ?></h1>
            <p>Here are the booking requests waiting for your confirmation.</p>
        </div>

        <div class="stats">
            <div class="card">
                <h3>My Active Farms</h3>
                <p><?= $farm_count ?></p>
            </div>
            <div class="card">
                <h3>Pending Requests</h3>
                <p><?= $pending_request_count ?></p>
            </div>
            <div class="card">
                <h3>Status</h3>
                <p style="font-size:16px; color:#555;">Waiting for Action</p>
            </div>
        </div>

        <div class="table-container">
            <h3>Recent Requests (Farmer Approved)</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tourist</th>
                        <th>Farm</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($bookings && $bookings->num_rows > 0): 
                        while($row = $bookings->fetch_assoc()): 
                    ?>
                        <tr>
                            <td>#<?= $row['booking_id'] ?></td>
                            <td><?= htmlspecialchars($row['visitor']) ?></td>
                            <td><?= htmlspecialchars($row['farm_name']) ?></td>
                            <td>৳<?= htmlspecialchars($row['total_amount']) ?></td>
                            <td>
                                <span class="status-badge approved">
                                    Action Needed
                                </span>
                            </td>
                            <td>
                                <a href="agent_booking_requests.php" class="action-btn">Review & Accept</a>
                            </td>
                        </tr>
                    <?php 
                        endwhile; 
                    else: 
                    ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 30px; color: #888;">
                                No new requests found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>