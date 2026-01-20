<?php
session_start();

if(!isset($_SESSION['tourist_id'])){
    header("Location: tourist_login.php");
    exit;
}

require_once __DIR__ . "/../config/db.php";

$tourist_id = $_SESSION['tourist_id'];

$stmt = $conn->prepare("
    SELECT b.*, f.farm_name, f.image
    FROM bookings b
    JOIN farms f ON b.farm_id = f.id
    WHERE b.tourist_id = ?
    ORDER BY b.id DESC
");
$stmt->bind_param("i", $tourist_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>Your Bookings</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI',Tahoma;
    background:#f4f6f8;
}

.container{
    padding:30px;
}

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
.back-btn:hover{
    background:#455a64;
}

.cards{
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));
    gap:20px;
}

.card{
    background:white;
    border-radius:12px;
    box-shadow:0 6px 16px rgba(0,0,0,.15);
    overflow:hidden;
}

.card img{
    width:100%;
    height:170px;
    object-fit:cover;
}

.card-body{
    padding:15px;
}

.card-body h3{
    margin:0 0 8px;
    color:#2e7d32;
}

.status{
    font-size:14px;
    margin-top:5px;
}

.pay-btn{
    display:block;
    margin-top:12px;
    padding:10px;
    background:#ff9800;
    color:white;
    text-align:center;
    text-decoration:none;
    border-radius:6px;
    font-weight:600;
}
.pay-btn:hover{
    background:#e68900;
}

.cancel-btn{
    display:block;
    margin-top:10px;
    padding:10px;
    background:#d32f2f;
    color:white;
    text-align:center;
    text-decoration:none;
    border-radius:6px;
    font-weight:600;
}
.cancel-btn:hover{
    background:#b71c1c;
}

.badge{
    display:inline-block;
    padding:4px 8px;
    border-radius:4px;
    font-size:12px;
    color:white;
}
.pending{background:#9e9e9e;}
.approved{background:#2e7d32;}
.rejected{background:#c62828;}
.paid{background:#1565c0;}
.cancelled{background:#616161;}
</style>
</head>

<body>

<div class="container">

    <!-- 🔙 Back Button -->
    <a href="tourist_dashboard.php" class="back-btn">⬅ Back to Dashboard</a>

    <h2>Your Bookings</h2>

    <?php if(isset($_GET['msg']) && $_GET['msg']=='cancelled'){ ?>
        <p style="color:green;font-weight:600;">✅ Booking cancelled successfully.</p>
    <?php } ?>

    <?php if($result->num_rows > 0){ ?>
    <div class="cards">
        <?php while($b = $result->fetch_assoc()){ ?>
        <div class="card">
            <img src="uploads/<?= htmlspecialchars($b['image']) ?>" alt="Farm">

            <div class="card-body">
                <h3><?= htmlspecialchars($b['farm_name']) ?></h3>

                <div class="status">
                    Status:
                    <span class="badge <?= htmlspecialchars($b['status']) ?>">
                        <?= ucfirst($b['status']) ?>
                    </span>
                </div>

                <?php if($b['payment_status']=='paid'){ ?>
                    <div class="status">
                        Payment:
                        <span class="badge paid">Paid</span>
                    </div>
                <?php } ?>

                <?php if($b['status']=='approved' && $b['payment_status']=='unpaid'){ ?>
                    <a href="pay_now.php?booking_id=<?= $b['id'] ?>" class="pay-btn">
                        💳 Pay Now
                    </a>
                <?php } ?>

                <?php if(
                    ($b['status']=='pending' || $b['status']=='approved') 
                    && $b['payment_status']=='unpaid'
                ){ ?>
                    <a href="cancel_booking.php?booking_id=<?= $b['id'] ?>"
                       class="cancel-btn"
                       onclick="return confirm('Are you sure you want to cancel this booking?');">
                        ❌ Cancel Booking
                    </a>
                <?php } ?>

            </div>
        </div>
        <?php } ?>
    </div>
    <?php } else { ?>
        <p>No bookings found.</p>
    <?php } ?>

</div>

</body>
</html>
