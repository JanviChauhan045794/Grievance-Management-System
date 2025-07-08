<?php
// Establish connection to the database
include '../php/config.php'; // Include your database connection script

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and role is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Get the user_id from session

// Fetch general statistics
$usersCount = $con->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$facultyCount = $con->query("SELECT COUNT(*) as count FROM faculty")->fetch_assoc()['count'];

$resolvedComplaints = $con->query("SELECT COUNT(*) as count FROM complaints WHERE Status = 'resolved'")->fetch_assoc()['count'];
$processingComplaints = $con->query("SELECT COUNT(*) as count FROM complaints WHERE Status = 'processing'")->fetch_assoc()['count'];
$pendingComplaints = $con->query("SELECT COUNT(*) as count FROM complaints WHERE Status = 'pending'")->fetch_assoc()['count'];

$totalComplaints = $resolvedComplaints + $processingComplaints + $pendingComplaints;

// Fetch the number of complaints per category
$complaintsPerCategory = $con->query("SELECT cc.category_description, COUNT(c.Comp_ID) AS count 
                                       FROM complaint_category cc 
                                       LEFT JOIN complaints c ON cc.complaint_category_id = c.Complaint_Cat_ID 
                                       GROUP BY cc.category_description");

$categories = [];
$counts = [];
while ($row = $complaintsPerCategory->fetch_assoc()) {
    $categories[] = $row['category_description'];
    $counts[] = $row['count'];
}

// Fetch daily complaints data
$dailyComplaints = $con->query("SELECT DATE(complaint_datetime) AS date, COUNT(*) AS complaints_count 
                                FROM complaints
                                GROUP BY DATE(complaint_datetime)
                                ORDER BY DATE(complaint_datetime)");

$dates = [];
$dailyCounts = [];
while ($row = $dailyComplaints->fetch_assoc()) {
    $dates[] = $row['date'];
    $dailyCounts[] = $row['complaints_count'];
}

// Fetch complaints unresolved after due date or without a due date
$unresolvedAfterDueDate = $con->query("SELECT DATE(complaint_datetime) AS date, COUNT(*) AS count 
                                       FROM complaints 
                                       WHERE (status != 'resolved' AND Due_Date < NOW()) 
                                       OR Due_Date IS NULL
                                       GROUP BY DATE(complaint_datetime)
                                       ORDER BY DATE(complaint_datetime)");

$unresolvedDates = [];
$unresolvedCounts = [];
while ($row = $unresolvedAfterDueDate->fetch_assoc()) {
    $unresolvedDates[] = $row['date'];
    $unresolvedCounts[] = $row['count'];
}

// Fetch complaints received and resolved for each faculty
$facultyComplaints = $con->query("
  SELECT 
    CONCAT(u.firstname, ' ', u.lastname) AS faculty_name, 
    COUNT(c.Comp_ID) AS total_complaints,
    COALESCE(SUM(CASE WHEN c.status = 'resolved' THEN 1 ELSE 0 END), 0) AS resolved_complaints,
    COALESCE(SUM(CASE WHEN c.status = 'pending' THEN 1 ELSE 0 END), 0) AS pending_complaints
FROM 
    faculty f
LEFT JOIN 
    users u ON f.user_id = u.user_id  -- Corrected the join condition here
LEFT JOIN 
    complaints c ON f.faculty_id = c.faculty_id
GROUP BY 
    f.faculty_id;

");



$facultyNames = [];
$facultyTotalComplaints = [];
$facultyResolvedComplaints = [];
$facultyPendingComplaints = [];

while ($row = $facultyComplaints->fetch_assoc()) {
    $facultyNames[] = $row['faculty_name'];
    $facultyTotalComplaints[] = $row['total_complaints'];
    $facultyResolvedComplaints[] = $row['resolved_complaints'];
    $facultyPendingComplaints[] = $row['pending_complaints'];
}

// Monthly Complaints Data (For interactive features)
$monthlyComplaints = $con->query("SELECT MONTH(complaint_datetime) AS month, YEAR(complaint_datetime) AS year, COUNT(*) AS count 
                                  FROM complaints 
                                  GROUP BY YEAR(complaint_datetime), MONTH(complaint_datetime)
                                  ORDER BY year DESC, month DESC");

$monthlyData = [];
while ($row = $monthlyComplaints->fetch_assoc()) {
    $monthlyData[] = [
        'label' => $row['year'] . '-' . str_pad($row['month'], 2, '0', STR_PAD_LEFT),
        'count' => $row['count']
    ];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9fafc;
            color: #333;
            margin: 0;
            padding: 0;
        }
        /* dashboard boxes */
         /* Container */
         .dashboard-container {
            margin-left: 220px; /* Adjust for the sidenav */
            padding: 80px 20px; /* Adjust for the navbar */
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Dashboard rows */
        .dashboard-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }

        /* Dashboard boxes */
        .dashboard-box {
            flex: 1;
            min-width: 200px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        .dashboard-box h3 {
            margin: 0;
            font-size: 20px;
            color: #333;
        }

        .dashboard-box p {
            font-size: 24px;
            color: #4CAF50;
            margin: 10px 0 0 0;
        }
        /* Sidebar adjustment */
        .content-wrapper {
            margin-left: 270px;
            padding: 20px;
        }
        .content-wrapper1{
            margin-left: 250px;
            padding: 20px;
        }
        .stat-box {
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .stat-box h5 {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .stat-box p {
            font-size: 24px;
            font-weight: bold;
            color: green;
        }
        .stat-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-box-container {
            flex: 1 1 calc(33.333% - 20px); /* 3 boxes per row */
            margin: 10px;
        }
        .chart-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
            overflow: hidden;
            padding: 15px;
        }
        .chart-card:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .chart-container {
            position: relative;
            width: 100%;
            height: 300px;
        }
        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 800px;
        }
        .close-modal {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 30px;
            color: #333;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include "admindashboard.php"; ?>
    <div class="dashboard-container">
        <div class="dashboard-row">
            <div class="dashboard-box">
                <h3>Number of Users</h3>
                <p><?php echo $usersCount; ?></p>
            </div>
            <div class="dashboard-box">
                <h3>Number of Faculty</h3>
                <p><?php echo $facultyCount; ?></p>
            </div>
            <div class="dashboard-box">
                <h3>Total Complaints</h3>
                <p><?php echo $totalComplaints; ?></p>
            </div>
        </div>
        <div class="dashboard-row">
            <div class="dashboard-box">
                <h3>Resolved Complaints</h3>
                <p><?php echo $resolvedComplaints; ?></p>
            </div>
            <div class="dashboard-box">
                <h3>Processing Complaints</h3>
                <p><?php echo $processingComplaints; ?></p>
            </div>
            <div class="dashboard-box">
                <h3>Pending Complaints</h3>
                <p><?php echo $pendingComplaints; ?></p>
            </div>
        </div>
    </div>
    
   

    <div class="content-wrapper">
        <div class="container">
            <div class="row g-4">
                <!-- Complaints Per Category -->
                <div class="col-md-6 col-lg-6">
                    <div class="chart-card" data-chart="categoryChart">
                        <h5 class="text-center">Complaints by Category</h5>
                        <div class="chart-container">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Daily Complaints -->
                <div class="col-md-6 col-lg-6">
                    <div class="chart-card" data-chart="dailyChart">
                        <h5 class="text-center">Daily Complaints</h5>
                        <div class="chart-container">
                            <canvas id="dailyChart"></canvas>
                        </div>
                    </div>
                </div>
                


            </div>
        </div>
    </div>

    <!-- Fullscreen Modal -->
    <div id="chartModal" class="modal">
        <span class="close-modal" id="closeModal">&times;</span>
        <div class="modal-content">
            <canvas id="modalChart"></canvas>
        </div>
    </div>

    <!-- Chart.js and Modal Logic -->
    <script>
        // Sample Data from PHP
        const categoryLabels = <?= json_encode($categories) ?>;
        const categoryData = <?= json_encode($counts) ?>;

        const dailyLabels = <?= json_encode($dates) ?>;
        const dailyData = <?= json_encode($dailyCounts) ?>;

        const unresolvedLabels = <?= json_encode($unresolvedDates) ?>;
        const unresolvedData = <?= json_encode($unresolvedCounts) ?>;

        const monthlyLabels = <?= json_encode(array_column($monthlyData, 'label')) ?>;
        const monthlyData = <?= json_encode(array_column($monthlyData, 'count')) ?>;

        // Chart Configurations
        const chartConfigs = {
            categoryChart: {
                type: 'pie',
                data: { labels: categoryLabels, datasets: [{ data: categoryData, backgroundColor: ['#ff6384', '#36a2eb', '#cc65fe', '#ffce56'] }] }
            },
            dailyChart: {
                type: 'line',
                data: { labels: dailyLabels, datasets: [{ label: 'Daily Complaints', data: dailyData, borderColor: '#4bc0c0', backgroundColor: 'rgba(75, 192, 192, 0.2)', fill: true }] }
            },
            unresolvedChart: {
                type: 'bar',
                data: { labels: unresolvedLabels, datasets: [{ label: 'Unresolved Complaints', data: unresolvedData, backgroundColor: '#ff9f40' }] }
            },
            monthlyChart: {
                type: 'line',
                data: { labels: monthlyLabels, datasets: [{ label: 'Monthly Complaints', data: monthlyData, borderColor: '#9966ff', backgroundColor: 'rgba(153, 102, 255, 0.2)', fill: true }] }
            }
        };

        // Initialize Charts
        const charts = {};
        for (let chartId in chartConfigs) {
            charts[chartId] = new Chart(document.getElementById(chartId), chartConfigs[chartId]);
        }

        // Modal for Enlarged Charts
        const modal = document.getElementById('chartModal');
        const modalChartCanvas = document.getElementById('modalChart');
        const closeModal = document.getElementById('closeModal');
        let modalChart;

        document.querySelectorAll('.chart-card').forEach(card => {
            card.addEventListener('click', () => {
                const chartId = card.getAttribute('data-chart');
                const chartConfig = charts[chartId].config;

                modal.style.display = 'flex';
                if (modalChart) modalChart.destroy();
                modalChart = new Chart(modalChartCanvas, chartConfig);
            });
        });

        closeModal.addEventListener('click', () => {
            modal.style.display = 'none';
            if (modalChart) modalChart.destroy();
        });

        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
                if (modalChart) modalChart.destroy();
            }
        });

        const facultyLabels = <?= json_encode($facultyNames) ?>;  // Faculty names
const facultyTotalData = <?= json_encode($facultyTotalComplaints) ?>;  // Total complaints per faculty

// Chart Configuration for Total Complaints per Faculty
const facultyChartConfig = {
    type: 'bar', // Bar chart for total complaints
    data: {
        labels: facultyLabels, // Faculty names as labels
        datasets: [{
            label: 'Total Complaints',
            data: facultyTotalData, // Data for total complaints per faculty
            backgroundColor: '#36a2eb', // Blue color for the bars
            borderColor: '#36a2eb',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Faculty'
                },
                grid: {
                    display: false
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Number of Complaints'
                },
                beginAtZero: true, // Start the y-axis at 0
                ticks: {
                    stepSize: 1 // Set the step size for the y-axis ticks
                }
            }
        },
        plugins: {
            legend: {
                display: false // Hide the legend as there's only one dataset
            },
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        return tooltipItem.label + ': ' + tooltipItem.raw + ' complaints';
                    }
                }
            }
        }
    }
};

// Check if the canvas element exists before initializing the chart
const facultyChartCanvas = document.getElementById('facultyChart');
if (facultyChartCanvas) {
    new Chart(facultyChartCanvas, facultyChartConfig); // Initialize the chart
} else {
    console.error("Faculty chart canvas element not found.");
}



        
    </script>
</body>
</html>
