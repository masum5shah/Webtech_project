<!DOCTYPE html>
<html>
<head>
  <title>Welcome | Agro-Tourism</title>
  <style>
    /* Reset default browser styles */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    /* Blue gradient background */
    body {
      font-family: Arial, Helvetica, sans-serif;
      background: linear-gradient(135deg, #6dd5ed, #2193b0); /* Light blue to deep blue */
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    /* Main container */
    .container {
      background: #ffffff;
      padding: 40px;
      width: 380px;
      text-align: center;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    /* Title */
    .container h1 {
      color: #2c3e50;
      margin-bottom: 10px;
    }

    /* Subtitle */
    .container p {
      color: #555;
      margin-bottom: 25px;
    }

    /* Role buttons */
    .role-box a {
      display: block;
      text-decoration: none;
      background: #2193b0; /* deep blue button */
      color: #fff;
      padding: 12px;
      border-radius: 6px;
      font-size: 18px;
      font-weight: bold;
      transition: background 0.3s, transform 0.2s;
      margin-bottom: 10px;
    }

    /* Hover effect */
    .role-box a:hover {
      background: #176b87; /* darker blue on hover */
      transform: translateY(-2px);
    }

    /* Responsive */
    @media (max-width: 480px) {
      .container {
        width: 90%;
      }
    }
  </style>
</head>
<body>

  <div class="container">
    <h1>Welcome to Agro-Tourism</h1>
    <p>Please select who you are:</p>

    <div class="role-box">
      <a href="farmer_login.php">👨‍🌾 Farmer</a>
      <a href="touristlogin.php">🧳 Tourist</a>
      <a href="adminlogin.php">🛡️ Admin</a>
    </div>
  </div>

</body>
</html>
