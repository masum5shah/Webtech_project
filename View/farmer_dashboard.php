<?php
session_start();

if(!isset($_SESSION['farmer_id'])){
    header("Location: farmer_login.php");
    exit;
}

require_once __DIR__ . "/../config/db.php";

$farmer_id   = $_SESSION['farmer_id'];
$farmer_name = $_SESSION['farmer_name'];
$success_msg = "";

/* Delete Farm */
if(isset($_POST['delete_farm'])){
    $farm_id = intval($_POST['farm_id']);

    $stmt = $conn->prepare("SELECT image FROM farms WHERE id=? AND farmer_id=?");
    $stmt->bind_param("ii", $farm_id, $farmer_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows > 0){
        $row = $res->fetch_assoc();
        $img_path = __DIR__."/uploads/".$row['image'];
        if(!empty($row['image']) && file_exists($img_path)){
            unlink($img_path);
        }

        $del = $conn->prepare("DELETE FROM farms WHERE id=? AND farmer_id=?");
        $del->bind_param("ii", $farm_id, $farmer_id);
        $del->execute();

        $success_msg = "Farm deleted successfully!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Farmer Dashboard</title>

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
    width:260px;
    background:#1b5e20;
    color:white;
    padding:25px 15px;
    transition:.35s ease;
    overflow:hidden;
}

.sidebar.collapsed{
    width:0;
    padding:0;
}

.sidebar h2{
    text-align:center;
    margin-bottom:25px;
}

.sidebar a{
    display:flex;
    align-items:center;
    gap:10px;
    color:white;
    text-decoration:none;
    padding:12px 15px;
    margin-bottom:10px;
    border-radius:8px;
    font-weight:500;
    transition:.2s;
}

.sidebar a:hover{
    background:#2e7d32;
}

/* Main */
.main{
    flex:1;
    padding:20px 25px;
    transition:.35s ease;
}

/* Top bar */
.topbar{
    display:flex;
    align-items:center;
    gap:15px;
    margin-bottom:20px;
}

.menu-btn{
    font-size:22px;
    cursor:pointer;
    background:#2e7d32;
    color:white;
    padding:8px 14px;
    border-radius:10px;
    transition:.25s;
    box-shadow:0 6px 14px rgba(0,0,0,.25);
}

.menu-btn:hover{
    background:#1b5e20;
    transform:scale(1.05);
}

/* Header */
.header{
    background:#2e7d32;
    color:white;
    padding:18px;
    border-radius:14px;
    text-align:center;
    margin-bottom:20px;
}

/* Success */
.success{
    background:#e0ffe0;
    padding:12px 18px;
    margin:15px auto;
    width:fit-content;
    border-left:5px solid #28a745;
    border-radius:8px;
}

/* Cards */
.cards{
    display:flex;
    flex-wrap:wrap;
    gap:20px;
    justify-content:center;
}

.card{
    width:280px;
    background:white;
    border-radius:16px;
    overflow:hidden;
    box-shadow:0 10px 25px rgba(0,0,0,.25);
}

.card img{
    width:100%;
    height:170px;
    object-fit:cover;
}

.card-content{
    padding:15px;
}

.card-content h3{
    margin:0 0 6px;
    color:#2e7d32;
}

.card-content p{
    font-size:14px;
    margin:4px 0;
}

.card-buttons{
    display:flex;
    gap:10px;
    margin-top:10px;
}

.edit-btn,.delete-btn{
    flex:1;
    padding:8px;
    border-radius:8px;
    text-align:center;
    color:white;
    border:none;
    cursor:pointer;
    text-decoration:none;
}

.edit-btn{background:#2e7d32;}
.delete-btn{background:#c62828;}
</style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <h2>🌾 Farmer Panel</h2>

    <a href="add_farm.php">➕ Add Farm</a>
    <a href="Received_bookings.php">📜 Reveived Book</a>
    <a href="farmer_chat.php">💬 Chat Box</a>
    <a href="farmer_profile.php">👤 Profile</a>
    <a href="farmer_logout.php">🚪 Logout</a>
</div>

<!-- Main -->
<div class="main">

    <!-- Top Bar -->
    <div class="topbar">
        <div class="menu-btn" onclick="toggleSidebar()">☰</div>
        <h2 style="color:white;margin:0;">Dashboard</h2>
    </div>

    <div class="header">
        <h1>Welcome, <?= htmlspecialchars($farmer_name) ?></h1>
    </div>

    <?php if($success_msg): ?>
        <div class="success"><?= $success_msg ?></div>
    <?php endif; ?>

    <div class="cards">
    <?php
    $stmt = $conn->prepare("SELECT * FROM farms WHERE farmer_id=?");
    $stmt->bind_param("i", $farmer_id);
    $stmt->execute();
    $res = $stmt->get_result();

    while($row = $res->fetch_assoc()):
        $img = (!empty($row['image']) && file_exists(__DIR__."/uploads/".$row['image']))
            ? "uploads/".$row['image']
            : "uploads/default.jpg";
    ?>
        <div class="card">
            <img src="<?= $img ?>">
            <div class="card-content">
                <h3><?= htmlspecialchars($row['farm_name']) ?></h3>
                <p><?= htmlspecialchars($row['farm_description']) ?></p>
                <p><b>Capacity:</b> <?= $row['capacity'] ?></p>
                <p><b>Ride:</b> <?= htmlspecialchars($row['available_ride']) ?></p>
                <p><b>Fee:</b> ৳<?= $row['entry_fee'] ?></p>

                <div class="card-buttons">
                    <a href="edit_farm.php?farm_id=<?= $row['id'] ?>" class="edit-btn">Edit</a>
                    <form method="POST" onsubmit="return confirm('Delete this farm?')">
                        <input type="hidden" name="farm_id" value="<?= $row['id'] ?>">
                        <button name="delete_farm" class="delete-btn">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
    </div>
</div>

<script>
function toggleSidebar(){
    document.getElementById('sidebar').classList.toggle('collapsed');
}
</script>

</body>
</html>
