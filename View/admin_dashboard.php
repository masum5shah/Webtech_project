<?php
session_start();
require_once "../config/db.php"; 

// 1. SECURITY: Redirect if not logged in
if(!isset($_SESSION['admin_logged_in'])){ 
    header("Location: admin_login.php"); 
    exit; 
}

// 2. CONFIGURATION: List all allowed tables
$allowed_tables = ['tourists', 'farmers', 'agents', 'farms']; 

// 3. GET CURRENT TABLE
$table = isset($_GET['table']) && in_array($_GET['table'], $allowed_tables) ? $_GET['table'] : 'tourists';

// 4. HANDLE DELETION
if(isset($_GET['delete_id']) && isset($_GET['pk'])){
    $del_id = $_GET['delete_id'];
    $pk = $_GET['pk'];
    
    // Security: Validate column name
    if(preg_match('/^[a-zA-Z0-9_]+$/', $pk)){
        $stmt = $conn->prepare("DELETE FROM $table WHERE $pk = ?");
        $stmt->bind_param("s", $del_id);
        $stmt->execute();
        header("Location: admin_dashboard.php?table=$table&msg=Deleted Successfully");
        exit;
    }
}

// 5. SEARCH & SORT PARAMETERS
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : ''; 
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';

// 6. FETCH COLUMNS DYNAMICALLY
$columns = [];
$pk_column = "id"; 

$col_query = $conn->query("SHOW COLUMNS FROM $table");
if(!$col_query){ die("Error: Table '$table' does not exist."); }

while($row = $col_query->fetch_assoc()){
    $columns[] = $row['Field'];
    if($row['Key'] == 'PRI') $pk_column = $row['Field']; 
}

// 7. BUILD QUERY
$sql = "SELECT * FROM $table";
if($search){
    $sql .= " WHERE ";
    $conditions = [];
    foreach($columns as $col){ 
        $conditions[] = "$col LIKE '%$search%'"; 
    }
    $sql .= implode(" OR ", $conditions);
}
if($sort && in_array($sort, $columns)){
    $sql .= " ORDER BY $sort $order";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body{ font-family: 'Poppins', sans-serif; margin:0; display:flex; background:#f4f6f8; }
        
        /* SIDEBAR */
        .sidebar{ width: 250px; background: #2e7d32; min-height: 100vh; color: white; padding: 20px; position:fixed;}
        .sidebar h2{ text-align: center; margin-bottom: 30px; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 10px; }
        .sidebar a{ display: block; color: rgba(255,255,255,0.8); text-decoration: none; padding: 12px; margin-bottom: 5px; border-radius: 5px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active{ background: rgba(255,255,255,0.2); color: white; font-weight: 600; }
        
        /* CONTENT */
        .content{ margin-left: 280px; padding: 30px; width: calc(100% - 280px); }
        .top-bar{ display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        
        /* TABLES */
        .table-wrapper{ background: white; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); overflow-x: auto; }
        table{ width: 100%; border-collapse: collapse; }
        th, td{ padding: 15px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; white-space: nowrap; }
        th{ background: #f8f9fa; color: #333; font-weight: 600; }
        th a{ text-decoration: none; color: #333; }
        tr:hover{ background: #f1f8e9; }
        
        /* BUTTONS */
        .btn{ padding: 8px 15px; border-radius: 5px; text-decoration: none; color: white; font-size: 13px; font-weight: 600; }
        .btn-add{ background: #2e7d32; border: none; cursor: pointer; display: inline-block; }
        .btn-del{ background: #e53935; }
        .btn-del:hover{ background: #b71c1c; }
        .search-box{ padding: 10px; border: 1px solid #ccc; border-radius: 5px; width: 250px; outline: none; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Agro Admin</h2>
        <?php foreach($allowed_tables as $t): ?>
            <a href="?table=<?= $t ?>" class="<?= $table == $t ? 'active' : '' ?>">
                Manage <?= ucfirst($t) ?>
            </a>
        <?php endforeach; ?>
        <br><br>
       <a href="admin_logout.php" style="background:#d32f2f; text-align: center;">Logout</a>
    </div>

    <div class="content">
        <div class="top-bar">
            <h1>Listing: <span style="color:#2e7d32"><?= ucfirst($table) ?></span></h1>
            
            <div style="display:flex; gap:15px; align-items: center;">
                <form method="GET" style="display:flex; gap:10px;">
                    <input type="hidden" name="table" value="<?= $table ?>">
                    <input type="text" name="search" class="search-box" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-add">Search</button>
                </form>

                <a href="admin_add.php?table=<?= $table ?>" class="btn btn-add">+ Add New</a>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <?php foreach($columns as $col): ?>
                            <th>
                                <a href="?table=<?= $table ?>&search=<?= $search ?>&sort=<?= $col ?>&order=<?= $order == 'ASC' ? 'DESC' : 'ASC' ?>">
                                    <?= ucfirst(str_replace('_', ' ', $col)) ?> 
                                    <?= $sort == $col ? ($order == 'ASC' ? '▲' : '▼') : '↕' ?>
                                </a>
                            </th>
                        <?php endforeach; ?>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <?php foreach($row as $key => $val): ?>
                                <td>
                                    <?php if($key == 'image' && !empty($val)): ?>
                                        <img src="../uploads/<?= htmlspecialchars($val) ?>" style="width:40px; height:40px; object-fit:cover; border-radius:4px;">
                                    <?php else: ?>
                                        <?= htmlspecialchars(substr($val ?? '', 0, 50)) . (strlen($val ?? '') > 50 ? '...' : '') ?>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                            
                            <td>
                                <a href="?table=<?= $table ?>&delete_id=<?= $row[$pk_column] ?>&pk=<?= $pk_column ?>" 
                                   class="btn btn-del" 
                                   onclick="return confirm('Delete this record permanently?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="<?= count($columns)+1 ?>" style="text-align:center;">No records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>