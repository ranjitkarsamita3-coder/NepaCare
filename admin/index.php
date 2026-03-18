<?php
session_start();
include '../config/db.php';

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit;
}

$role = 'admin';
$activePage = 'dashboard';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - NepaCare</title>
    <link rel="stylesheet" href="../assets/css/caregiverstyle.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .page-wrapper { display: flex; min-height: 100vh; }
        .admin-sidebar {
            width: 250px;
            background-color: #1e3a8a;
            color: #fefefe;
            padding: 30px 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .admin-sidebar h3 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
        }
        .admin-sidebar a {
            display: block;
            padding: 12px 15px;
            margin-bottom: 10px;
            text-decoration: none;
            color: #fefefe;
            font-size: 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .admin-sidebar a:hover, .admin-sidebar a.active {
            background-color: #3478e5;
            color: #fd866b;
            transform: translateX(5px);
        }
        .admin-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }
        .admin-content h1 {
            color: #b43113;
            margin-bottom: 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #1e3a8a;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-card h3 {
            margin: 0 0 10px 0;
            color: #666;
            font-size: 14px;
        }
        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
            color: #1e3a8a;
        }
        .chart-container {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            max-width: 600px;
        }
        .chart-container h2 {
            color: #1e3a8a;
            margin-top: 0;
            margin-bottom: 20px;
        }
        #userChart {
            max-height: 400px;
        }
        .logout-btn {
            display: inline-block;
            padding: 10px 18px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
        .logout-btn:hover { background: #c82333; }
    </style>
</head>
<body>

<div class="page-wrapper">
    <div class="admin-sidebar">
        <div class="logo-container" style="text-align:center; margin-bottom:20px;">
            <img src="../assets/images/logo.png" alt="NepaCare Logo" class="logo" style="max-width:100px; height:auto; border-radius:15px; border:2px solid #fff;" />
        </div>
        <h3>NepaCare Admin</h3>
        <a href="index.php" class="<?= $activePage=='dashboard'?'active':'' ?>">Dashboard</a>
        <a href="manage_registrations.php" class="<?= $activePage=='registrations'?'active':'' ?>">Manage Registrations</a>
        <a href="manage_users.php" class="<?= $activePage=='users'?'active':'' ?>">Manage Users</a>
        <a href="feedback.php" class="<?= $activePage=='feedback'?'active':'' ?>">Feedback</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="admin-content">
        <h1>Welcome, <?php echo $_SESSION['admin_name']; ?>!</h1>
        
        <div class="stats-grid">
            <?php
            $total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"));
            
            $total_caregivers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='caregiver'"));
            
            $total_elders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='elder'"));
            
            $linked_pairs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT linked_elder_id) as count FROM users WHERE linked_elder_id IS NOT NULL"));
            
            ?>
            
            <div class="stat-card">
                <h3>Total Users</h3>
                <div class="number"><?php echo $total_users['count']; ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Total Caregivers</h3>
                <div class="number"><?php echo $total_caregivers['count']; ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Total Elders</h3>
                <div class="number"><?php echo $total_elders['count']; ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Linked Pairs</h3>
                <div class="number"><?php echo $linked_pairs['count']; ?></div>
            </div>
        </div>

        <div class="chart-container">
            <h2>User Statistics by Role</h2>
            <canvas id="userChart"></canvas>
        </div>

        <script>
            const ctx = document.getElementById('userChart').getContext('2d');
            const userChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Caregivers', 'Elders', 'Linked Pairs'],
                    datasets: [{
                        label: 'Count',
                        data: [<?php echo $total_caregivers['count']; ?>, <?php echo $total_elders['count']; ?>, <?php echo $linked_pairs['count']; ?>],
                        backgroundColor: [
                            '#3b82f6',
                            '#8b5cf6',
                            '#10b981'
                        ],
                        borderColor: [
                            '#1e3a8a',
                            '#5b21b6',
                            '#059669'
                        ],
                        borderWidth: 2,
                        borderRadius: 8,
                        padding: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            },
                            title: {
                                display: true,
                                text: 'Number of Users'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                font: {
                                    size: 14
                                }
                            }
                        }
                    }
                }
            });
        </script>
    </div>
</div>

</body>
</html>
