<?php
session_start();
require_once __DIR__ . "/../config/db.php";

if(!isset($_SESSION['tourist_id'])){
    header("Location: tourist_login.php");
    exit;
}

$tourist_id = $_SESSION['tourist_id'];

/* SEND MESSAGE */
if(isset($_POST['send'])){
    $message   = trim($_POST['message']);
    $farmer_id = intval($_POST['farmer_id']);

    if($message && $farmer_id){
        $stmt = $conn->prepare("
            INSERT INTO messages
            (sender_type, sender_id, receiver_type, receiver_id, message)
            VALUES ('tourist', ?, 'farmer', ?, ?)
        ");
        $stmt->bind_param("iis", $tourist_id, $farmer_id, $message);
        $stmt->execute();
    }
}

/* FETCH FARMERS */
$farmers = $conn->query("SELECT id, name FROM farmers ORDER BY name ASC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Tourist Chat</title>

<style>
body{
    font-family:'Segoe UI';
    background:#f4f6f8;
    margin:0;
    padding:20px;
}
.container{
    max-width:800px;
    margin:auto;
    background:white;
    padding:20px;
    border-radius:14px;
    box-shadow:0 10px 25px rgba(0,0,0,.25);
}

/* 🔙 BACK BUTTON */
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
.back-btn:hover{
    background:#455a64;
}

select{
    width:100%;
    padding:10px;
    border-radius:8px;
    border:1px solid #ccc;
    margin-bottom:10px;
}
.chat-header{
    background:#2e7d32;
    color:white;
    padding:10px;
    border-radius:8px;
    font-weight:600;
}
.chat-box{
    height:400px;
    border:1px solid #ccc;
    border-radius:8px;
    padding:10px;
    overflow-y:auto;
    background:#f9f9f9;
    margin:10px 0;
}
.message{
    max-width:70%;
    padding:10px;
    border-radius:10px;
    margin:6px 0;
    clear:both;
}
.tourist{
    background:#2e7d32;
    color:white;
    float:right;
}
.farmer{
    background:#9e9e9e;
    color:white;
    float:left;
}
form{
    display:flex;
    gap:10px;
}
input[type=text]{
    flex:1;
    padding:10px;
    border-radius:8px;
    border:1px solid #ccc;
}
button{
    padding:10px 16px;
    background:#2e7d32;
    color:white;
    border:none;
    border-radius:8px;
    cursor:pointer;
}
</style>

<script>
function loadMessages(fid){
    if(!fid) return;

    fetch("get_messages_tourist.php?farmer_id=" + fid)
    .then(res => res.text())
    .then(data => {
        const box = document.getElementById("chat-box");
        box.innerHTML = data;
        box.scrollTop = box.scrollHeight;
    });
}

setInterval(()=>{
    const fid = document.getElementById("farmer_select").value;
    if(fid) loadMessages(fid);
}, 3000);
</script>
</head>

<body>

<div class="container">

    <!-- 🔙 BACK BUTTON -->
    <a href="tourist_dashboard.php" class="back-btn">⬅ Back to Dashboard</a>

    <h2>💬 Tourist Chat</h2>

    <!-- FARMER SELECT -->
    <select id="farmer_select" onchange="
        document.getElementById('farmer_id').value=this.value;
        loadMessages(this.value);
        document.getElementById('chat-header').innerText=
            this.options[this.selectedIndex].text
    ">
        <option value="">Select Farmer</option>

        <?php if($farmers && $farmers->num_rows > 0): ?>
            <?php while($f = $farmers->fetch_assoc()): ?>
                <option value="<?= $f['id'] ?>">
                    <?= htmlspecialchars($f['name']) ?>
                </option>
            <?php endwhile; ?>
        <?php else: ?>
            <option>No farmers found</option>
        <?php endif; ?>
    </select>

    <div class="chat-header" id="chat-header">
        Select a farmer to start chat
    </div>

    <div class="chat-box" id="chat-box">
        <p style="text-align:center;color:#888;">No messages</p>
    </div>

    <form method="POST">
        <input type="hidden" name="farmer_id" id="farmer_id">
        <input type="text" name="message" placeholder="Type message..." required>
        <button name="send">Send</button>
    </form>

</div>

</body>
</html>
