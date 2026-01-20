<?php
session_start();

if(!isset($_SESSION['tourist_id'])){
    header("Location: tourist_login.php");
    exit;
}

require_once __DIR__ . "/../config/db.php";

$tourist_id = $_SESSION['tourist_id'];

/* Fetch booking history for this tourist */
$sql = "SELECT b.*, f.farm_name, f.address, f.entry_fee
        FROM bookings b
        JOIN farms f ON b.farm_id = f.id
        WHERE b.tourist_id = ?
        ORDER BY b.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tourist_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>Booking History | Agro-Tourism</title>

<style>
*{box-sizing:border-box;}
body{
    margin:0;
    min-height:100vh;
    font-family:'Segoe UI',Tahoma;
    background:
        linear-gradient(rgba(0,0,0,.45),rgba(0,0,0,.45)),
        url("updates/farm2.jpg") no-repeat center/cover;
    display:flex;
}

/* Sidebar */
.sidebar{
    width:240px;
    background:#1b5e20;
    color:white;
    padding:25px 15px;
    display:flex;
    flex-direction:column;
}

.sidebar h2{
    text-align:center;
    margin-bottom:30px;
}

.sidebar a{
    color:white;
    text-decoration:none;
    padding:12px 15px;
    margin-bottom:10px;
    border-radius:6px;
    font-weight:500;
    transition:.3s;
}

.sidebar a:hover{
    background:#2e7d32;
}

/* Main Content */
.main{
    flex:1;
    padding:25px;
    overflow-y:auto;
}

.header{
    background:#2e7d32;
    color:white;
    padding:15px;
    border-radius:10px;
    text-align:center;
    margin-bottom:25px;
}

/* Booking Table */
table{
    width:100%;
    border-collapse:collapse;
    background:white;
    border-radius:10px;
    overflow:hidden;
    box-shadow:0 10px 25px rgba(0,0,0,.25);
}

th, td{
    padding:12px;
    text-align:left;
    border-bottom:1px solid #ddd;
}

th{
    background:#2e7d32;
    color:white;
}

tr:hover{background:#f1f1f1;}

.no-booking{
    color:white;
    text-align:center;
    font-size:16px;
    margin-top:20px;
}

@media(max-width:900px){
    .sidebar{display:none;}
    body{flex-direction:column;}
}
</style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Tourist Panel</h2>
    <a href="tourist_dashboard.php">🏡 Browse Farms</a>
    <a href="booking_history.php">📜 Booking History</a>
    <a href="chat.php">💬 Chat Box</a>
    <a href="tourist_profile.php">👤 Profile</a>
    <a href="tourist_logout.php">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="main">
    <div class="header">
        <h1>My Booking History</h1>
    </div>

    <?php if($result->num_rows > 0){ ?>
        <table>
            <tr>
                <th>#</th>
                <th>Farm Name</th>
                <th>Address</th>
                <th>Visit Date</th>
                <th>People</th>
                <th>Total Paid (৳)</th>
                <th>Status</th>
            </tr>
            <?php $i=1; while($row = $result->fetch_assoc()){ ?>
            <tr>
                <td><?= $i++; ?></td>
                <td><?= htmlspecialchars($row['farm_name']) ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td>
                <td><?= htmlspecialchars($row['visit_date']) ?></td>
                <td><?= htmlspecialchars($row['people']) ?></td>
                <td><?= htmlspecialchars($row['total_amount']) ?></td>
                <td><?= ($row['status']) ? htmlspecialchars($row['status']) : 'Pending' ?></td>
            </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p class="no-booking">You have no bookings yet.</p>
    <?php } ?>
</div>

</body>
</html>
