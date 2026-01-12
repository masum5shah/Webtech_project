<?php
session_start();

if(!isset($_SESSION['farmer_id'])){
    header("Location: farmer_login.php");
    exit;
}

require "db.php";
$farmer_id = $_SESSION['farmer_id'];
$farmer_name = $_SESSION['farmer_name'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Farmer Dashboard | Agro-Tourism</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#f0f2f5; margin:0; }
        .header { background-color:#2193b0; color:white; padding:20px; text-align:center; border-bottom:4px solid #176b87; }
        .header h1 { margin:0; font-size:28px; }
        .actions { text-align:center; margin:20px 0; }
        .actions a { text-decoration:none; color:white; background:#2193b0; padding:12px 20px; border-radius:6px; margin:0 10px; display:inline-block; transition:0.3s; }
        .actions a:hover { background-color:#176b87; }
        .cards-container { display:flex; flex-wrap:wrap; justify-content:center; gap:20px; padding:20px; }
        .card { background:white; width:250px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.1); overflow:hidden; transition:0.3s; }
        .card:hover { transform:translateY(-5px); box-shadow:0 10px 20px rgba(0,0,0,0.2); }
        .card img { width:100%; height:150px; object-fit:cover; }
        .card-content { padding:15px; }
        .card-content h3 { margin:0 0 10px 0; color:#2193b0; }
        .card-content p { margin:5px 0; font-size:14px; color:#555; }
        .card-content .created { font-size:12px; color:#888; }
        .no-farms { text-align:center; color:#555; margin-top:30px; font-size:18px; }
    </style>
</head>
<body>

<div class="header">
    <h1>Welcome, <?php echo htmlspecialchars($farmer_name); ?>!</h1>
</div>

<div class="actions">
    <a href="add_farm.php">➕ Add Farm / Tourist Place</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="cards-container">
<?php
$sql = "SELECT * FROM farms WHERE farmer_id=? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        echo "<div class='card'>
                <img src='uploads/".htmlspecialchars($row['image'])."' alt='Farm Image'>
                <div class='card-content'>
                    <h3>".htmlspecialchars($row['farm_name'])."</h3>
                    <p>".htmlspecialchars($row['farm_description'])."</p>
                    <p><strong>Type:</strong> ".htmlspecialchars($row['farm_type'])."</p>
                    <p><strong>Address:</strong> ".htmlspecialchars($row['address'])."</p>
                    <p class='created'>Added: ".$row['created_at']."</p>
                </div>
              </div>";
    }
} else {
    echo "<p class='no-farms'>You have not added any farms yet.</p>";
}
?>
</div>

</body>
</html>
