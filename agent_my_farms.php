<?php
session_start();
require_once "../config/db.php";

// 1. Check Login
if (!isset($_SESSION['agent_logged_in'])) {
    header("Location: agent_login.php");
    exit;
}

$agent_id = $_SESSION['agent_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Active Farms</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; margin: 0; display: flex; background: #f9f9f9; }
        
        /* Sidebar Styles */
        .sidebar { width: 260px; background: #2e7d32; height: 100vh; color: white; padding: 25px; position: fixed; }
        .sidebar a { display: block; color: white; text-decoration: none; padding: 12px; margin: 5px 0; border-radius: 8px; transition: 0.3s; }
        .sidebar a:hover { background: #1b5e20; }
        .logout { background: #c62828 !important; margin-top: 40px !important; text-align: center; }

        /* Main Content */
        .main { margin-left: 310px; padding: 40px; width: calc(100% - 350px); }
        
        /* Farm Card */
        .farm-card { 
            background: white; border-radius: 12px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.08); 
            margin-bottom: 40px; overflow: hidden; 
            border-top: 5px solid #2e7d32;
        }
        
        .farm-header {
            background: #e8f5e9; padding: 20px; display: flex; justify-content: space-between; align-items: center;
        }
        .farm-header h2 { margin: 0; color: #1b5e20; }
        .farm-header span { font-size: 14px; color: #555; background: #fff; padding: 5px 10px; border-radius: 15px; }

        /* Table Styles */
        .visitor-table { width: 100%; border-collapse: collapse; }
        .visitor-table th, .visitor-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .visitor-table th { background: #f1f1f1; color: #333; font-size: 14px; }
        .visitor-table tr:last-child td { border-bottom: none; }
        
        .paid-badge { background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; }
        .info-msg { padding: 20px; color: #777; font-style: italic; text-align: center; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Agent Control</h2>
        <a href="agent_dashboard.php">Dashboard</a>
        <a href="agent_booking_requests.php">Booking Requests</a>
        <a href="agent_my_farms.php" style="background:#1b5e20;">My Active Farms</a>
        <a href="admin_logout.php" class="logout">Logout</a>
    </div>

    <div class="main">
        <h1>🚜 My Active Farms & Paid Visitors</h1>
        <p>Details of confirmed tourists who have paid and are scheduled to visit.</p>
        <br>

        <?php
        // 2. Fetch All Farms managed by this Agent
        $farm_sql = "SELECT * FROM farms WHERE agent_id = '$agent_id'";
        $farms = $conn->query($farm_sql);

        if ($farms->num_rows > 0):
            while ($farm = $farms->fetch_assoc()):
                $farm_id = $farm['farm_id'];
        ?>
            <div class="farm-card">
                <div class="farm-header">
                    <h2><?= htmlspecialchars($farm['farm_name']) ?></h2>
                    <span>💰 Fee: ৳<?= $farm['entry_fee'] ?> | 📍 <?= htmlspecialchars($farm['address']) ?></span>
                </div>

                <?php
                $book_sql = "SELECT b.*, t.name, t.phone, t.email 
                             FROM bookings b 
                             JOIN tourists t ON b.tourist_id = t.tourist_id 
                             WHERE b.farm_id = '$farm_id' 
                             AND b.payment_status = 'paid' 
                             ORDER BY b.visit_date DESC";
                $bookings = $conn->query($book_sql);
                ?>

                <?php if ($bookings->num_rows > 0): ?>
                    <table class="visitor-table">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Tourist Name</th>
                                <th>Phone</th>
                                <th>Visit Date</th>
                                <th>People</th>
                                <th>Amount Paid</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($b = $bookings->fetch_assoc()): ?>
                            <tr>
                                <td>#<?= $b['booking_id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($b['name']) ?></strong><br>
                                    <small style="color:#777;"><?= htmlspecialchars($b['email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($b['phone']) ?></td>
                                <td><?= htmlspecialchars($b['visit_date']) ?></td>
                                <td><?= $b['people'] ?> Persons</td>
                                <td><strong>৳<?= $b['total_amount'] ?></strong></td>
                                <td><span class="paid-badge">✅ PAID</span></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="info-msg">No paid visitors for this farm yet.</div>
                <?php endif; ?>
            </div>
            <?php 
            endwhile;
        else:
        ?>
            <p>You are not managing any farms yet.</p>
        <?php endif; ?>

    </div>

</body>
</html>