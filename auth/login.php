<?php
require_once 'config.php';
require_guest();

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = "Security token invalid!";
    } else {
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $errors[] = "Email and password required";
        } else {
            // Query user
            $query = "SELECT id, username, email, password FROM users WHERE email = '$email'";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                
                if (verify_password($password, $user['password'])) {
                    // Login successful
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    
                    // Update last login
                    $update_query = "UPDATE users SET last_login = NOW() WHERE id = " . $user['id'];
                    $conn->query($update_query);
                    
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $errors[] = "Invalid email or password";
                }
            } else {
                $errors[] = "Invalid email or password";
            }
        }
    }
}

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hellcorp</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        .container {
            max-width: 450px;
            width: 90%;
        }

        .auth-box {
            background: rgba(20, 20, 20, 0.95);
            border: 1px solid rgba(255, 0, 34, 0.2);
            border-radius: 12px;
            padding: 50px 40px;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 60px rgba(255, 0, 34, 0.1);
        }

        .logo {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2rem;
            letter-spacing: 0.15em;
            color: var(--hell-red);
            text-shadow: 0 0 30px var(--glow);
            text-align: center;
            margin-bottom: 40px;
        }

        .logo span {
            color: var(--white);
        }

        h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2rem;
            margin-bottom: 10px;
            text-align: center;
        }

        .subtitle {
            text-align: center;
            color: var(--dim);
            margin-bottom: 30px;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.75rem;
            letter-spacing: 0.2em;
            color: var(--dim);
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 0, 34, 0.2);
            border-radius: 6px;
            color: var(--white);
            font-family: 'Rajdhani', sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        input:focus {
            outline: none;
            border-color: var(--hell-red);
            background: rgba(255, 0, 34, 0.05);
            box-shadow: 0 0 20px rgba(255, 0, 34, 0.2);
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .alert {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            border-left: 3px solid;
            background: rgba(255, 0, 34, 0.1);
            border-color: var(--hell-red);
            color: #ff6b6b;
        }

        .alert ul {
            list-style: none;
            padding: 0;
        }

        .alert li {
            margin-bottom: 5px;
        }

        .alert li:last-child {
            margin-bottom: 0;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: var(--hell-red);
            color: var(--void);
            border: none;
            border-radius: 6px;
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.75rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            box-shadow: 0 0 30px var(--glow);
            transform: translateY(-2px);
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: var(--dim);
        }

        .register-link a {
            color: var(--hell-red);
            text-decoration: none;
            transition: all 0.3s;
        }

        .register-link a:hover {
            text-shadow: 0 0 10px var(--glow);
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--dim);
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s;
        }

        .back-link:hover {
            color: var(--hell-red);
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin: 20px 0;
        }

        .remember-me input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            cursor: pointer;
        }

        .remember-me label {
            margin: 0;
            color: var(--dim);
            font-size: 0.9rem;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="../index.html" class="back-link">← Back to Home</a>

    <div class="auth-box">
        <div class="logo">HELL<span>CORP</span></div>
        <h1>LOGIN</h1>
        <p class="subtitle">Access your warrior profile</p>

        <?php if (!empty($errors)): ?>
            <div class="alert">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li>⚠️ <?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="your@email.com" 
                       value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember" value="1">
                <label for="remember">Remember me</label>
            </div>

            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <button type="submit" class="btn-submit">LOGIN</button>
        </form>

        <p class="register-link">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
    </div>
</div>

</body>
</html>