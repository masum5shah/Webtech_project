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
<title>Tourist Dashboard</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI',Tahoma;
    background:
        linear-gradient(rgba(0,0,0,.4),rgba(0,0,0,.4)),
        url("updates/farm2.jpg") no-repeat center/cover;
    transition: background 0.6s ease-in-out;
}

/* MENU */
.menu-btn{
    position:fixed;
    top:15px;
    left:15px;
    font-size:26px;
    cursor:pointer;
    color:white;
    z-index:1000;
}

/* SLIDESHOW BUTTONS */
.slideshow-controls{
    position:fixed;
    top:15px;
    right:15px;
    z-index:1000;
}
.slide-btn{
    padding:10px 14px;
    margin-left:6px;
    background:#2e7d32;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
    font-weight:600;
}
.slide-btn:hover{
    background:#1b5e20;
}

/* SIDEBAR */
.sidebar{
    position:fixed;
    top:0;
    left:-240px;
    width:240px;
    height:100%;
    background:#2e7d32;
    padding-top:60px;
    transition:.3s;
}
.sidebar a{
    display:block;
    padding:14px 20px;
    color:white;
    text-decoration:none;
}
.sidebar a:hover{
    background:#1b5e20;
}
.sidebar.show{
    left:0;
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
    gap:20px;
    justify-content:center;
    padding:30px;
}

.card{
    background:white;
    width:260px;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 8px 20px rgba(0,0,0,.2);
}

.card img{
    width:100%;
    height:160px;
    object-fit:cover;
}

.card-body{
    padding:15px;
}

.card-body h3{
    margin:0 0 10px;
    color:#2e7d32;
}

.card-body p{
    font-size:14px;
    color:#555;
    margin:5px 0;
}

.book-btn{
    display:block;
    margin-top:10px;
    padding:10px;
    background:#2e7d32;
    color:white;
    text-align:center;
    text-decoration:none;
    border-radius:6px;
}
.book-btn:hover{background:#1b5e20;}
</style>
</head>

<body>

<div class="menu-btn" onclick="toggleMenu()">☰</div>

<!-- 🔹 SLIDESHOW BUTTONS (ONLY ADDITION) -->
<div class="slideshow-controls">
    <button class="slide-btn" onclick="prevSlide()">⬅ Prev</button>
    <button class="slide-btn" onclick="nextSlide()">Next ➡</button>
</div>

<div class="sidebar" id="sidebar">
    <a href="tourist_dashboard.php">🏡 View Farms</a>
    <a href="tourist_bookings.php">📖 Your Bookings</a>
      <a href="tourist_chat.php">💬 Chat Box</a>
    <a href="#">👤 Profile</a>
    <a href="tourist_logout.php">🚪 Logout</a>
</div>

<div class="header">
    <h1>Available Farms & Tourist Places</h1>
</div>

<div class="container">
<?php while($row = $result->fetch_assoc()){ ?>
    <div class="card">
        <img src="uploads/<?= htmlspecialchars($row['image']) ?>">
        <div class="card-body">
            <h3><?= htmlspecialchars($row['farm_name']) ?></h3>
            <p><?= htmlspecialchars($row['farm_description']) ?></p>
            <p><strong>Type:</strong> <?= htmlspecialchars($row['farm_type']) ?></p>
            <p><strong>Farmer:</strong> <?= htmlspecialchars($row['farmer_name']) ?></p>
            <a href="book_farm.php?farm_id=<?= $row['id'] ?>" class="book-btn">
                📅 Book Now
            </a>
        </div>
    </div>
<?php } ?>
</div>

<script>
function toggleMenu(){
    document.getElementById('sidebar').classList.toggle('show');
}

/* 🔹 SLIDESHOW LOGIC (ONLY ADDITION) */
const images = [
    "updates/farm2.jpg",
    "updates/farm3.jpg",
    "updates/farm4.jpg",
    "updates/farm5.jpg"
];

let currentIndex = 0;

function setBackground(){
    document.body.style.background =
        "linear-gradient(rgba(0,0,0,.4),rgba(0,0,0,.4)), url('" 
        + images[currentIndex] + "') no-repeat center/cover";
}

function nextSlide(){
    currentIndex = (currentIndex + 1) % images.length;
    setBackground();
}

function prevSlide(){
    currentIndex = (currentIndex - 1 + images.length) % images.length;
    setBackground();
}
</script>

</body>
</html>
