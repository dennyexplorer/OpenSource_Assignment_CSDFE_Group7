<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$result = null;
$searched = false;
$notFound = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $searched = true;
    $searchId = trim($_POST["incident_id"]);

    if (file_exists("incidents.csv")) {
        $rows = array_map('str_getcsv', file("incidents.csv"));
        foreach ($rows as $i => $fields) {
            if ($i === 0) continue;
            if (isset($fields[0]) && $fields[0] == $searchId) {
                $result = $fields;
                break;
            }
        }
    }
    if (!$result) {
        $notFound = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Incident - IncidentGuard</title>
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
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .form-group input {
            flex: 1;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s, box-shadow 0.3s;
            background: #f8f9fa;
            min-width: 180px;
        }
        .form-group input:focus {
            border-color: #00b4d8;
            outline: none;
            box-shadow: 0 0 0 4px rgba(0, 180, 216, 0.15);
            background: #fff;
        }
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
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
        /* ===== RESULT CARD ===== */
        .result-card {
            margin-top: 25px;
            border-radius: 12px;
            padding: 25px 30px;
            border-left: 5px solid #00b4d8;
            background: #f8f9fa;
        }
        .result-card h3 {
            color: #1a1a2e;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .result-item {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .result-item:last-child {
            border-bottom: none;
        }
        .result-item .label {
            font-weight: 600;
            color: #555;
            width: 120px;
            flex-shrink: 0;
        }
        .result-item .value {
            color: #1a1a2e;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        .status-open {
            background: #f8d7da;
            color: #721c24;
        }
        .status-investigating {
            background: #fff3cd;
            color: #856404;
        }
        .status-resolved {
            background: #d4edda;
            color: #155724;
        }
        .not-found {
            margin-top: 25px;
            padding: 25px;
            text-align: center;
            background: #f8d7da;
            border-radius: 12px;
            color: #721c24;
        }
        .not-found .icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .btn-row {
            margin-top: 25px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
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
            .form-group {
                flex-direction: column;
            }
            .result-item {
                flex-direction: column;
                padding: 10px 0;
            }
            .result-item .label {
                width: 100%;
                margin-bottom: 2px;
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
            <h2>🔍 Search Incident</h2>
            <p class="subtitle">Enter the Incident ID to retrieve details</p>

            <form method="post">
                <div class="form-group">
                    <input type="text" name="incident_id" placeholder="Enter Incident ID (e.g., 1, 2, 3...)" required>
                    <button type="submit" class="btn btn-primary">🔎 Search</button>
                </div>
            </form>

            <?php if ($searched): ?>
                <?php if ($result): ?>
                    <div class="result-card">
                        <h3>📄 Incident Details</h3>
                        <div class="result-item">
                            <span class="label">Incident ID</span>
                            <span class="value"><strong>#<?php echo htmlspecialchars($result[0]); ?></strong></span>
                        </div>
                        <div class="result-item">
                            <span class="label">Title</span>
                            <span class="value"><?php echo htmlspecialchars($result[1]); ?></span>
                        </div>
                        <div class="result-item">
                            <span class="label">Description</span>
                            <span class="value"><?php echo htmlspecialchars($result[2]); ?></span>
                        </div>
                        <div class="result-item">
                            <span class="label">Date Reported</span>
                            <span class="value"><?php echo htmlspecialchars($result[3]); ?></span>
                        </div>
                        <div class="result-item">
                            <span class="label">Status</span>
                            <span class="value">
                                <?php
                                $status = $result[4] ?? 'Open';
                                $class = '';
                                if ($status === 'Open') $class = 'status-open';
                                elseif ($status === 'Investigating') $class = 'status-investigating';
                                elseif ($status === 'Resolved') $class = 'status-resolved';
                                ?>
                                <span class="status-badge <?php echo $class; ?>"><?php echo htmlspecialchars($status); ?></span>
                            </span>
                        </div>
                    </div>
                <?php elseif ($notFound): ?>
                    <div class="not-found">
                        <div class="icon">🔍</div>
                        <h3>Incident Not Found</h3>
                        <p>No incident exists with ID: <strong><?php echo htmlspecialchars($_POST['incident_id']); ?></strong></p>
                        <p style="margin-top:8px; font-size:14px;">Please check the ID and try again.</p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="btn-row">
                <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>