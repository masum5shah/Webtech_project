<?php
session_start();
require_once __DIR__ . "/../config/db.php";

if(!isset($_SESSION['farmer_id']) || !isset($_GET['tourist_id'])){
    exit;
}

$farmer_id = $_SESSION['farmer_id'];
$tourist_id = intval($_GET['tourist_id']);

$stmt = $conn->prepare("
    SELECT * FROM messages
    WHERE (sender_type='farmer' AND sender_id=? AND receiver_type='tourist' AND receiver_id=?)
       OR (sender_type='tourist' AND sender_id=? AND receiver_type='farmer' AND receiver_id=?)
    ORDER BY created_at ASC
");
$stmt->bind_param("iiii", $farmer_id, $tourist_id, $tourist_id, $farmer_id);
$stmt->execute();
$res = $stmt->get_result();

while($m = $res->fetch_assoc()){
    $cls = $m['sender_type']=='farmer' ? 'farmer-msg' : 'tourist-msg';
    echo "<div class='message $cls'>".htmlspecialchars($m['message'])."</div>";
}
?>
