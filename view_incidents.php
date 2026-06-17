<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Incidents - IncidentGuard</title>
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
            max-width: 1000px;
            margin: 50px auto;
            padding: 0 20px;
        }
        .card {
            background: white;
            border-radius: 16px;
            padding: 35px 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }
        .card-header h2 {
            font-size: 26px;
            color: #1a1a2e;
        }
        .card-header .count {
            background: #f0f2f5;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            color: #555;
        }
        .card-header .count strong {
            color: #1a1a2e;
        }
        .table-wrapper {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        thead {
            background: linear-gradient(135deg, #1a1a2e, #302b63);
            color: white;
        }
        th {
            padding: 14px 16px;
            text-align: left;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        td {
            padding: 14px 16px;
            border-bottom: 1px solid #eee;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
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
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #888;
        }
        .empty-state .icon {
            font-size: 60px;
            margin-bottom: 15px;
        }
        .btn {
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }
        .btn-secondary:hover {
            background: #d0d0d0;
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
                padding: 20px 16px;
            }
            .card-header {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            th, td {
                padding: 10px 12px;
                font-size: 13px;
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
            <div class="card-header">
                <h2>📋 All Incidents</h2>
                <?php
                $count = 0;
                if (file_exists("incidents.csv")) {
                    $rows = file("incidents.csv", FILE_IGNORE_NEW_LINES);
                    $count = count($rows) - 1;
                    if ($count < 0) $count = 0;
                }
                ?>
                <span class="count">📊 Total: <strong><?php echo $count; ?></strong> incidents</span>
            </div>

            <div class="table-wrapper">
                <?php if ($count > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th># ID</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Date Reported</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (file_exists("incidents.csv")) {
                                $rows = array_map('str_getcsv', file("incidents.csv"));
                                foreach ($rows as $i => $fields) {
                                    if ($i === 0) continue;
                                    $statusClass = '';
                                    $statusLabel = htmlspecialchars($fields[4] ?? 'Open');
                                    if ($statusLabel === 'Open') $statusClass = 'status-open';
                                    elseif ($statusLabel === 'Investigating') $statusClass = 'status-investigating';
                                    elseif ($statusLabel === 'Resolved') $statusClass = 'status-resolved';
                                    echo "<tr>";
                                    echo "<td><strong>#" . htmlspecialchars($fields[0] ?? '') . "</strong></td>";
                                    echo "<td>" . htmlspecialchars($fields[1] ?? '') . "</td>";
                                    echo "<td>" . htmlspecialchars(substr($fields[2] ?? '', 0, 60)) . (strlen($fields[2] ?? '') > 60 ? '...' : '') . "</td>";
                                    echo "<td>" . htmlspecialchars($fields[3] ?? '') . "</td>";
                                    echo "<td><span class=\"status-badge $statusClass\">" . $statusLabel . "</span></td>";
                                    echo "</tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="icon">📭</div>
                        <h3>No Incidents Yet</h3>
                        <p style="color:#888; margin-top:8px;">Be the first to report a security incident.</p>
                        <a href="add_incident.php" class="btn btn-secondary" style="margin-top:20px;">+ Report Incident</a>
                    </div>
                <?php endif; ?>
            </div>

            <div style="margin-top:25px; text-align:center;">
                <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>