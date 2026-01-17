<?php
session_start();

if(!isset($_SESSION['farmer_id'])){
    header("Location: farmer_login.php");
    exit;
}

require_once __DIR__ . "/../config/db.php";

$farmer_id = $_SESSION['farmer_id'];
$farmer_name = $_SESSION['farmer_name'];

$success_msg = "";

// Handle Delete Request
if(isset($_POST['delete_farm'])){
    $farm_id = intval($_POST['farm_id']);

    // Verify farm belongs to logged-in farmer
    $stmt = $conn->prepare("SELECT image FROM farms WHERE id=? AND farmer_id=?");
    $stmt->bind_param("ii", $farm_id, $farmer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        // Delete image file
        if(!empty($row['image']) && file_exists('uploads/'.$row['image'])){
            unlink('uploads/'.$row['image']);
        }
        // Delete farm record
        $stmt_del = $conn->prepare("DELETE FROM farms WHERE id=?");
        $stmt_del->bind_param("i", $farm_id);
        $stmt_del->execute();
        $stmt_del->close();

        $success_msg = "Farm deleted successfully!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Farmer Dashboard | Agro-Tourism</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            min-height: 100vh;
            background: 
                linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)),
                url("updates/farm2.jpg") no-repeat center center/cover;
            color: #333;
        }

        .header {
            background-color: #2e7d32;
            color: white;
            padding: 20px;
            text-align: center;
            border-bottom: 4px solid #1b5e20;
        }

        .header h1 { margin:0; font-size:28px; }

        .actions {
            text-align: center;
            margin: 20px 0;
        }

        .actions a {
            text-decoration: none;
            color: white;
            background: #2e7d32;
            padding: 12px 20px;
            border-radius: 6px;
            margin: 0 10px;
            display: inline-block;
            transition: 0.3s;
        }

        .actions a:hover { background-color: #1b5e20; }

        .success {
            background: #e0ffe0;
            border-left: 5px solid #28a745;
            padding: 10px;
            margin: 10px auto;
            border-radius: 5px;
            color: #006400;
            width: fit-content;
            text-align: center;
        }

        .cards-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        .card {
            background: rgba(255,255,255,0.95);
            width: 250px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .card-content {
            padding: 15px;
        }

        .card-content h3 { margin:0 0 10px 0; color:#2e7d32; }

        .card-content p { margin:5px 0; font-size:14px; color:#555; }

        .card-content .created { font-size:12px; color:#888; }

        .delete-btn {
            display: block;
            width: 100%;
            text-align: center;
            padding: 8px;
            margin-top: 10px;
            background-color: #c62828; 
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
        }

        .delete-btn:hover { background-color: #8e0000; }

       .no-farms {
          text-align: center;
          color: #e53935;
          margin-top: 30px;
          font-size: 18px;
        }


        @media(max-width:600px){
            .cards-container { flex-direction: column; align-items: center; }
        }
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

<!-- Success message -->
<?php if(!empty($success_msg)) { ?>
    <div class='success' id="success-msg"><?php echo $success_msg; ?></div>
<?php } ?>

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
                    <form method='POST' onsubmit='return confirm(\"Are you sure you want to delete this farm?\");'>
                        <input type='hidden' name='farm_id' value='". $row['id'] ."'>
                        <button type='submit' name='delete_farm' class='delete-btn'>🗑 Delete</button>
                    </form>
                </div>
              </div>";
    }
} else {
    echo "<p class='no-farms'>You have not added any farms yet.</p>";
}
?>
</div>

<script>
    // Hide success message after 3 seconds
    setTimeout(function(){
        var msg = document.getElementById('success-msg');
        if(msg) msg.style.display = 'none';
    }, 3000);
</script>

</body>
</html>
