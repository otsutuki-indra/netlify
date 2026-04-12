<?php
require_once 'config.php';
require_guest();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = "Security token invalid!";
    } else {
        $username = sanitize($_POST['username'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // Validation
        if (empty($username) || strlen($username) < 3) {
            $errors[] = "Username must be at least 3 characters";
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Valid email required";
        }
        if (empty($password) || strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters";
        }
        if ($password !== $password_confirm) {
            $errors[] = "Passwords do not match";
        }

        // Check if user exists
        if (empty($errors)) {
            $check_query = "SELECT id FROM users WHERE email = '$email' OR username = '$username'";
            $check_result = $conn->query($check_query);
            
            if ($check_result->num_rows > 0) {
                $errors[] = "User already exists with that email or username";
            }
        }

        // Register user
        if (empty($errors)) {
            $hashed_password = hash_password($password);
            $created_at = date('Y-m-d H:i:s');
            
            $insert_query = "INSERT INTO users (username, email, password, created_at) 
                           VALUES ('$username', '$email', '$hashed_password', '$created_at')";
            
            if ($conn->query($insert_query) === TRUE) {
                $success = true;
                // Clear form
                $username = $email = '';
            } else {
                $errors[] = "Registration failed: " . $conn->error;
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
    <title>Register - Hellcorp</title>
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
            max-width: 500px;
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
        }

        .alert-error {
            background: rgba(255, 0, 34, 0.1);
            border-color: var(--hell-red);
            color: #ff6b6b;
        }

        .alert-success {
            background: rgba(52, 199, 89, 0.1);
            border-color: #34C759;
            color: #34C759;
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

        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: var(--dim);
        }

        .login-link a {
            color: var(--hell-red);
            text-decoration: none;
            transition: all 0.3s;
        }

        .login-link a:hover {
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

        .success-message {
            text-align: center;
            padding: 30px;
        }

        .success-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #34C759;
        }

        .success-text {
            font-size: 1.1rem;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .btn-success {
            display: inline-block;
            padding: 12px 30px;
            background: var(--hell-red);
            color: var(--void);
            border-radius: 6px;
            text-decoration: none;
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.75rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            font-weight: 700;
            transition: all 0.3s;
        }

        .btn-success:hover {
            box-shadow: 0 0 30px var(--glow);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<div class="container">
    <a href="../index.html" class="back-link">← Back to Home</a>

    <div class="auth-box">
        <?php if ($success): ?>
            <div class="success-message">
                <div class="success-icon">✓</div>
                <div class="success-text">
                    <h2 style="margin-bottom: 15px;">Registration Successful!</h2>
                    <p>Your account has been created. You can now login.</p>
                </div>
                <a href="login.php" class="btn-success">Go to Login</a>
            </div>
        <?php else: ?>
            <div class="logo">HELL<span>CORP</span></div>
            <h1>CREATE ACCOUNT</h1>
            <p class="subtitle">Join the digital warfare division</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li>⚠️ <?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Choose your callsign" 
                           value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="your@email.com" 
                           value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Min 8 characters" required>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirm Password</label>
                    <input type="password" id="password_confirm" name="password_confirm" placeholder="Repeat password" required>
                </div>

                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <button type="submit" class="btn-submit">CREATE ACCOUNT</button>
            </form>

            <p class="login-link">
                Already have an account? <a href="login.php">Login here</a>
            </p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>