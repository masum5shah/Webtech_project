<?php
session_start();

/* Tourist login check */
if(!isset($_SESSION['tourist_id'])){
    header("Location: tourist_login.php");
    exit;
}

require_once __DIR__ . "/../config/db.php";

$tourist_id = $_SESSION['tourist_id'];

if(!isset($_GET['farm_id'])){
    die("Farm ID missing.");
}

$farm_id = intval($_GET['farm_id']);
$errors = [];
$success = "";

/* Fetch farm details */
$stmt = $conn->prepare("SELECT * FROM farms WHERE id=?");
$stmt->bind_param("i", $farm_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0){
    die("Farm not found.");
}

$farm = $result->fetch_assoc();

/* Handle booking */
if(isset($_POST['book_farm'])){
    $visit_date = $_POST['visit_date'];
    $people = (int)$_POST['people'];

    if(empty($visit_date) || $people < 1){
        $errors[] = "All fields are required.";
    }

    /* 🔒 Capacity check */
    if(empty($errors)){
        $stmt = $conn->prepare(
            "SELECT IFNULL(SUM(people),0) AS booked
             FROM bookings
             WHERE farm_id=? AND visit_date=? AND status!='Rejected'"
        );
        $stmt->bind_param("is", $farm_id, $visit_date);
        $stmt->execute();
        $booked = $stmt->get_result()->fetch_assoc()['booked'];

        if(($booked + $people) > $farm['capacity']){
            $errors[] = "Not enough capacity available for this date.";
        }
    }

    /* 💰 Price calculation & insert */
    if(empty($errors)){
        $total_amount = $people * $farm['entry_fee'];

        $stmt = $conn->prepare(
            "INSERT INTO bookings 
            (tourist_id, farm_id, visit_date, people, total_amount)
            VALUES (?,?,?,?,?)"
        );

        if(!$stmt){
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param(
            "iisid",
            $tourist_id,
            $farm_id,
            $visit_date,
            $people,
            $total_amount
        );

        if($stmt->execute()){
            $success = "Booking successful! Total cost: ৳".$total_amount;
        } else {
            $errors[] = "Booking failed: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Book Farm | Agro-Tourism</title>

<style>
body{
    margin:0;
    min-height:100vh;
    font-family:'Segoe UI',Tahoma;
    background:
        linear-gradient(rgba(0,0,0,.4),rgba(0,0,0,.4)),
        url("updates/farm2.jpg") no-repeat center/cover;
    display:flex;
    justify-content:center;
    align-items:center;
}
.container{
    background:rgba(255,255,255,.96);
    width:430px;
    padding:30px 35px;
    border-radius:15px;
    box-shadow:0 10px 30px rgba(0,0,0,.3);
}
h2{
    text-align:center;
    color:#2e7d32;
    margin-bottom:15px;
}
img{
    width:100%;
    height:180px;
    object-fit:cover;
    border-radius:10px;
    margin-bottom:10px;
}
p{
    font-size:14px;
    color:#444;
    margin:6px 0;
}
input{
    width:100%;
    padding:12px;
    margin-top:10px;
    border:1px solid #ccc;
    border-radius:8px;
    font-size:15px;
}
button{
    width:100%;
    padding:12px;
    background:#2e7d32;
    color:white;
    border:none;
    border-radius:8px;
    font-size:16px;
    cursor:pointer;
    margin-top:15px;
}
button:hover{
    background:#1b5e20;
}
.error{
    background:#ffe0e0;
    border-left:5px solid #ff4b5c;
    padding:10px;
    margin-bottom:10px;
    border-radius:5px;
}
.success{
    background:#e0ffe0;
    border-left:5px solid #28a745;
    padding:10px;
    margin-bottom:10px;
    border-radius:5px;
}
a{
    display:block;
    text-align:center;
    margin-top:15px;
    text-decoration:none;
    color:#2e7d32;
    font-weight:500;
}
</style>
</head>

<body>
<div class="container">
<h2>Book Farm</h2>

<img src="uploads/<?php echo htmlspecialchars($farm['image']); ?>" alt="Farm">

<h3><?php echo htmlspecialchars($farm['farm_name']); ?></h3>

<p><?php echo htmlspecialchars($farm['farm_description']); ?></p>
<p><strong>Type:</strong> <?php echo htmlspecialchars($farm['farm_type']); ?></p>
<p><strong>Capacity:</strong> <?php echo $farm['capacity']; ?> people</p>
<p><strong>Available Ride:</strong> <?php echo htmlspecialchars($farm['available_ride']); ?></p>
<p><strong>Entry Fee:</strong> ৳<?php echo $farm['entry_fee']; ?> per person</p>

<?php
foreach($errors as $e){
    echo "<div class='error'>$e</div>";
}
if($success){
    echo "<div class='success'>$success</div>";
}
?>

<form method="POST">
    <input type="date" name="visit_date" required>
    <input type="number" name="people" min="1" placeholder="Number of People" required>
    <button type="submit" name="book_farm">Confirm Booking</button>
</form>

<a href="view_farms.php">⬅ Back to Farms</a>
</div>
</body>
</html>
