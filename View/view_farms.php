<?php
session_start();

if(!isset($_SESSION['tourist_id'])){
    header("Location: tourist_login.php");
    exit;
}

require_once __DIR__ . "/../config/db.php";

/* Fetch all farms */
$sql = "SELECT farms.*, farmers.name AS farmer_name
        FROM farms
        JOIN farmers ON farms.farmer_id = farmers.id
        ORDER BY farms.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>View Farms | Agro-Tourism</title>

<style>
body{
    margin:0;
    min-height:100vh;
    font-family:'Segoe UI',Tahoma;
    background:
        linear-gradient(rgba(0,0,0,.4),rgba(0,0,0,.4)),
        url("updates/farm2.jpg") no-repeat center/cover;
}

.header{
    background:#2e7d32;
    color:white;
    padding:20px;
    text-align:center;
}

.container{
    display:flex;
    flex-wrap:wrap;
    gap:22px;
    justify-content:center;
    padding:30px;
}

.card{
    background:white;
    width:280px;
    border-radius:14px;
    overflow:hidden;
    box-shadow:0 10px 25px rgba(0,0,0,.25);
    transition:.3s;
}

.card:hover{
    transform:translateY(-6px);
}

.card img{
    width:100%;
    height:170px;
    object-fit:cover;
}

.card-body{
    padding:15px 16px;
}

.card-body h3{
    margin:0 0 8px;
    color:#2e7d32;
}

.card-body p{
    font-size:14px;
    color:#555;
    margin:4px 0;
    line-height:1.4;
}

.card-body p strong{
    color:#333;
}

.book-btn{
    display:block;
    margin-top:12px;
    padding:10px;
    background:#2e7d32;
    color:white;
    text-align:center;
    text-decoration:none;
    border-radius:6px;
    font-weight:600;
}

.book-btn:hover{
    background:#1b5e20;
}

.back-btn{
    position:absolute;
    top:20px;
    left:20px;
    background:white;
    color:#2e7d32;
    padding:10px 16px;
    border-radius:6px;
    text-decoration:none;
    font-weight:bold;
    box-shadow:0 4px 10px rgba(0,0,0,.3);
}

.back-btn:hover{
    background:#2e7d32;
    color:white;
}
</style>
</head>

<body>

<a href="tourist_dashboard.php" class="back-btn">⬅ Back</a>

<div class="header">
    <h1>Available Farms & Tourist Places</h1>
</div>

<div class="container">
<?php if($result->num_rows > 0){ ?>
    <?php while($row = $result->fetch_assoc()){ ?>

        <?php
        // Image fallback
        $img = (!empty($row['image']) && file_exists(__DIR__."/uploads/".$row['image']))
               ? "uploads/".$row['image']
               : "uploads/default.jpg";
        ?>

        <div class="card">
            <img src="<?= $img ?>" alt="Farm Image">

            <div class="card-body">
                <h3><?= htmlspecialchars($row['farm_name']) ?></h3>

                <p><?= htmlspecialchars($row['farm_description']) ?></p>

                <p><strong>Type:</strong> <?= htmlspecialchars($row['farm_type']) ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($row['address']) ?></p>
                <p><strong>Capacity:</strong> <?= htmlspecialchars($row['capacity']) ?> people</p>
                <p><strong>Ride:</strong> <?= htmlspecialchars($row['available_ride']) ?></p>
                <p><strong>Entry Fee:</strong> ৳<?= htmlspecialchars($row['entry_fee']) ?></p>

                <p><strong>Farmer:</strong> <?= htmlspecialchars($row['farmer_name']) ?></p>

                <a href="book_farm.php?farm_id=<?= $row['id'] ?>" class="book-btn">
                    📅 Book Now
                </a>
            </div>
        </div>

    <?php } ?>
<?php } else { ?>
    <p style="color:white;font-size:18px;">No farms available.</p>
<?php } ?>
</div>

</body>
</html>
