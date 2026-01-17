<?php
session_start();

// Only logged-in farmers can access
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
    $farm_address = trim($_POST['farm_address']);

    // File upload
    if(isset($_FILES['farm_image']) && $_FILES['farm_image']['error'] == 0){
        $file_name = $_FILES['farm_image']['name'];
        $file_tmp = $_FILES['farm_image']['tmp_name'];
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_name = time() . "_" . preg_replace("/[^a-zA-Z0-9]/", "_", $farm_name) . "." . $ext;

        // Create uploads folder if not exists
        $upload_dir = "uploads/";
        if(!is_dir($upload_dir)){
            mkdir($upload_dir, 0755, true);
        }
        $destination = $upload_dir . $new_name;

        if(!move_uploaded_file($file_tmp, $destination)){
            $errors[] = "Failed to upload image.";
        }
    } else {
        $errors[] = "Please select an image.";
    }

    // Validate required fields
    if(empty($farm_name) || empty($farm_description) || empty($farm_type) || empty($farm_address)){
        $errors[] = "All fields are required.";
    }

    // Insert into DB
    if(empty($errors)){
        $stmt = $conn->prepare("INSERT INTO farms (farmer_id, farm_name, farm_description, farm_type, address, image) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("isssss", $farmer_id, $farm_name, $farm_description, $farm_type, $farm_address, $new_name);
        if($stmt->execute()){
            header("Location: farmer_dashboard.php?added=1");
            exit;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Farm | Agro-Tourism</title>
    <style>
        /* Body & Background */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            min-height: 100vh;
            background: 
                linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)),
                url("updates/farm2.jpg") no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Container */
        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 35px 45px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            width: 420px;
        }

        h2 {
            text-align:center;
            color:#2e7d32; /* Green like login/register */
            margin-bottom: 25px;
            font-size:26px;
        }

        input, textarea {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            outline: none;
        }

        input:focus, textarea:focus {
            border-color: #2e7d32;
            box-shadow: 0 0 6px rgba(46,125,50,0.4);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #2e7d32; /* Green */
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            margin-top: 15px;
            transition: 0.3s;
        }

        button:hover {
            background-color: #1b5e20; /* Darker green on hover */
        }

        .error {
            background: #ffe0e0;
            padding: 12px;
            margin-bottom: 12px;
            border-left: 5px solid #ff4b5c;
            color: #a70000;
            border-radius: 6px;
        }

        a {
            text-decoration: none;
            color: #2e7d32; /* Green like buttons */
            display:block;
            text-align:center;
            margin-top: 15px;
            font-weight: 500;
        }

        a:hover {
            color: #1b5e20;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Add Farm / Tourist Place</h2>

    <?php
    if(!empty($errors)){
        foreach($errors as $err){
            echo "<div class='error'>$err</div>";
        }
    }
    ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="farm_name" placeholder="Farm Name" required>
        <textarea name="farm_description" placeholder="Farm Description" rows="4" required></textarea>
        <input type="text" name="farm_type" placeholder="Farm Type (Organic, Dairy, Vegetable...)" required>
        <input type="text" name="farm_address" placeholder="Farm Address" required>
        <input type="file" name="farm_image" accept="image/*" required>
        <button type="submit" name="add_farm">Add Farm</button>
    </form>

    <a href="farmer_dashboard.php">⬅ Back to Dashboard</a>
</div>
</body>
</html>
