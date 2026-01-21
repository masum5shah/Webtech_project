<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Agro-Tourism | Home</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* --- RESET & BASICS --- */
    *{ margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif; }
    body{ background:#fff; }

    /* --- TOP BAR --- */
    .top-bar{ background:#f5f5f5; padding:8px 50px; font-size:14px; display:flex; justify-content:space-between; }

    /* --- NAVBAR --- */
    nav{
        display:flex;
        justify-content:space-between;
        align-items:center;
        padding:15px 50px;
        border-bottom:1px solid #eee;
        background: white;
        position: sticky; /* Keeps navbar at top */
        top: 0;
        z-index: 1000;
    }

    .logo{ font-size:24px; font-weight:700; color:#2e7d32; text-decoration: none;}

    .nav-links a{
        margin:0 15px;
        text-decoration:none;
        color:#333;
        font-weight: 500;
        transition: color 0.3s;
    }

    .nav-links a:hover{ color: #2e7d32; }

    /* --- LOGIN DROPDOWN --- */
    .login-dropdown{ position:relative; }
    .login-btn{ cursor:pointer; font-weight:600; }
    .dropdown-menu{
        position:absolute; right:0; top:35px; background:#fff; width:180px;
        border-radius:8px; box-shadow:0 10px 25px rgba(0,0,0,0.2);
        display:none; z-index:1000;
    }
    .dropdown-menu a{ display:block; padding:12px 16px; text-decoration:none; color:#333; }
    .dropdown-menu a:hover{ background:#e8f5e9; color:#2e7d32; }
    .dropdown-menu.show{ display:block; }

    /* --- HERO SECTION --- */
    .hero{
        position:relative; height:75vh;
        background-image:url('updates/hero1.jpg'); /* Default Image */
        background-size:cover; background-position:center;
        transition:background-image 1s ease-in-out;
    }
    .hero::after{ content:""; position:absolute; inset:0; background:rgba(0,0,0,0.35); }
    .hero-content{ position:absolute; left:60px; bottom:80px; color:#fff; max-width:600px; z-index: 2; }
    .hero-content h1{ font-size:42px; line-height:1.3; }
    .hero-content p{ margin-top:15px; font-size:18px; }

    /* --- SECTIONS --- */
    .notice{ background:#e8f5e9; padding:15px; text-align:center; font-weight:500; }
    .highlights{ padding:60px; }
    .highlights h2{ font-size:32px; margin-bottom:30px; }
    .highlight-grid{ display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:25px; }
    .highlight-card{ border-radius:12px; overflow:hidden; box-shadow:0 10px 25px rgba(0,0,0,0.15); transition:transform 0.3s; background:#fff; }
    .highlight-card:hover{ transform:translateY(-6px); }
    .highlight-card img{ width:100%; height:200px; object-fit:cover; }
    .highlight-card h3{ padding:15px; font-size:18px; }

    /* --- CALL TO ACTION (CONTACT AGENT) --- */
    .cta-section {
        background: #2e7d32;
        color: white;
        text-align: center;
        padding: 60px 20px;
        margin-top: 40px;
    }
    .cta-section h2 { font-size: 32px; margin-bottom: 15px; }
    .cta-section p { font-size: 18px; margin-bottom: 30px; opacity: 0.9; }
    .btn-contact {
        background: white;
        color: #2e7d32;
        padding: 12px 30px;
        text-decoration: none;
        font-weight: 700;
        border-radius: 50px;
        transition: 0.3s;
        display: inline-block;
    }
    .btn-contact:hover { background: #e8f5e9; transform: scale(1.05); }

    /* --- RESPONSIVE --- */
    @media(max-width:768px){
        nav{ flex-wrap:wrap; gap:10px; justify-content: center; }
        .hero-content{ left:20px; right:20px; bottom:40px; }
        .hero-content h1{ font-size:30px; }
    }
</style>
</head>

<body>

    <div class="top-bar">
        <div>Media | Events | Safe Travel</div>
        <div>🌐 English</div>
    </div>

    <nav>
        <a href="index.php" class="logo">Agro-Tourism</a>

        <div class="nav-links">
            <a href="destinations.php">Destinations</a>
            <a href="experience.php">Experiences</a>
            <a href="tourist_login.php">Plan Your Visit</a>
            <a href="articles.php">Articles</a>
            <a href="contact_with_agent.php" style="color:#2e7d32; font-weight:600;">Contact Agents</a>
        </div>

        <div class="login-dropdown">
            <span class="login-btn" id="loginBtn">Login ▾</span>
            <div class="dropdown-menu" id="loginMenu">
                <a href="tourist_login.php">🧳 Tourist</a>
                <a href="farmer_login.php">👨‍🌾 Farmer</a>
                <a href="agentlogin.php">🧑‍💼 Agent</a>
                <a href="admin_login.php">🧑 Admin</a>
            </div>
        </div>
    </nav>

    <section class="hero" id="hero">
        <div class="hero-content">
            <h1>Unforgettable Farm Experiences</h1>
            <p>Explore nature, agriculture, and rural tourism</p>
        </div>
    </section>

    <div class="notice">
        NOTICE: Please follow sustainable and eco-friendly travel guidelines 🌱
    </div>

    <section class="highlights">
        <h2>Travel Highlights</h2>
        <div class="highlight-grid">
            <div class="highlight-card">
                <img src="updates/farm1.jpg" alt="Organic Farm">
                <h3>Organic Farms</h3>
            </div>
            <div class="highlight-card">
                <img src="updates/farm2.jpg" alt="Homestay">
                <h3>Village Homestays</h3>
            </div>
            <div class="highlight-card">
                <img src="updates/farm3.jpg" alt="Harvest">
                <h3>Harvest Festivals</h3>
            </div>
            <div class="highlight-card">
                <img src="updates/farm4.jpg" alt="Nature Trail">
                <h3>Nature Trails</h3>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <h2>Need Help Planning Your Trip?</h2>
        <p>Our certified agents are ready to assist you with bookings and guidance.</p>
        <a href="contact_with_agent.php" class="btn-contact">Contact an Agent</a>
    </section>

    <script>
    /* ===== LOGIN CLICK TOGGLE ===== */
    const loginBtn = document.getElementById("loginBtn");
    const loginMenu = document.getElementById("loginMenu");

    loginBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        loginMenu.classList.toggle("show");
    });

    document.addEventListener("click", () => {
        loginMenu.classList.remove("show");
    });

    /* ===== HERO IMAGE SLIDER ===== */
    const hero = document.getElementById("hero");
    const images = [ "updates/hero1.jpg", "updates/hero2.jpg", "updates/hero3.jpg" ];
    let index = 0;

    function changeImage(){
        hero.style.backgroundImage = `url(${images[index]})`;
        index = (index + 1) % images.length;
    }
    
    // Start slider
    setInterval(changeImage, 3000);
    </script>

</body>
</html>