<?php
include 'partials/_dbconnect2.php'; // adjust path if needed

// Default to current month & year
$currentMonth = date('F');
$currentYear = date('Y');

// User selection
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : $currentMonth;
$selectedYear = isset($_GET['year']) ? $_GET['year'] : $currentYear;

// Query: show only members who have payments (paid or pending)
$sql = "
    SELECT m.name, p.amount, p.status, p.month, p.year
    FROM member m
    INNER JOIN payment p ON m.id = p.id
    WHERE p.month = ? AND p.year = ?
    ORDER BY 
        CASE 
            WHEN LOWER(p.status) = 'pending' THEN 1
            WHEN LOWER(p.status) = 'paid' THEN 2
            ELSE 3
        END, 
        m.name ASC
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('SQL Error: ' . $conn->error);
}
$stmt->bind_param('si', $selectedMonth, $selectedYear);
$stmt->execute();
$result = $stmt->get_result();

$payments = [];
$total_contribution = 0;
while ($row = $result->fetch_assoc()) {
    $payments[] = $row;
    if (strtolower($row['status']) === 'paid') {
        $total_contribution += $row['amount'];
    }
}
$stmt->close();

// Get available years from database
$yearsSql = "SELECT DISTINCT p.year FROM payment p ORDER BY p.year DESC";
$yearsStmt = $conn->prepare($yearsSql);
if (!$yearsStmt) {
    die('SQL Error: ' . $conn->error);
}
$yearsStmt->execute();
$yearsResult = $yearsStmt->get_result();
$availableYears = [];
while ($row = $yearsResult->fetch_assoc()) {
    $availableYears[] = $row['year'];
}
$yearsStmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Contribution - <?php echo htmlspecialchars($selectedMonth . " " . $selectedYear); ?></title>

<link rel="stylesheet" href="contribution_current_month.css">

<style>
/* Header and controls spacing */
.header-controls {
    margin-bottom: 8px;
    padding-top: 0;
    padding-bottom: 0;
}

.controls-wrapper {
    padding: 8px;
}

/* Keep amount centered on desktop */
.table-container table th:nth-child(2),
.table-container table td:nth-child(2),
.table-container table tfoot th:nth-child(2) {
    text-align: center;
}

/* Tablet header spacing */
@media (max-width: 768px) {
    .header-controls { margin-bottom: 8px; padding-top: 0; }
    .controls-wrapper { padding: 8px; gap: 12px; }
    .modern-filter-form { gap: 12px; }
}

@media (max-width: 600px) {
    .filter-group { min-width: 100%; flex: 1 1 100%; }
    .filter-submit-btn { width: 100%; }
}

/* Modern Filter Form */
.modern-filter-form {
    display: flex;
    align-items: flex-end;
    gap: 16px;
    flex-wrap: wrap;
    width: 100%;
    margin: 0;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex: 1;
    min-width: 140px;
}

.filter-group label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.select-wrapper {
    position: relative;
    width: 100%;
}

.select-wrapper select {
    width: 100%;
    appearance: none !important;
    background-color: #f8fafc !important;
    background-image: none !important;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 14px;
    padding-right: 36px;
    font-size: 15px;
    font-weight: 600;
    color: #0f172a;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.02) !important;
    height: 44px;
}

.select-wrapper select:hover {
    border-color: #cbd5e1;
    background-color: #ffffff !important;
    transform: none !important;
    box-shadow: none !important;
}

.select-wrapper select:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.15) !important;
    background-color: #ffffff !important;
}

/* Custom chevron for select */
.select-wrapper::after {
    content: '';
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    width: 16px;
    height: 16px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23475569' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: center;
    pointer-events: none;
}

.filter-submit-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: #ffffff;
    border: none;
    border-radius: 10px;
    padding: 0 24px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    height: 44px; /* Match height of select inputs with labels */
    margin-top: auto;
}

.filter-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
}

.filter-submit-btn:active {
    transform: translateY(0);
}

/* ===========================
   MOBILE RESPONSIVE FIX
   =========================== */
@media (max-width: 600px) {
    /* Remove horizontal scroll */
    .table-container {
        overflow-x: visible;
    }

    /* Hide desktop table header */
    .table-container thead {
        display: none;
    }

    /* Make table full width without min-width */
    .table-container table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        min-width: auto;
        background: transparent;
    }

    /* Card layout for each row: 2 columns */
    .table-container tbody tr {
        display: grid;
        grid-template-columns: 1fr auto;
        grid-template-rows: auto auto;
        gap: 4px 12px;
        align-items: center;
        
        background: linear-gradient(135deg, #d1e4fcff 0%, #9dc6f9ff 100%);
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 14px;
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.25);
        border: 1px solid rgba(59, 130, 246, 0.3);
        transition: all 0.3s ease;
    }

    .table-container tbody tr:active {
        transform: scale(0.98);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
    }

    /* Remove default table cell styling */
    .table-container tbody td {
        border: none;
        background: transparent;
        padding: 0;
    }

    /* Member Name - Top Left (Row 1, Col 1) */
    .table-container tbody td:nth-child(1) {
        grid-column: 1;
        grid-row: 1;
        font-weight: 700;
        font-size: 16px;
        color: #1e293b;
        text-align: left;
        letter-spacing: -0.2px;
    }

    /* Amount - Bottom Left (Row 2, Col 1) */
    .table-container tbody td:nth-child(2) {
        grid-column: 1;
        grid-row: 2;
        font-size: 15px;
        color: #6366f1;
        text-align: left;
        font-weight: 700;
        margin-top: 2px;
    }

 

    /* Status - Right Side spanning both rows (Row 1-2, Col 2) */
    .table-container tbody td:nth-child(3) {
        grid-column: 2;
        grid-row: 1 / 3;
        justify-self: end;
        align-self: center;
    }

    /* Paid and Pending badges */
    .paid-cell,
    .pending-cell {
        display: inline-block;
        font-weight: 700;
        border-radius: 10px;
        padding: 11px 18px;
        font-size: 13px;
        text-align: center;
        min-width: 90px;
        white-space: nowrap;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .paid-cell {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    }

    .paid-cell::before {
        content: '✓ ';
        font-size: 14px;
    }

    .pending-cell {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
    }

    .pending-cell::before {
        content: '⏱ ';
        font-size: 14px;
    }

    /* Footer - Total row */
    .table-container tfoot {
        display: block;
    }

    .table-container tfoot tr {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        border-radius: 14px;
        padding: 18px;
        box-shadow: 0 8px 24px rgba(99, 102, 241, 0.35);
        border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .table-container tfoot th {
        background: transparent;
        border: none;
        padding: 0;
        font-size: 15px;
        color: #ffffff;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table-container tfoot th:first-child {
        text-align: left;
        font-weight: 700;
    }

    .table-container tfoot th:nth-child(2) {
        text-align: right;
        color: #ffffff;
        font-size: 18px;
        font-weight: 800;
    }

    

    .table-container tfoot th:last-child {
        display: none;
    }

    /* Empty state message */
    .table-container tbody tr td[colspan="3"] {
        grid-column: 1 / -1;
        grid-row: 1;
        text-align: center;
        padding: 40px 20px;
        color: #64748b;
        font-style: italic;
        font-size: 15px;
    }

    .table-container tbody tr:has(td[colspan="3"]) {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        border: 2px dashed rgba(99, 102, 241, 0.3);
    }
}

/* Tablet fix */
@media (min-width: 601px) and (max-width: 1024px) {
    .table-container table { 
        min-width: 100%; 
    }
}
</style>

</head>

<body>
<div class="header-controls">

    <div class="controls-wrapper">
        <form id="monthYearForm" method="GET" class="modern-filter-form">
            <div class="filter-group">
                <label for="month">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    Month
                </label>
                <div class="select-wrapper">
                    <select name="month" id="month">
                        <?php
for ($m = 1; $m <= 12; $m++) {
    $monthName = date('F', mktime(0, 0, 0, $m, 10));
    $sel = ($monthName == $selectedMonth) ? 'selected' : '';
    echo "<option value='{$monthName}' {$sel}>{$monthName}</option>";
}
?>
                    </select>
                </div>
            </div>

            <div class="filter-group">
                <label for="year">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    Year
                </label>
                <div class="select-wrapper">
                    <select name="year" id="year">
                        <?php
foreach ($availableYears as $y) {
    $sel = ($y == $selectedYear) ? 'selected' : '';
    echo "<option value='{$y}' {$sel}>{$y}</option>";
}
?>
                    </select>
                </div>
            </div>

            <button type="submit" class="filter-submit-btn" id="filterBtn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                Filter Contribution — <span id="filterText"><?php echo htmlspecialchars($selectedMonth . " " . $selectedYear); ?></span>
            </button>
        </form>
    </div>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Member Name</th>
                <th>Amount (৳)</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($payments)): ?>
                <?php foreach ($payments as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo number_format($row['amount'], 2); ?> ৳</td>
                        <td>
                            <?php if (strtolower($row['status']) === 'paid'): ?>
                                <span class="paid-cell">Paid</span>
                            <?php
        else: ?>
                                <span class="pending-cell">Pending</span>
                            <?php
        endif; ?>
                        </td>
                    </tr>
                <?php
    endforeach; ?>
            <?php
else: ?>
                <tr>
                    <td colspan="3" style="padding:18px; text-align:center; font-style:italic;">
                        No contribution data found for <?php echo htmlspecialchars($selectedMonth . " " . $selectedYear); ?>.
                    </td>
                </tr>
            <?php
endif; ?>
        </tbody>

        <?php if (!empty($payments)): ?>
        <tfoot>
            <tr>
                <th>Total Paid</th>
                <th><?php echo number_format($total_contribution, 2); ?> ৳</th>
                <th></th>
            </tr>
        </tfoot>
        <?php
endif; ?>
    </table>
</div>

<script>
    // Update filter button text when month or year changes
    document.getElementById('month').addEventListener('change', updateFilterText);
    document.getElementById('year').addEventListener('change', updateFilterText);

    function updateFilterText() {
        const monthSelect = document.getElementById('month');
        const yearSelect = document.getElementById('year');
        const filterText = document.getElementById('filterText');
        
        const selectedMonth = monthSelect.value;
        const selectedYear = yearSelect.value;
        
        filterText.textContent = selectedMonth + ' ' + selectedYear;
    }
</script>

</body>
</html>