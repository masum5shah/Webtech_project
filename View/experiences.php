<?php
session_start();
// Optional: require_once "../config/db.php"; 

// --- TESTIMONIALS DATA ---
// I matched the quotes to the names based on the structure provided.
// Added a placeholder name for the first quote since it had no specific author tag in the prompt.
$reviews = [
    [
        'name' => 'Fatima Y., Nature Enthusiast',
        'quote' => 'Waking up to the mist over the tea gardens in Sylhet was a dream. Agro-Tourism provided an authentic look into local life. The tea-plucking workshop was the highlight! The natural beauty here is unmatched.',
        'initial' => 'F'
    ],
    [
        'name' => 'Ahmed R., Domestic Tourist',
        'quote' => 'Never knew the Barisal backwaters could be this peaceful. Being able to pick fresh guava directly from the floating orchards and seeing how the farmers work was incredible. Truly a refreshing escape from Dhaka\'s chaos.',
        'initial' => 'A'
    ],
    [
        'name' => 'The Chowdhury Family',
        'quote' => 'Our kids loved feeding the livestock and learning about organic rice farming. It’s rare to find such a well-organized tour that balances education with such breathtaking green landscapes. Thank you for the hospitality!',
        'initial' => 'C'
    ],
    [
        'name' => 'Sarah L., Wellness Retreat Guest',
        'quote' => 'The quiet mornings by the Tanguar Haor wetlands, the songs of birds, and the mesmerizing view of the sunset filled my soul with peace. The traditional mud house stay and home-cooked meals were simple yet perfect.',
        'initial' => 'S'
    ],
    [
        'name' => 'David K., Photographer',
        'quote' => 'The greenery in rural Bangladesh is stunning. The walking paths that touch the rivers and hills, the fresh air – it was exactly what I needed to relax. The local guides were very welcoming and helpful.',
        'initial' => 'D'
    ],
    [
        'name' => 'Nirjhara Khisa, Local Visitor',
        'quote' => 'Visiting the agricultural park was a delightful experience. The vibrant array of colorful flowers and trees created a peaceful environment where one could just sit, chat, and enjoy nature.',
        'initial' => 'N'
    ],
    [
        'name' => 'Mohammed A., Student Group Leader',
        'quote' => 'The educational aspect of the farm stay was fantastic. The team made everything fun and educational. Our students left with a greater appreciation for agriculture and the hard work of farmers.',
        'initial' => 'M'
    ],
    [
        'name' => 'Pavel M., Weekend Explorer',
        'quote' => 'The hospitality of the village people is heartwarming. They greet you with a sweet smile and make you feel so close that you never feel you are a stranger. The fresh handmade food will be remembered for the rest of my life!',
        'initial' => 'P'
    ],
    [
        'name' => 'Rashid B., Nature Lover',
        'quote' => 'I stayed at an eco-cottage in the Cox\'s Bazar hilly forest area. It was very natural and charming. The ability to do boating and fishing in the lake made it a perfect amusement spot for the whole family.',
        'initial' => 'R'
    ],
    [
        'name' => 'Tapti Chakma, Traveler',
        'quote' => 'The area has a unique charm, blending local culture with beautiful natural scenery. It’s a must-do for those who want a flavor of rural life, away from the concrete jungle of the city, and need a space for fresh air.',
        'initial' => 'T'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traveler Experiences | Agro-Tourism</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* --- GLOBAL RESET --- */
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif; }
        body { background: #f8f9fa; color: #333; }

        /* --- NAVBAR STYLES --- */
        .top-bar { background:#f5f5f5; padding:8px 50px; font-size:14px; display:flex; justify-content:space-between; }
        nav { display:flex; justify-content:space-between; align-items:center; padding:15px 50px; background:white; border-bottom:1px solid #eee; position: sticky; top:0; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .logo { font-size:24px; font-weight:700; color:#2e7d32; text-decoration: none;}
        .nav-links a { margin:0 15px; text-decoration:none; color:#333; font-weight: 500; transition: color 0.3s; }
        .nav-links a:hover, .nav-links a.active { color:#2e7d32; }
        .back-btn { background: #2e7d32; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; }

        /* --- HERO HEADER --- */
        .page-header {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('updates/hero2.jpg'); /* Ensure this image exists or change path */
            background-size: cover;
            background-position: center;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            margin-bottom: 50px;
        }
        .page-header h1 { font-size: 42px; font-weight: 700; margin-bottom: 10px; }
        .page-header p { font-size: 18px; opacity: 0.9; }

        /* --- TESTIMONIALS GRID --- */
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px 60px 20px; }
        
        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }
        .section-title h2 { color: #2e7d32; font-size: 32px; margin-bottom: 10px; }
        .section-title p { color: #666; max-width: 600px; margin: 0 auto; }

        .review-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); /* Responsive Grid */
            gap: 30px;
        }

        /* --- CARD DESIGN --- */
        .review-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            border-top: 4px solid #2e7d32;
        }

        .review-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(46, 125, 50, 0.15);
        }

        /* Quote Icon Background */
        .review-card::before {
            content: "“";
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 80px;
            color: #e8f5e9;
            font-family: serif;
            line-height: 1;
        }

        .review-text {
            font-size: 15px;
            line-height: 1.7;
            color: #555;
            margin-bottom: 25px;
            position: relative;
            z-index: 1;
            font-style: italic;
        }

        .reviewer-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* Generated Avatar Circle */
        .avatar-circle {
            width: 50px;
            height: 50px;
            background: #2e7d32;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 20px;
            flex-shrink: 0;
        }

        .reviewer-details h4 {
            color: #333;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .reviewer-details span {
            color: #888;
            font-size: 13px;
            font-weight: 400;
        }

        /* Responsive Mobile */
        @media(max-width: 768px) {
            .nav-links { display: none; } 
            .page-header h1 { font-size: 32px; }
            .review-grid { grid-template-columns: 1fr; }
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
            <a href="index.php">Home</a>
            <a href="destinations.php">Destinations</a>
            <a href="experience.php" class="active">Experiences</a>
            <a href="tourist_login.php">Plan Your Visit</a>
            <a href="articles.php">Articles</a>
        </div>
        <a href="index.php" class="back-btn">Back Home</a>
    </nav>

    <header class="page-header">
        <div>
            <h1>Traveler Stories</h1>
            <p>Discover real experiences from people who explored rural Bangladesh</p>
        </div>
    </header>

    <div class="container">
        
        <div class="section-title">
            <h2>What People Say</h2>
            <p>From tea gardens to river deltas, hear how agro-tourism has touched the hearts of travelers from around the country.</p>
        </div>

        <div class="review-grid">
            <?php foreach($reviews as $review): ?>
            <div class="review-card">
                <p class="review-text">"<?= $review['quote'] ?>"</p>
                
                <div class="reviewer-info">
                    <div class="avatar-circle">
                        <?= $review['initial'] ?>
                    </div>
                    <div class="reviewer-details">
                        <h4><?= explode(',', $review['name'])[0] ?></h4>
                        <span><?= strstr($review['name'], ',') ? trim(substr(strstr($review['name'], ','), 1)) : 'Visitor' ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>

</body>
</html>