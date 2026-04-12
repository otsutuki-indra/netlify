<?php
require_once 'config.php';
require_login();

$user = get_current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Hellcorp</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Share+Tech+Mono&family=Rajdhani:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --hell-red: #FF0022;
            --void: #0A0A0A;
            --white: #F0EDE8;
            --dim: #888888;
            --glow: rgba(255,0,34,0.6);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--void) 0%, #1a1a1a 50%, var(--void) 100%);
            color: var(--white);
            font-family: 'Rajdhani', sans-serif;
            min-height: 100vh;
        }

        header {
            background: rgba(10, 10, 10, 0.95);
            border-bottom: 1px solid rgba(255, 0, 34, 0.2);
            padding: 20px 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(10px);
        }

        .logo {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.8rem;
            letter-spacing: 0.15em;
            color: var(--hell-red);
            text-shadow: 0 0 20px var(--glow);
        }

        .logo span {
            color: var(--white);
        }

        .nav {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .nav a {
            color: var(--dim);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .nav a:hover {
            color: var(--hell-red);
        }

        .btn-logout {
            padding: 10px 20px;
            background: var(--hell-red);
            color: var(--void);
            border: none;
            border-radius: 4px;
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.75rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-logout:hover {
            box-shadow: 0 0 20px var(--glow);
            transform: translateY(-2px);
        }

        .container {
            max-width: 1000px;
            margin: 60px auto;
            padding: 0 20px;
        }

        .welcome-box {
            background: rgba(20, 20, 20, 0.95);
            border: 1px solid rgba(255, 0, 34, 0.2);
            border-radius: 12px;
            padding: 50px 40px;
            margin-bottom: 40px;
            box-shadow: 0 20px 60px rgba(255, 0, 34, 0.1);
        }

        h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 3rem;
            color: var(--white);
            margin-bottom: 10px;
        }

        .username {
            color: var(--hell-red);
            font-weight: bold;
        }

        .subtitle {
            color: var(--dim);
            font-size: 1.1rem;
            margin-top: 10px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }

        .card {
            background: rgba(20, 20, 20, 0.95);
            border: 1px solid rgba(255, 0, 34, 0.2);
            border-radius: 12px;
            padding: 30px;
            transition: all 0.3s;
        }

        .card:hover {
            border-color: var(--hell-red);
            box-shadow: 0 0 20px rgba(255, 0, 34, 0.2);
        }

        .card h3 {
            color: var(--hell-red);
            margin-bottom: 15px;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.5rem;
        }

        .card p {
            color: var(--dim);
            line-height: 1.6;
        }

        .profile-info {
            background: rgba(20, 20, 20, 0.95);
            border: 1px solid rgba(255, 0, 34, 0.2);
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 40px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255, 0, 34, 0.1);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: var(--hell-red);
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        .info-value {
            color: var(--white);
            text-align: right;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            background: rgba(52, 199, 89, 0.2);
            border: 1px solid #34C759;
            color: #34C759;
            border-radius: 20px;
            font-size: 0.8rem;
            font-family: 'Share Tech Mono', monospace;
        }

        .footer-text {
            text-align: center;
            color: var(--dim);
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 0, 34, 0.1);
        }

        @media (max-width: 768px) {
            header {
                padding: 15px 20px;
                flex-direction: column;
                gap: 15px;
            }

            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">HELL<span>CORP</span></div>
    <nav class="nav">
        <a href="../index.html">Home</a>
        <a href="../index.html#services">Services</a>
        <a href="profile.php">Profile Settings</a>
        <form method="GET" action="logout.php" style="margin: 0;">
            <button type="submit" class="btn-logout">LOGOUT</button>
        </form>
    </nav>
</header>

<div class="container">
    <div class="welcome-box">
        <h1>Welcome back, <span class="username"><?php echo htmlspecialchars($user['username']); ?></span>! 🔥</h1>
        <p class="subtitle">You are logged into the Hellcorp digital warfare division</p>
    </div>

    <div class="profile-info">
        <div class="info-row">
            <span class="info-label">// Status</span>
            <span class="info-value"><span class="status-badge">ACTIVE AGENT</span></span>
        </div>
        <div class="info-row">
            <span class="info-label">// Username</span>
            <span class="info-value"><?php echo htmlspecialchars($user['username']); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">// Email</span>
            <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">// Member Since</span>
            <span class="info-value"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">// Last Login</span>
            <span class="info-value">
                <?php 
                if ($user['last_login']) {
                    echo date('M d, Y H:i', strtotime($user['last_login']));
                } else {
                    echo 'This is your first login';
                }
                ?>
            </span>
        </div>
    </div>

    <div class="grid">
        <div class="card">
            <h3>🔥 Welcome</h3>
            <p>You've successfully joined the Hellcorp network. Complete your profile and explore our services to unlock exclusive features and opportunities.</p>
        </div>

        <div class="card">
            <h3>⚡ Quick Links</h3>
            <p>
                <a href="profile.php" style="color: var(--hell-red); text-decoration: none;">Edit Profile</a> •
                <a href="#" style="color: var(--hell-red); text-decoration: none;">Settings</a> •
                <a href="logout.php" style="color: var(--hell-red); text-decoration: none;">Logout</a>
            </p>
        </div>

        <div class="card">
            <h3>🎯 Next Steps</h3>
            <p>Explore our AI engineering, cybersecurity, and blockchain services. Choose what resonates with your vision and let's build something extraordinary.</p>
        </div>
    </div>

    <div class="footer-text">
        <p>© 2026 HELLCORP INC. WE BUILD WHAT OTHERS FEAR.</p>
    </div>
</div>

</body>
</html>