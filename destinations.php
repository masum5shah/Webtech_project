<?php
session_start();
// Optional: If you want to use the same header/login logic, include db or checks here
// require_once "../config/db.php"; 

// --- DATA: 8 DIVISIONS OF BANGLADESH ---
$divisions = [
    [
        'name' => 'Dhaka',
        'desc' => 'The capital city, known for its vibrant culture, historic Lalbagh Fort, and bustling markets.',
        'img' => 'updates/dhaka.jpg'
    ],
    [
        'name' => 'Chittagong (Chattogram)',
        'desc' => 'Home to the beautiful hill tracts and Cox\'s Bazar, the longest natural sea beach in the world.',
        'img' => 'updates/chittagong.jpg'
    ],
    [
        'name' => 'Sylhet',
        'desc' => 'Famous for its lush tea gardens, Ratargul Swamp Forest, and the crystal clear waters of Jaflong.',
        'img' => 'updates/sylhet.jpg'
    ],
    [
        'name' => 'Khulna',
        'desc' => 'The gateway to the Sundarbans, the world\'s largest mangrove forest and home of the Royal Bengal Tiger.',
        'img' => 'updates/khulna.jpg'
    ],
    [
        'name' => 'Rajshahi',
        'desc' => 'Known as the city of silk and mangoes, featuring archaeological sites like Mahasthangarh.',
        'img' => 'updates/rajshahi.jpg'
    ],
    [
        'name' => 'Rangpur',
        'desc' => 'Rich in history with landmarks like the Tajhat Palace and known for its agricultural heritage.',
        'img' => 'updates/rangpur.jpg'
    ],
    [
        'name' => 'Barisal',
        'desc' => 'The land of rivers, famous for its floating guava markets and backwater tourism.',
        'img' => 'updates/barisal.jpg'
    ],
    [
        'name' => 'Mymensingh',
        'desc' => 'Known for its rich folklore, traditional ballads (Mymensingh Gitika), and natural beauty.',
        'img' => 'updates/mymensingh.jpg'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destinations | Agro-Tourism</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif; }
        body { background: #f9f9f9; }

        /* REUSING NAVBAR STYLES FOR CONSISTENCY */
        .top-bar { background:#f5f5f5; padding:8px 50px; font-size:14px; display:flex; justify-content:space-between; }
        nav { display:flex; justify-content:space-between; align-items:center; padding:15px 50px; background:white; border-bottom:1px solid #eee; }
        .logo { font-size:24px; font-weight:700; color:#2e7d32; text-decoration: none;}
        .nav-links a { margin:0 15px; text-decoration:none; color:#333; }
        .back-btn { background: #2e7d32; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; }

        /* DESTINATIONS GRID */
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .page-title { text-align: center; margin-bottom: 40px; color: #2e7d32; font-size: 36px; }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .card:hover { transform: translateY(-10px); }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .card-content { padding: 20px; }
        .card-content h3 { color: #2e7d32; margin-bottom: 10px; }
        .card-content p { font-size: 14px; color: #555; line-height: 1.6; }
        
        .card-btn {
            display: inline-block;
            margin-top: 15px;
            color: #2e7d32;
            font-weight: 600;
            text-decoration: none;
        }
        .card-btn:hover { text-decoration: underline; }

        @media(max-width: 768px) {
            nav { flex-direction: column; gap: 15px; }
            .nav-links { display: flex; flex-wrap: wrap; justify-content: center; }
        }
    </style>
</head>
<body>

    <div class="top-bar">
        <div>Agro-Tourism Bangladesh</div>
        <div>🌐 English</div>
    </div>

    <nav>
        <a href="index.php" class="logo">Agro-Tourism</a>
        <div class="nav-links">
            <a href="welcomepage.php">Home</a>
            <a href="destinations.php" style="color:#2e7d32; font-weight:600;">Destinations</a>
            <a href="experiences.php">Experiences</a>
            <a href="tourist_login.php">Plan Your Visit</a>
            <a href="articles.php">Articles</a>
        </div>
        <a href="welcomepage.php" class="back-btn">Back Home</a>
    </nav>

    <div class="container">
        <h1 class="page-title">Explore the 8 Divisions</h1>
        
        <div class="grid">
            <?php foreach($divisions as $div): ?>
            <div class="card">
                <img src="<?= $div['img'] ?>" alt="<?= $div['name'] ?>" onerror="this.src='https://via.placeholder.com/400x250?text=<?= $div['name'] ?>'">
                <div class="card-content">
                    <h3><?= $div['name'] ?></h3>
                    <p><?= $div['desc'] ?></p>
                    <a href="#" class="card-btn">View Farms in <?= explode(' ', $div['name'])[0] ?> →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>
</html>