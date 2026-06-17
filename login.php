<?php
session_start();
$error = "";
$registered = isset($_GET['registered']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $found = false;
    if (file_exists("users.csv")) {
        $rows = file("users.csv", FILE_IGNORE_NEW_LINES);
        foreach ($rows as $i => $row) {
            if ($i === 0) continue;
            $fields = str_getcsv($row);
            if (isset($fields[1], $fields[2]) && $fields[1] === $username) {
                if (password_verify($password, $fields[2])) {
                    $found = true;
                    $_SESSION['username'] = $username;
                }
                break;
            }
        }
    }

    if ($found) {
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IncidentGuard</title>
    <style>
        /* ===== RESET & BASE ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        /* ===== LOGIN CARD ===== */
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            padding: 50px 45px;
            max-width: 420px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.6s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .login-container .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-container .logo h1 {
            font-size: 28px;
            color: #1a1a2e;
            letter-spacing: 1px;
        }
        .login-container .logo h1 span {
            color: #00b4d8;
        }
        .login-container .logo p {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        .login-container h2 {
            color: #1a1a2e;
            font-size: 22px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 600;
        }
        /* ===== FORM ELEMENTS ===== */
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s, box-shadow 0.3s;
            background: #f8f9fa;
        }
        .form-group input:focus {
            border-color: #00b4d8;
            outline: none;
            box-shadow: 0 0 0 4px rgba(0, 180, 216, 0.15);
            background: #fff;
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #1a1a2e, #302b63);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(26, 26, 46, 0.3);
        }
        .btn:active {
            transform: translateY(0);
        }
        /* ===== MESSAGES ===== */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        /* ===== FOOTER ===== */
        .footer-text {
            text-align: center;
            margin-top: 22px;
            font-size: 14px;
            color: #666;
        }
        .footer-text a {
            color: #00b4d8;
            text-decoration: none;
            font-weight: 600;
        }
        .footer-text a:hover {
            text-decoration: underline;
        }
        .demo-info {
            margin-top: 20px;
            padding: 12px 16px;
            background: #f0f2f5;
            border-radius: 8px;
            font-size: 13px;
            color: #555;
            text-align: center;
            border: 1px dashed #ccc;
        }
        .demo-info strong {
            color: #1a1a2e;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>🛡️ <span>Incident</span>Guard</h1>
            <p>Security Incident Reporting System</p>
        </div>
        <h2>Welcome Back</h2>

        <?php if ($registered): ?>
            <div class="alert alert-success">✅ Registration successful! Please log in.</div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">❌ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn">🔐 Login</button>
        </form>

        <p class="footer-text">Don't have an account? <a href="register.php">Register here</a></p>

        <div class="demo-info">
            <strong>Demo Credentials:</strong> admin / admin123
        </div>
    </div>
</body>
</html>