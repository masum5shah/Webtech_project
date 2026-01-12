<?php
session_start();
require_once "db.php";

$errors = [];
if(isset($_POST['login'])){
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if(empty($email) || empty($password)){
        $errors[] = "Both fields are required!";
    } else {
        $stmt = $conn->prepare("SELECT id,name,password FROM farmers WHERE email=?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id,$name,$hash);

        if($stmt->num_rows > 0){
            $stmt->fetch();
            if(password_verify($password, $hash)){
                $_SESSION['farmer_id'] = $id;
                $_SESSION['farmer_name'] = $name;

                // ✅ Redirect to farmer dashboard
                header("Location: farmer_dashboard.php");
                exit;
            }else{
                $errors[] = "Incorrect password!";
            }
        } else {
            $errors[] = "Email not registered!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Farmer Login | Agro-Tourism</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #6dd5ed, #2193b0);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            background: white;
            padding: 40px 50px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            width: 400px;
        }

        h2 { text-align: center; margin-bottom: 30px; color: #333; }

        input {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            outline: none;
            font-size: 16px;
        }

        input:focus {
            border-color: #2193b0;
            box-shadow: 0 0 5px rgba(33,147,176,0.5);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #2193b0;
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.3s;
        }

        button:hover { background-color: #176b87; }

        .error {
            background: #ffe0e0;
            border-left: 5px solid #ff4b5c;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            color: #a70000;
        }

        a { color: #2193b0; text-decoration: none; }
        a:hover { text-decoration: underline; }

        p.back { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Farmer Login</h2>

    <?php
    if(!empty($errors)){
        foreach($errors as $err){
            echo "<div class='error'>$err</div>";
        }
    }
    ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>

    <p>Don't have an account? <a href="farmer_register.php">Register here</a></p>
    <p class="back"><a href="welcomepage.php">⬅ Back to Welcome Page</a></p>
</div>
</body>
</html>
