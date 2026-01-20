<?php
session_start();

if(!isset($_SESSION['farmer_id'])){
    header("Location: farmer_login.php");
    exit;
}

require_once __DIR__ . "/../config/db.php";

$farmer_id = $_SESSION['farmer_id'];

/* Fetch bookings for this farmer's farms */
$stmt = $conn->prepare("
    SELECT b.*, t.name AS tourist_name, f.farm_name
    FROM bookings b
    JOIN farms f ON b.farm_id = f.id
    JOIN tourists t ON b.tourist_id = t.id
    WHERE f.farmer_id = ?
    ORDER BY b.id DESC
");
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<title>Received Bookings</title>
<style>
body{font-family:'Segoe UI';background:#f4f6f8;margin:0}
.container{padding:30px}
.back-btn{
    display:inline-block;
    margin-bottom:20px;
    padding:10px 18px;
    background:#607d8b;
    color:white;
    text-decoration:none;
    border-radius:6px;
    font-weight:600;
}
.back-btn:hover{background:#455a64}
.card{
    background:white;
    padding:15px;
    border-radius:10px;
    margin-bottom:15px;
    box-shadow:0 6px 15px rgba(0,0,0,.15)
}
.badge{padding:4px 8px;border-radius:4px;color:white;font-size:12px}
.pending{background:#9e9e9e}
.approved{background:#2e7d32}
.rejected{background:#c62828}
.btn{
    padding:8px 14px;
    border:none;
    border-radius:6px;
    color:white;
    cursor:pointer;
    margin-right:5px
}
.approve{background:#2e7d32}
.reject{background:#c62828}
</style>
</head>

<body>
<div class="container">

<!-- 🔙 Back Button -->
<a href="farmer_dashboard.php" class="back-btn">⬅ Back to Dashboard</a>

<h2>📜 Received Booking Requests</h2>

<?php if($result->num_rows==0): ?>
    <p>No bookings received yet.</p>
<?php endif; ?>

<?php while($b = $result->fetch_assoc()): ?>
<div class="card">
    <p><b>Farm:</b> <?= htmlspecialchars($b['farm_name']) ?></p>
    <p><b>Tourist:</b> <?= htmlspecialchars($b['tourist_name']) ?></p>
    <p><b>Date:</b> <?= $b['visit_date'] ?></p>
    <p><b>People:</b> <?= $b['people'] ?></p>
    <p>
        <b>Status:</b>
        <span class="badge <?= $b['status'] ?>">
            <?= ucfirst($b['status']) ?>
        </span>
    </p>

    <?php if($b['status']=='pending'): ?>
    <form method="POST" action="update_booking_status.php">
        <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
        <button name="approve" class="btn approve">✔ Approve</button>
        <button name="reject" class="btn reject">✖ Reject</button>
    </form>
    <?php endif; ?>
</div>
<?php endwhile; ?>

</div>
</body>
</html>
