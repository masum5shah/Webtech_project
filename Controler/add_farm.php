<?php
session_start();

if(!isset($_SESSION['farmer_id'])){
    header("Location: farmer_login.php");
    exit;
}

require_once __DIR__ . "/../config/db.php";

$farmer_id = $_SESSION['farmer_id'];
$errors = [];

if(isset($_POST['add_farm'])){
    $farm_name = trim($_POST['farm_name']);
    $farm_description = trim($_POST['farm_description']);
    $farm_type = trim($_POST['farm_type']);
    $capacity = intval($_POST['capacity']);
    $available_ride = trim($_POST['available_ride']);
    $entry_fee = floatval($_POST['entry_fee']);
    $farm_address = trim($_POST['farm_address']);

    /* Image Upload */
    if(isset($_FILES['farm_image']) && $_FILES['farm_image']['error'] == 0){
        $ext = pathinfo($_FILES['farm_image']['name'], PATHINFO_EXTENSION);
        $new_name = time() . "_" . preg_replace("/[^a-zA-Z0-9]/","_", $farm_name) . "." . $ext;

        $upload_dir = "uploads/";
        if(!is_dir($upload_dir)){
            mkdir($upload_dir, 0755, true);
        }

        if(!move_uploaded_file($_FILES['farm_image']['tmp_name'], $upload_dir.$new_name)){
            $errors[] = "Image upload failed.";
        }
    } else {
        $errors[] = "Farm image required.";
    }

    /* Validation */
    if(
        empty($farm_name) || empty($farm_description) || empty($farm_type) ||
        $capacity <= 0 || $entry_fee < 0 || empty($farm_address)
    ){
        $errors[] = "All fields are required.";
    }

    /* Insert */
    if(empty($errors)){
        $sql = "INSERT INTO farms 
        (farmer_id, farm_name, farm_description, farm_type, capacity, available_ride, entry_fee, address, image)
        VALUES (?,?,?,?,?,?,?,?,?)";

        $stmt = $conn->prepare($sql);

        if(!$stmt){
            die("Prepare failed: ".$conn->error);
        }

        $stmt->bind_param(
            "isssisdss",
            $farmer_id,
            $farm_name,
            $farm_description,
            $farm_type,
            $capacity,
            $available_ride,
            $entry_fee,
            $farm_address,
            $new_name
        );

        if($stmt->execute()){
            header("Location: farmer_dashboard.php?added=1");
            exit;
        } else {
            $errors[] = "Database error: ".$stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Farm</title>
<style>
body{
    margin:0;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:
        linear-gradient(rgba(0,0,0,.4),rgba(0,0,0,.4)),
        url("updates/farm2.jpg") no-repeat center/cover;
    font-family:'Segoe UI';
}
.container{
    background:white;
    width:440px;
    padding:35px;
    border-radius:15px;
}
h2{text-align:center;color:#2e7d32;}
input,textarea{
    width:100%;
    padding:12px;
    margin:10px 0;
    border:1px solid #ccc;
    border-radius:8px;
}
button{
    width:100%;
    padding:12px;
    background:#2e7d32;
    color:white;
    border:none;
    border-radius:8px;
    font-size:16px;
}
.error{
    background:#ffe0e0;
    padding:10px;
    margin-bottom:10px;
    border-left:5px solid #ff4b5c;
}
a{
    display:block;
    text-align:center;
    margin-top:15px;
    color:#2e7d32;
    text-decoration:none;
}
</style>
</head>

<body>
<div class="container">
<h2>Add Farm / Tourist Place</h2>

<?php foreach($errors as $e){ echo "<div class='error'>$e</div>"; } ?>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="farm_name" placeholder="Farm Name" required>
    <textarea name="farm_description" placeholder="Farm Description" rows="4" required></textarea>
    <input type="text" name="farm_type" placeholder="Farm Type" required>

    <input type="number" name="capacity" placeholder="Maximum Capacity (Visitors)" min="1" required>
    <input type="text" name="available_ride" placeholder="Available Ride (Horse, Boat, None)">
    <input type="number" step="0.01" name="entry_fee" placeholder="Entry Fee (per person)" required>

    <input type="text" name="farm_address" placeholder="Farm Address" required>
    <input type="file" name="farm_image" accept="image/*" required>

    <button type="submit" name="add_farm">Add Farm</button>
</form>

<a href="farmer_dashboard.php">⬅ Back to Dashboard</a>
</div>
</body>
</html>
