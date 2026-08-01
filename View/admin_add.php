<?php
session_start();
require_once "../config/db.php"; 

if(!isset($_SESSION['admin_logged_in']) || !isset($_GET['table'])){
    header("Location: admin_dashboard.php");
    exit;
}

$table = $_GET['table'];
$popup_msg = ""; 

$columns = [];
$col_query = $conn->query("SHOW COLUMNS FROM $table");
while($row = $col_query->fetch_assoc()){
    if($row['Extra'] != 'auto_increment' && $row['Field'] != 'created_at'){
        $columns[] = $row['Field'];
    }
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $values = [];
    $form_valid = true;
    $optional_fields = ['agent_id', 'farmer_id', 'image', 'description', 'phone', 'address'];

    foreach($columns as $col){
        // Special check for image files
        if($col == 'image'){
            if(empty($_FILES['image']['name']) && !in_array($col, $optional_fields)){
                $popup_msg = "Please select an image!";
                $form_valid = false;
                break;
            }
            continue; 
        }

        $val = isset($_POST[$col]) ? trim($_POST[$col]) : '';
        if($val === '' && !in_array($col, $optional_fields)){
            $popup_msg = "Please fill all required boxes!";
            $form_valid = false;
            break; 
        }
    }

    if($form_valid){
        foreach($columns as $col){
            // --- IMAGE UPLOAD LOGIC ---
            if($col == 'image'){
                if(!empty($_FILES['image']['name'])){
                    $target_dir = "../uploads/"; // Make sure this folder exists!
                    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
                    
                    $file_name = time() . "_" . basename($_FILES["image"]["name"]);
                    $target_file = $target_dir . $file_name;
                    
                    if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)){
                        $values[] = "'" . $conn->real_escape_string($file_name) . "'";
                    } else {
                        $values[] = "NULL";
                    }
                } else {
                    $values[] = "NULL";
                }
                continue;
            }

            $val = isset($_POST[$col]) ? trim($_POST[$col]) : ''; 
            if($val === ''){
                $values[] = "NULL"; 
            } else {
                if($col == 'password') { $val = password_hash($val, PASSWORD_DEFAULT); }
                $values[] = "'" . $conn->real_escape_string($val) . "'";
            }
        }

        $col_str = implode(",", $columns);
        $val_str = implode(",", $values);
        $sql = "INSERT INTO $table ($col_str) VALUES ($val_str)";
        
        try {
            if($conn->query($sql)){
                header("Location: admin_dashboard.php?table=$table&msg=Added Successfully");
                exit;
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $popup_msg = "Username or Email already exists!";
            } else {
                $popup_msg = "Error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Record</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body{ font-family: 'Poppins', sans-serif; background: #f5f5f5; padding: 40px; }
        .form-container{ background: white; max-width: 500px; margin: 0 auto; padding: 30px; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        label{ font-weight: 600; color: #333; font-size: 14px; }
        input, textarea{ width: 100%; padding: 10px; margin: 8px 0 18px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        button{ background: #2e7d32; color: white; padding: 12px; width: 100%; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: 600; }
        button:hover{ background: #1b5e20; }
        .back-link{ display: block; text-align: center; margin-top: 15px; color: #555; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>

    <?php if(!empty($popup_msg)): ?>
    <script>alert("<?= $popup_msg ?>");</script>
    <?php endif; ?>

    <div class="form-container">
        <h2 style="color:#2e7d32; margin-bottom: 20px;">Add New: <?= ucfirst($table) ?></h2>
        
        <form method="POST" enctype="multipart_form-data">
            <?php foreach($columns as $col): ?>
                <label><?= ucfirst(str_replace('_', ' ', $col)) ?></label>
                
                <?php if($col == 'image'): ?>
                    <input type="file" name="image" accept="image/*">
                
                <?php elseif($col == 'password'): ?>
                    <input type="password" name="<?= $col ?>">
                
                <?php elseif(strpos($col, 'email') !== false): ?>
                    <input type="email" name="<?= $col ?>">
                
                <?php elseif($col == 'description' || $col == 'address'): ?>
                    <textarea name="<?= $col ?>" rows="3"></textarea>
                
                <?php else: ?>
                    <input type="text" name="<?= $col ?>" placeholder="<?= in_array($col, ['agent_id', 'farmer_id']) ? '(Optional)' : '' ?>">
                <?php endif; ?>
            <?php endforeach; ?>
            
            <button type="submit">Save Record</button>
        </form>
        
        <a href="admin_dashboard.php?table=<?= $table ?>" class="back-link">Cancel and Go Back</a>
    </div>
</body>
</html>