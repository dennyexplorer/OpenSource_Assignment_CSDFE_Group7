<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $status = trim($_POST["status"]);

    if (empty($title) || empty($description)) {
        $error = "Title and description are required.";
    } else {
        $newId = 1;
        if (file_exists("incidents.csv")) {
            $rows = file("incidents.csv", FILE_IGNORE_NEW_LINES);
            $newId = count($rows);
        }

        $date = date("Y-m-d H:i:s");
        $row = [$newId, $title, $description, $date, $status];
        $fp = fopen("incidents.csv", "a");
        fputcsv($fp, $row);
        fclose($fp);

        $message = "✅ Incident #$newId recorded successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Incident - IncidentGuard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
        }
        .navbar {
            background: linear-gradient(135deg, #1a1a2e, #302b63);
            color: white;
            padding: 18px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        .navbar .brand {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .navbar .brand span {
            color: #00b4d8;
        }
        .navbar .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
            font-size: 15px;
        }
        .navbar .user-info .username {
            background: rgba(255, 255, 255, 0.15);
            padding: 8px 18px;
            border-radius: 20px;
        }
        .navbar .user-info a {
            color: white;
            text-decoration: none;
            padding: 8px 20px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.1);
            transition: background 0.3s;
            font-weight: 500;
        }
        .navbar .user-info a:hover {
            background: rgba(255, 255, 255, 0.25);
        }
        .container {
            max-width: 700px;
            margin: 50px auto;
            padding: 0 20px;
        }
        .card {
            background: white;
            border-radius: 16px;
            padding: 40px 45px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        .card h2 {
            font-size: 26px;
            color: #1a1a2e;
            margin-bottom: 8px;
        }
        .card .subtitle {
            color: #888;
            font-size: 15px;
            margin-bottom: 25px;
        }
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
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s, box-shadow 0.3s;
            background: #f8f9fa;
            font-family: inherit;
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: #00b4d8;
            outline: none;
            box-shadow: 0 0 0 4px rgba(0, 180, 216, 0.15);
            background: #fff;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        .form-group select {
            appearance: auto;
            cursor: pointer;
        }
        .btn-row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 5px;
        }
        .btn {
            padding: 13px 35px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: linear-gradient(135deg, #1a1a2e, #302b63);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(26, 26, 46, 0.3);
        }
        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }
        .btn-secondary:hover {
            background: #d0d0d0;
        }
        .alert-success {
            padding: 14px 18px;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 15px;
            font-weight: 500;
        }
        .alert-error {
            padding: 14px 18px;
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 15px;
            font-weight: 500;
        }
        @media (max-width: 600px) {
            .navbar {
                padding: 15px 20px;
                flex-direction: column;
                gap: 12px;
                text-align: center;
            }
            .navbar .user-info {
                flex-wrap: wrap;
                justify-content: center;
            }
            .card {
                padding: 25px 20px;
            }
            .btn-row {
                flex-direction: column;
            }
            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="brand">🛡️ <span>Incident</span>Guard</div>
        <div class="user-info">
            <span class="username">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="dashboard.php">🏠 Dashboard</a>
            <a href="logout.php" style="background:#e74c3c;">🚪 Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <h2>📝 Report Security Incident</h2>
            <p class="subtitle">Fill in the details of the security incident</p>

            <?php if ($message): ?>
                <div class="alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert-error">❌ <?php echo $error; ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <label for="title">Incident Title</label>
                    <input type="text" id="title" name="title" placeholder="e.g., Unauthorized Access Detected" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Provide detailed description of the incident..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="Open">🔴 Open</option>
                        <option value="Investigating">🟡 Investigating</option>
                        <option value="Resolved">🟢 Resolved</option>
                    </select>
                </div>

                <div class="btn-row">
                    <button type="submit" class="btn btn-primary">📤 Submit Incident</button>
                    <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>