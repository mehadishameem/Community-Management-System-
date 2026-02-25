<?php


include 'partials/_dbconnect2.php';

// Get current year
$current_year = date('Y');
$selected_year = isset($_GET['year']) ? (int)$_GET['year'] : $current_year;

// Get total members
$total_members_result = $conn->query("SELECT COUNT(*) as total FROM member");
$total_members = $total_members_result->fetch_assoc()['total'];

// Get total contributions for selected year
$year_contributions_result = $conn->query("SELECT SUM(amount) as total FROM payment WHERE status='paid' AND year=$selected_year");
$year_contributions = $year_contributions_result->fetch_assoc()['total'] ?? 0;

// Get total expenses for selected year
$year_expenses_result = $conn->query("SELECT SUM(total_cost) as total FROM expenses WHERE YEAR(date)=$selected_year");
$year_expenses = $year_expenses_result->fetch_assoc()['total'] ?? 0;

// Calculate total dues (pending payments for selected year)
$total_dues_result = $conn->query("SELECT SUM(amount) as total FROM payment WHERE status='pending' AND year=$selected_year");
$total_dues = $total_dues_result->fetch_assoc()['total'] ?? 0;

// Get members with pending payments
$members_with_dues_result = $conn->query("SELECT COUNT(DISTINCT id) as count FROM payment WHERE status='pending' AND year=$selected_year");
$members_with_dues = $members_with_dues_result->fetch_assoc()['count'];

// Get members who paid
$members_paid_result = $conn->query("SELECT COUNT(DISTINCT id) as count FROM payment WHERE status='paid' AND year=$selected_year");
$members_paid = $members_paid_result->fetch_assoc()['count'];

// Calculate balance for selected year
$year_balance = $year_contributions - $year_expenses;

// Get top 10 contributors for selected year
$top_contributors_result = $conn->query("
    SELECT m.name, SUM(p.amount) as total 
    FROM payment p 
    JOIN member m ON p.id = m.id 
    WHERE p.status='paid' AND p.year=$selected_year
    GROUP BY p.id, m.name 
    ORDER BY total DESC 
    LIMIT 10
");
$top_contributors = [];
if ($top_contributors_result) {
    while ($row = $top_contributors_result->fetch_assoc()) {
        $top_contributors[] = $row;
    }
}

// Get members with highest dues
$highest_dues_result = $conn->query("
    SELECT m.name, SUM(p.amount) as total 
    FROM payment p 
    JOIN member m ON p.id = m.id 
    WHERE p.status='pending' AND p.year=$selected_year
    GROUP BY p.id, m.name 
    ORDER BY total DESC 
    LIMIT 10
");
$highest_dues = [];
if ($highest_dues_result) {
    while ($row = $highest_dues_result->fetch_assoc()) {
        $highest_dues[] = $row;
    }
}

// Month-wise contribution data for graph
$months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
$full_months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
$monthly_data = [];

foreach ($full_months as $month) {
    $stmt = $conn->prepare("SELECT SUM(amount) as total FROM payment WHERE status='paid' AND year=? AND month=?");
    $stmt->bind_param("is", $selected_year, $month);
    $stmt->execute();
    $result = $stmt->get_result();
    $monthly_data[] = $result->fetch_assoc()['total'] ?? 0;
    $stmt->close();
}

// Get available years
$years_result = $conn->query("SELECT DISTINCT year FROM payment ORDER BY year DESC");
$available_years = [];
for ($y = $current_year - 2; $y <= $current_year + 2; $y++) {
    $available_years[] = $y;
}
if ($years_result) {
    while ($row = $years_result->fetch_assoc()) {
        if (!in_array($row['year'], $available_years)) {
            $available_years[] = $row['year'];
        }
    }
}
sort($available_years);
$available_years = array_reverse($available_years);

// Calculate payment rate
$payment_rate = $total_members > 0 ? round(($members_paid / $total_members) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f3f4f6;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .title {
            font-size: 2rem;
            color: #192F59;
            font-weight: 700;
        }

        .year-selector select {
            padding: 10px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            background: white;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid;
        }

        .stat-box.blue { border-left-color: #3b82f6; }
        .stat-box.green { border-left-color: #10b981; }
        .stat-box.red { border-left-color: #ef4444; }
        .stat-box.purple { border-left-color: #8b5cf6; }
        .stat-box.orange { border-left-color: #f59e0b; }

        .stat-label {
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1f2937;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .chart-title {
            font-size: 1.3rem;
            color: #192F59;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .chart-container {
            position: relative;
            height: 350px;
        }

        .contributors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
        }

        .contributors-section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .section-title {
            font-size: 1.3rem;
            color: #192F59;
            margin-bottom: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .contributor-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            transition: background 0.2s;
        }

        .contributor-item:hover {
            background: #f9fafb;
        }

        .contributor-item:last-child {
            border-bottom: none;
        }

        .contributor-rank {
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .contributor-rank.top3 {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .contributor-name {
            flex: 1;
            margin-left: 15px;
            font-weight: 600;
            color: #1f2937;
        }

        .contributor-amount {
            font-weight: 700;
            color: #10b981;
            font-size: 1.1rem;
        }

        .no-data {
            text-align: center;
            color: #9ca3af;
            padding: 40px;
        }

        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }

            .contributors-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .title {
                font-size: 1.5rem;
            }

            .stats-row {
                grid-template-columns: 1fr;
            }

            .chart-container {
                height: 250px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1 class="title">📊 Analytics Dashboard - <?= $selected_year ?></h1>
        <div class="year-selector">
            <select id="yearSelect">
                 <?php foreach ($available_years as $year): ?>
                    <option value="<?= $year ?>" <?= $year == $selected_year ? 'selected' : '' ?>><?= $year ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-box blue">
            <div class="stat-label">👥 Total Members</div>
            <div class="stat-value"><?= $total_members ?></div>
        </div>

        <div class="stat-box green">
            <div class="stat-label">💰 Total Contributions</div>
            <div class="stat-value">৳<?= number_format($year_contributions, 2) ?></div>
        </div>

        <div class="stat-box red">
            <div class="stat-label">⚠️ Total Dues</div>
            <div class="stat-value">৳<?= number_format($total_dues, 2) ?></div>
        </div>

        <div class="stat-box purple">
            <div class="stat-label">✅ Members Paid</div>
            <div class="stat-value"><?= $members_paid ?> / <?= $total_members ?></div>
        </div>

        <div class="stat-box orange">
            <div class="stat-label">📊 Payment Rate</div>
            <div class="stat-value"><?= $payment_rate ?>%</div>
        </div>
    </div>

   
    <!-- Contributors Section -->
    <div class="contributors-grid">
        <div class="contributors-section">
            <h3 class="section-title">🏆 Top Contributors</h3>
            <?php if (!empty($top_contributors)): ?>
                <?php foreach ($top_contributors as $index => $contributor): ?>
                    <div class="contributor-item">
                        <div class="contributor-rank <?= $index < 3 ? 'top3' : '' ?>">
                            <?= $index + 1 ?>
                        </div>
                        <div class="contributor-name"><?= htmlspecialchars($contributor['name']) ?></div>
                        <div class="contributor-amount">৳<?= number_format($contributor['total'], 2) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data">No contributions yet</div>
            <?php endif; ?>
        </div>

        <div class="contributors-section">
            <h3 class="section-title">💸 Highest Dues</h3>
            <?php if (!empty($highest_dues)): ?>
                <?php foreach ($highest_dues as $index => $due): ?>
                    <div class="contributor-item">
                        <div class="contributor-rank">
                            <?= $index + 1 ?>
                        </div>
                        <div class="contributor-name"><?= htmlspecialchars($due['name']) ?></div>
                        <div class="contributor-amount" style="color: #ef4444;">৳<?= number_format($due['total'], 2) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data">No pending dues</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
(function () {
    const yearSelect = document.getElementById('yearSelect');
    const contentContainer = document.getElementById('content');

    // If analytics.php is opened directly (no #content wrapper),
    // handle year change by reloading the whole page.
    if (!contentContainer && yearSelect) {
        yearSelect.addEventListener('change', function () {
            const selectedYear = this.value;
            window.location.href = 'analytics.php?year=' + encodeURIComponent(selectedYear);
        });
    }

    // Chart rendering logic
    const months = <?= json_encode($months) ?>;
    const monthlyData = <?= json_encode($monthly_data) ?>;
    const membersPaid = <?= $members_paid ?>;
    const membersWithDues = <?= $members_with_dues ?>;

    // Line chart
    const monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Contributions',
                    data: monthlyData,
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => '৳' + value.toLocaleString()
                        }
                    }
                }
            }
        });
    }

    // Pie chart
    const pieCtx = document.getElementById('pieChart');
    if (pieCtx) {
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Paid', 'Pending'],
                datasets: [{
                    data: [membersPaid, membersWithDues],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderColor: [
                        'rgba(16, 185, 129, 1)',
                        'rgba(239, 68, 68, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
})();
</script>



</body>
</html>