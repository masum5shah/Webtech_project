<?php
session_start();
if(!isset($_SESSION['farmer_id'])){
    header("Location: farmer_login.php");
    exit;
}

require_once __DIR__ . "/../config/db.php";

$farm_id = intval($_GET['farm_id']);
$farmer_id = $_SESSION['farmer_id'];

// fetch farm
$stmt = $conn->prepare("SELECT * FROM farms WHERE id=? AND farmer_id=?");
$stmt->bind_param("ii", $farm_id, $farmer_id);
$stmt->execute();
$res = $stmt->get_result();
$farm = $res->fetch_assoc();

if(!$farm){
    die("Farm not found!");
}

// UPDATE
if(isset($_POST['update_farm'])){

    $name     = $_POST['farm_name'];
    $desc     = $_POST['farm_description'];
    $type     = $_POST['farm_type'];
    $address  = $_POST['address'];
    $capacity = $_POST['capacity'];
    $ride     = $_POST['available_ride'];
    $fee      = $_POST['entry_fee'];

    // keep old image
    $image_name = $farm['image'];

    if(!empty($_FILES['farm_image']['name'])){
        $ext = pathinfo($_FILES['farm_image']['name'], PATHINFO_EXTENSION);
        $new_name = time()."_".uniqid().".".$ext;
        $path = __DIR__."/uploads/".$new_name;

        if(move_uploaded_file($_FILES['farm_image']['tmp_name'], $path)){
            if(!empty($farm['image']) && file_exists(__DIR__."/uploads/".$farm['image'])){
                unlink(__DIR__."/uploads/".$farm['image']);
            }
            $image_name = $new_name;
        }
    }

    $up = $conn->prepare(
        "UPDATE farms SET 
        farm_name=?, farm_description=?, farm_type=?, address=?, capacity=?, available_ride=?, entry_fee=?, image=?
        WHERE id=? AND farmer_id=?"
    );
    $up->bind_param(
        "ssssisssii",
        $name, $desc, $type, $address, $capacity, $ride, $fee, $image_name,
        $farm_id, $farmer_id
    );
    $up->execute();

    header("Location: farmer_dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Farm</title>

<style>
body{
    margin:0;
    min-height:100vh;
    font-family:'Segoe UI',Tahoma;
    background:
      linear-gradient(rgba(0,0,0,.45),rgba(0,0,0,.45)),
      url("updates/farm2.jpg") no-repeat center/cover;
    display:flex;
    justify-content:center;
    align-items:center;
}
.form-box{
    background:rgba(255,255,255,0.95);
    width:450px;
    padding:30px 35px;
    border-radius:15px;
    box-shadow:0 15px 30px rgba(0,0,0,.3);
}
h2{
    text-align:center;
    color:#2e7d32;
    margin-bottom:20px;
}
label{
    font-weight:600;
    margin-top:10px;
    display:block;
    color:#333;
}
input,textarea,select{
    width:100%;
    padding:10px 12px;
    margin-top:6px;
    border:1px solid #ccc;
    border-radius:8px;
    font-size:15px;
}
input:focus,textarea:focus,select:focus{
    border-color:#2e7d32;
    outline:none;
    box-shadow:0 0 6px rgba(46,125,50,.4);
}
img{
    width:100%;
    height:180px;
    object-fit:cover;
    border-radius:8px;
    margin:10px 0;
}
button{
    width:100%;
    margin-top:18px;
    padding:12px;
    background:#2e7d32;
    color:white;
    font-size:16px;
    border:none;
    border-radius:8px;
    cursor:pointer;
}
button:hover{
    background:#1b5e20;
}
</style>

</head>
<body>

<div class="form-box">
<form method="POST" enctype="multipart/form-data">

    <h2>Edit Farm</h2>

    <label>Current Farm Image</label>
    <?php
    $img = (!empty($farm['image']) && file_exists(__DIR__."/uploads/".$farm['image']))
           ? "uploads/".$farm['image']
           : "uploads/default.jpg";
    ?>
    <img src="<?= $img ?>">

    <label>Change Image</label>
    <input type="file" name="farm_image" accept="image/*">

    <label>Farm Name</label>
    <input type="text" name="farm_name" value="<?= htmlspecialchars($farm['farm_name']) ?>" required>

    <label>Farm Description</label>
    <textarea name="farm_description" rows="3"><?= htmlspecialchars($farm['farm_description']) ?></textarea>

    <label>Farm Type</label>
    <select name="farm_type">
        <option value="organic" <?= $farm['farm_type']=="organic"?"selected":"" ?>>Organic</option>
        <option value="eco" <?= $farm['farm_type']=="eco"?"selected":"" ?>>Eco</option>
    </select>

    <label>Address</label>
    <input type="text" name="address" value="<?= htmlspecialchars($farm['address']) ?>">

    <label>Visitor Capacity</label>
    <input type="number" name="capacity" value="<?= $farm['capacity'] ?>">

    <label>Available Ride</label>
    <input type="text" name="available_ride" value="<?= htmlspecialchars($farm['available_ride']) ?>">

    <label>Entry Fee</label>
    <input type="number" name="entry_fee" value="<?= $farm['entry_fee'] ?>">

    <button name="update_farm">Update Farm</button>

</form>
</div>

</body>
</html>
