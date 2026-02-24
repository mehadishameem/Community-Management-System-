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
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Contribution - <?php echo htmlspecialchars($selectedMonth . " " . $selectedYear); ?></title>

<link rel="stylesheet" href="memberlist_and_payment.css">

<style>
/* Keep amount centered on desktop */
.table-container table th:nth-child(2),
.table-container table td:nth-child(2),
.table-container table tfoot th:nth-child(2) {
    text-align: center;
}

/* Tablet header spacing */
@media (max-width: 768px) {
    .header-controls { margin-bottom: 12px; padding-top: 6px; }
    .controls-wrapper { padding: 12px; gap: 8px; }
    .year-selector label { display:block; color: #fff; font-weight:700; margin-bottom:6px; }
    .year-selector { gap:8px; padding:0; align-items:stretch; }
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
    <h2>Contribution — <?php echo htmlspecialchars($selectedMonth . " " . $selectedYear); ?></h2>

    <div class="controls-wrapper">
        <form id="monthYearForm" method="GET" class="year-selector" style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
            <div style="display:flex; align-items:center; gap:8px;">
                <label for="month">Month:</label>
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

            <div style="display:flex; align-items:center; gap:8px;">
                <label for="year">Year:</label>
                <select name="year" id="year">
                    <?php
                    for ($y = 2024; $y <= date('Y'); $y++) {
                        $sel = ($y == $selectedYear) ? 'selected' : '';
                        echo "<option value='{$y}' {$sel}>{$y}</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit">Show</button>
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
                            <?php else: ?>
                                <span class="pending-cell">Pending</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="padding:18px; text-align:center; font-style:italic;">
                        No contribution data found for <?php echo htmlspecialchars($selectedMonth . " " . $selectedYear); ?>.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>

        <?php if (!empty($payments)): ?>
        <tfoot>
            <tr>
                <th>Total Paid</th>
                <th><?php echo number_format($total_contribution, 2); ?> ৳</th>
                <th></th>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>
</div>
</body>
</html>