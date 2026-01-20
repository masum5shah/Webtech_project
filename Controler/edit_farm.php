<?php
session_start();

if(!isset($_SESSION['farmer_id'])){
    header("Location: farmer_login.php");
    exit;
}

require_once __DIR__ . "/../config/db.php";

$farmer_id = $_SESSION['farmer_id'];
$errors = [];
$success_msg = "";

// Get farm ID
if(!isset($_GET['farm_id'])){
    header("Location: farmer_dashboard.php");
    exit;
}

$farm_id = intval($_GET['farm_id']);

// Fetch farm data
$stmt = $conn->prepare("SELECT * FROM farms WHERE id=? AND farmer_id=?");
$stmt->bind_param("ii", $farm_id, $farmer_id);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows == 0){
    die("Farm not found or you don't have permission.");
}
$farm = $result->fetch_assoc();

// Handle form submission
if(isset($_POST['update_farm'])){
    $farm_name = trim($_POST['farm_name']);
    $farm_description = trim($_POST['farm_description']);
    $farm_type = trim($_POST['farm_type']);
    $farm_address = trim($_POST['farm_address']);
    $capacity = intval($_POST['capacity']);
    $available_ride = trim($_POST['available_ride']);
    $entry_fee = floatval($_POST['entry_fee']);
    $new_image = $farm['image']; // default to old image

    // Validate required fields
    if(empty($farm_name) || empty($farm_description) || empty($farm_type) || empty($farm_address) || $capacity <= 0 || $entry_fee < 0){
        $errors[] = "Please fill in all fields correctly.";
    }

    // Handle image upload ONLY if a new file is selected
    if(isset($_FILES['farm_image']) && $_FILES['farm_image']['error'] === 0){
        $file_tmp = $_FILES['farm_image']['tmp_name'];
        $file_ext = strtolower(pathinfo($_FILES['farm_image']['name'], PATHINFO_EXTENSION));

        // Generate safe unique filename
        $new_name = time() . "_" . preg_replace("/[^a-zA-Z0-9]/", "_", $farm_name) . "." . $file_ext;
        $upload_dir = __DIR__ . "/uploads/"; // Absolute path for saving file

        if(!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

        $destination = $upload_dir . $new_name;

        if(move_uploaded_file($file_tmp, $destination)){
            // Only delete old image if new upload successful
            if(!empty($farm['image']) && file_exists($upload_dir . $farm['image'])){
                unlink($upload_dir . $farm['image']);
            }
            $new_image = $new_name;
        } else {
            $errors[] = "Failed to upload new image.";
        }
    }

    // Check if any change is made
    $fields_changed = (
        $farm_name !== $farm['farm_name'] ||
        $farm_description !== $farm['farm_description'] ||
        $farm_type !== $farm['farm_type'] ||
        $farm_address !== $farm['address'] ||
        $capacity !== (int)$farm['capacity'] ||
        $available_ride !== $farm['available_ride'] ||
        $entry_fee !== (float)$farm['entry_fee'] ||
        $new_image !== $farm['image']
    );

    if(empty($errors)){
        if($fields_changed){
            $stmt = $conn->prepare("UPDATE farms SET farm_name=?, farm_description=?, farm_type=?, capacity=?, available_ride=?, entry_fee=?, address=?, image=? WHERE id=? AND farmer_id=?");
            $stmt->bind_param(
                "sssisssdii",
                $farm_name,
                $farm_description,
                $farm_type,
                $capacity,
                $available_ride,
                $entry_fee,
                $farm_address,
                $new_image,
                $farm_id,
                $farmer_id
            );

            if($stmt->execute()){
                $success_msg = "Farm updated successfully!";
                // Refresh farm data
                $stmt = $conn->prepare("SELECT * FROM farms WHERE id=? AND farmer_id=?");
                $stmt->bind_param("ii", $farm_id, $farmer_id);
                $stmt->execute();
                $farm = $stmt->get_result()->fetch_assoc();
            } else {
                $errors[] = "Database error: " . $stmt->error;
            }
        } else {
            $success_msg = "No changes detected.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Farm | Agro-Tourism</title>
<style>
body{font-family:'Segoe UI',Tahoma;margin:0;min-height:100vh;background:linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('updates/farm2.jpg') no-repeat center/cover;display:flex;justify-content:center;align-items:center;}
.container{background:rgba(255,255,255,0.95);padding:35px 45px;border-radius:15px;width:450px;}
h2{color:#2e7d32;text-align:center;margin-bottom:25px;}
input,textarea{width:100%;padding:12px 15px;margin:10px 0;border:1px solid #ccc;border-radius:8px;font-size:16px;}
input:focus,textarea:focus{border-color:#2e7d32;box-shadow:0 0 6px rgba(46,125,50,0.4);}
button{width:100%;padding:12px;background:#2e7d32;color:white;border:none;border-radius:8px;font-size:16px;cursor:pointer;margin-top:15px;}
button:hover{background:#1b5e20;}
.error{background:#ffe0e0;padding:12px;margin-bottom:12px;border-left:5px solid #ff4b5c;color:#a70000;border-radius:6px;}
.success{background:#e0ffe0;padding:12px;margin-bottom:12px;border-left:5px solid #28a745;color:#006400;border-radius:6px;}
a{display:block;text-align:center;margin-top:15px;color:#2e7d32;text-decoration:none;font-weight:500;}
a:hover{color:#1b5e20;}
img{width:100%;border-radius:8px;margin-bottom:10px;object-fit:cover;max-height:200px;}
</style>
</head>
<body>
<div class="container">
<h2>Edit Farm</h2>

<?php
if(!empty($errors)){
    foreach($errors as $err){
        echo "<div class='error'>$err</div>";
    }
}
if(!empty($success_msg)){
    echo "<div class='success'>$success_msg</div>";
}
?>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="farm_name" value="<?= htmlspecialchars($farm['farm_name']) ?>" placeholder="Farm Name" required>
    <textarea name="farm_description" rows="4" placeholder="Farm Description" required><?= htmlspecialchars($farm['farm_description']) ?></textarea>
    <input type="text" name="farm_type" value="<?= htmlspecialchars($farm['farm_type']) ?>" placeholder="Farm Type" required>

    <input type="number" name="capacity" value="<?= htmlspecialchars($farm['capacity']) ?>" placeholder="Maximum Capacity (Visitors)" min="1" required>
    <input type="text" name="available_ride" value="<?= htmlspecialchars($farm['available_ride']) ?>" placeholder="Available Ride (Horse, Boat, None)">
    <input type="number" step="0.01" name="entry_fee" value="<?= htmlspecialchars($farm['entry_fee']) ?>" placeholder="Entry Fee (per person)" required>

    <input type="text" name="farm_address" value="<?= htmlspecialchars($farm['address']) ?>" placeholder="Farm Address" required>

    <label>Current Image:</label>
    <img id="preview" src="uploads/<?= htmlspecialchars($farm['image']) ?>" alt="Farm Image">
    <input type="file" name="farm_image" accept="image/*">

    <button type="submit" name="update_farm">Update Farm</button>
</form>

<a href="farmer_dashboard.php">⬅ Back to Dashboard</a>

<script>
const fileInput = document.querySelector('input[name="farm_image"]');
const previewImg = document.getElementById('preview');

fileInput.addEventListener('change', function(){
    const file = this.files[0];
    if(file){
        const reader = new FileReader();
        reader.onload = function(e){
            previewImg.src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>

</div>
</body>
</html>
