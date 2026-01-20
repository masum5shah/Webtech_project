<?php
session_start();
require_once __DIR__ . "/../config/db.php";

if(!isset($_SESSION['farmer_id'])){
    header("Location: farmer_login.php");
    exit;
}

$farmer_id = $_SESSION['farmer_id'];

/* Send message */
if(isset($_POST['send'])){
    $msg = trim($_POST['message']);
    $tourist_id = intval($_POST['tourist_id']);

    if(!empty($msg)){
        $stmt = $conn->prepare("
            INSERT INTO messages 
            (sender_type, sender_id, receiver_type, receiver_id, message)
            VALUES ('farmer', ?, 'tourist', ?, ?)
        ");
        $stmt->bind_param("iis", $farmer_id, $tourist_id, $msg);
        $stmt->execute();
    }
}

/* Get tourists who sent messages to this farmer */
$users = $conn->query("
    SELECT DISTINCT t.id, t.name
    FROM messages m
    JOIN tourists t ON t.id = m.sender_id
    WHERE m.receiver_type='farmer'
      AND m.receiver_id=$farmer_id
      AND m.sender_type='tourist'
");
?>
<!DOCTYPE html>
<html>
<head>
<title>Farmer Chat</title>

<style>
body{font-family:'Segoe UI';background:#f4f6f8;margin:0;padding:20px}
.container{max-width:800px;margin:auto;background:white;padding:20px;border-radius:12px;box-shadow:0 10px 25px rgba(0,0,0,.2);}

/* 🔙 Back button */
.back-btn{
    display:inline-block;
    margin-bottom:12px;
    padding:8px 14px;
    background:#607d8b;
    color:white;
    text-decoration:none;
    border-radius:6px;
    font-weight:600;
}
.back-btn:hover{background:#455a64;}

.chat-box{border:1px solid #ccc;padding:15px;border-radius:8px;height:400px;overflow-y:scroll;margin-bottom:15px;background:#f9f9f9;}
.message{margin:8px 0;padding:10px;border-radius:8px;max-width:70%;clear:both;}
.farmer-msg{background:#2e7d32;color:white;float:right;}
.tourist-msg{background:#9e9e9e;color:white;float:left;}
input[type=text]{width:80%;padding:10px;border:1px solid #ccc;border-radius:6px}
button{padding:10px 14px;border:none;border-radius:6px;background:#2e7d32;color:white;cursor:pointer}
select{margin-bottom:10px;padding:8px;border-radius:6px}
</style>

<script>
function loadMessages(tid){
    if(!tid) return;
    fetch('get_messages.php?tourist_id='+tid)
    .then(res => res.text())
    .then(data => {
        const box = document.getElementById('chat-box');
        box.innerHTML = data;
        box.scrollTop = box.scrollHeight;
    });
}

setInterval(function(){
    var sel = document.getElementById('tourist_select');
    if(sel && sel.value) loadMessages(sel.value);
},3000);
</script>
</head>

<body>
<div class="container">

    <!-- 🔙 BACK BUTTON -->
    <a href="farmer_dashboard.php" class="back-btn">⬅ Back to Dashboard</a>

    <h2>💬 Farmer Chat</h2>

    <!-- Select Tourist -->
    <select id="tourist_select" onchange="loadMessages(this.value)">
        <option value="">Select Tourist</option>
        <?php while($t = $users->fetch_assoc()): ?>
            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
        <?php endwhile; ?>
    </select>

    <div class="chat-box" id="chat-box">
        <p style="text-align:center;color:#999;">Select a tourist to start chat</p>
    </div>

    <form method="POST" style="margin-top:10px; display:flex; gap:10px;">
        <input type="hidden" name="tourist_id" id="tourist_id_field">
        <input type="text" name="message" placeholder="Type message..." required>
        <button name="send">Send</button>
    </form>

</div>

<script>
document.getElementById('tourist_select').addEventListener('change',function(){
    document.getElementById('tourist_id_field').value = this.value;
});
</script>

</body>
</html>
