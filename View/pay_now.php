<?php
session_start();
require_once __DIR__ . "/../config/db.php";

if(!isset($_SESSION['tourist_id'])){
    header("Location: tourist_login.php");
    exit;
}

if(!isset($_GET['booking_id'])){
    header("Location: tourist_bookings.php");
    exit;
}

$booking_id = intval($_GET['booking_id']);
$tourist_id = $_SESSION['tourist_id'];

/* Check booking ownership + approved + unpaid */
$stmt = $conn->prepare("
    SELECT b.*, f.farm_name, f.entry_fee, f.capacity
    FROM bookings b
    JOIN farms f ON b.farm_id = f.id
    WHERE b.id=? AND b.tourist_id=? AND b.status='approved' AND b.payment_status='unpaid'
");
$stmt->bind_param("ii", $booking_id, $tourist_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if(!$booking){
    header("Location: tourist_bookings.php?error=invalid");
    exit;
}

/* Handle payment click */
if(isset($_POST['pay_now'])){
    $upd = $conn->prepare("
        UPDATE bookings
        SET payment_status='paid'
        WHERE id=?
    ");
    $upd->bind_param("i", $booking_id);
    $upd->execute();

    // Refresh data to show paid status in receipt
    $booking['payment_status'] = 'paid';
    $paid = true;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Pay Now | Agro-Tourism</title>
<style>
body{font-family:'Segoe UI';background:#f4f6f8;margin:0;display:flex;justify-content:center;align-items:center;height:100vh}
.container{background:white;padding:25px 30px;border-radius:12px;box-shadow:0 10px 25px rgba(0,0,0,.2);width:400px}
h2{text-align:center;color:#2e7d32}
p{font-size:14px;margin:8px 0;color:#444}
button{width:100%;padding:12px;background:#2e7d32;color:white;border:none;border-radius:8px;font-size:16px;cursor:pointer;margin-top:15px}
button:hover{background:#1b5e20}
.receipt{margin-top:20px;padding:15px;border:1px solid #ccc;border-radius:8px;background:#f7f7f7}
</style>
<script>
function printReceipt(){
    var content = document.getElementById('receipt').innerHTML;
    var myWindow = window.open('', 'Print', 'width=600,height=600');
    myWindow.document.write('<html><head><title>Receipt</title></head><body>'+content+'</body></html>');
    myWindow.document.close();
    myWindow.focus();
    myWindow.print();
    myWindow.close();
}
</script>
</head>
<body>
<div class="container">
<h2>Pay Now</h2>

<p><b>Farm:</b> <?= htmlspecialchars($booking['farm_name']) ?></p>
<p><b>Date:</b> <?= $booking['visit_date'] ?></p>
<p><b>People:</b> <?= $booking['people'] ?></p>
<p><b>Total Amount:</b> ৳<?= $booking['total_amount'] ?></p>

<?php if(empty($paid)): ?>
<form method="POST">
    <button name="pay_now">💳 Pay Now</button>
</form>
<?php else: ?>
<div class="receipt" id="receipt">
    <h3 style="text-align:center;color:#2e7d32">Payment Receipt</h3>
    <p><b>Farm:</b> <?= htmlspecialchars($booking['farm_name']) ?></p>
    <p><b>Date:</b> <?= $booking['visit_date'] ?></p>
    <p><b>Tourist:</b> <?= $_SESSION['tourist_name'] ?? 'Anonymous' ?></p>
    <p><b>People:</b> <?= $booking['people'] ?></p>
    <p><b>Total Paid:</b> ৳<?= $booking['total_amount'] ?></p>
    <p><b>Status:</b> Paid</p>
    <p style="text-align:center;margin-top:15px;">Thank you for your payment!</p>
</div>
<button onclick="printReceipt()">🖨 Print Receipt</button>
<a href="tourist_bookings.php" style="display:block;text-align:center;margin-top:15px;color:#2e7d32;text-decoration:none;font-weight:500;">⬅ Back to Bookings</a>
<?php endif; ?>

</div>
</body>
</html>
