<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Count incidents
$incidentCount = 0;
if (file_exists("incidents.csv")) {
    $rows = file("incidents.csv", FILE_IGNORE_NEW_LINES);
    $incidentCount = count($rows) - 1; // subtract header
    if ($incidentCount < 0) $incidentCount = 0;
}
?>
<p>Total Incidents Recorded: <?php echo $totalIncidents; ?></p>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - IncidentGuard</title>
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
        /* ===== NAVBAR ===== */
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
        .navbar .user-info .logout-btn {
            background: #e74c3c;
        }
        .navbar .user-info .logout-btn:hover {
            background: #c0392b;
        }
        /* ===== CONTAINER ===== */
        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 0 20px;
        }
        /* ===== WELCOME CARD ===== */
        .welcome-card {
            background: white;
            border-radius: 16px;
            padding: 40px 45px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 35px;
            text-align: center;
        }
        .welcome-card h2 {
            font-size: 28px;
            color: #1a1a2e;
        }
        .welcome-card h2 span {
            color: #00b4d8;
        }
        .welcome-card p {
            color: #666;
            font-size: 16px;
            margin-top: 8px;
        }
        .welcome-card .stats {
            margin-top: 20px;
            display: inline-block;
            background: #f0f2f5;
            padding: 10px 30px;
            border-radius: 30px;
            font-size: 16px;
        }
        .welcome-card .stats strong {
            font-size: 22px;
            color: #1a1a2e;
        }
        /* ===== MENU GRID ===== */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }
        .menu-card {
            background: white;
            border-radius: 16px;
            padding: 35px 30px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            text-decoration: none;
            color: #1a1a2e;
            border: 2px solid transparent;
        }
        .menu-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
            border-color: #00b4d8;
        }
        .menu-card .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .menu-card h3 {
            font-size: 18px;
            margin-bottom: 8px;
        }
        .menu-card p {
            color: #888;
            font-size: 14px;
        }
        /* ===== RESPONSIVE ===== */
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
            .welcome-card {
                padding: 25px 20px;
            }
            .welcome-card h2 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <!-- ===== NAVBAR ===== -->
    <nav class="navbar">
        <div class="brand">🛡️ <span>Incident</span>Guard</div>
        <div class="user-info">
            <span class="username">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="logout-btn">🚪 Logout</a>
        </div>
    </nav>

    <!-- ===== MAIN CONTENT ===== -->
    <div class="container">
        <div class="welcome-card">
            <h2>Welcome, <span><?php echo htmlspecialchars($_SESSION['username']); ?></span> 👋</h2>
            <p>Manage and track security incidents efficiently</p>
            <div class="stats">
                📊 Total Incidents: <strong><?php echo $incidentCount; ?></strong>
            </div>
        </div>

        <div class="menu-grid">
            <a href="add_incident.php" class="menu-card">
                <div class="icon">📝</div>
                <h3>Record Incident</h3>
                <p>Report a new security incident</p>
            </a>
            <a href="view_incidents.php" class="menu-card">
                <div class="icon">📋</div>
                <h3>View Incidents</h3>
                <p>See all reported incidents</p>
            </a>
            <a href="search_incident.php" class="menu-card">
                <div class="icon">🔍</div>
                <h3>Search Incident</h3>
                <p>Find incidents by ID</p>
            </a>
        </div>
    </div>
</body>
</html>