<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'partials/_dbconnect2.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}


$all_months = ['January','February','March','April','May','June', 'July', 'August', 'September', 'October', 'November', 'December'];
$allowed_statuses = ['pending', 'paid'];

$message = '';
$error = '';

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

$admin_name = $_SESSION['username'];

// Get current year or selected year
$current_year = date('Y');
$selected_year = isset($_GET['year']) ? (int)$_GET['year'] : $current_year;

// Get current page (default to page based on current month)
$current_month_index = (int)date('n') - 1; // 0-11
$default_page = floor($current_month_index / 3) + 1;
$current_page = isset($_GET['page']) ? max(1, min(4, (int)$_GET['page'])) : $default_page;

// Calculate which months to show (3 per page)
$months_per_page = 3;
$start_index = ($current_page - 1) * $months_per_page;
$allowed_months = array_slice($all_months, $start_index, $months_per_page);

// Handle Add Money form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_payment' && isset($_POST['member_id'], $_POST['amount'], $_POST['status'], $_POST['payment_date'])) {
        $member_id = (int)$_POST['member_id'];
        $amount = floatval($_POST['amount']);
        $status = $_POST['status'];
        $payment_date = $_POST['payment_date'];
        
        // Extract year and month from date
        $date_obj = new DateTime($payment_date);
        $year = (int)$date_obj->format('Y');
        $month = $date_obj->format('F'); // Full month name (January, February, etc.)

        if (in_array($month, $all_months) && $amount > 0 && in_array($status, $allowed_statuses)) {
            $stmt = $conn->prepare("INSERT INTO payment (id, month, year, amount, status, payment_date, added_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isiisss", $member_id, $month, $year, $amount, $status, $payment_date, $admin_name);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Added $amount to $month $year for member ID $member_id with status $status.";
                $stmt->close();
                header("Location: " . strtok($_SERVER['REQUEST_URI'], '?') . "?year=$year&page=$current_page");
                exit();
            } else {
                $error = "Database insert failed: " . $stmt->error;
            }
        } else {
            $error = "Invalid input for add payment.";
        }
    }
    elseif ($_POST['action'] === 'edit_payment' && isset($_POST['edit_member_id'], $_POST['edit_month'], $_POST['edit_amount'], $_POST['edit_status'], $_POST['edit_payment_date'])) {
        $member_id = (int)$_POST['edit_member_id'];
        $month = $_POST['edit_month'];
        $amount = floatval($_POST['edit_amount']);
        $status = $_POST['edit_status'];
        $payment_date = $_POST['edit_payment_date'];
        
        // Extract year from date
        $date_obj = new DateTime($payment_date);
        $year = (int)$date_obj->format('Y');

        if (in_array($month, $all_months) && $amount >= 0 && in_array($status, $allowed_statuses)) {
            $del_stmt = $conn->prepare("DELETE FROM payment WHERE id = ? AND month = ? AND year = ?");
            $del_stmt->bind_param("isi", $member_id, $month, $year);
            $del_stmt->execute();
            $del_stmt->close();

            if ($amount > 0) {
                $ins_stmt = $conn->prepare("INSERT INTO payment (id, month, year, amount, status, payment_date, added_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $ins_stmt->bind_param("isiisss", $member_id, $month, $year, $amount, $status, $payment_date, $admin_name);
                $ins_stmt->execute();
                $ins_stmt->close();
            }

            $_SESSION['message'] = "Payment updated for member ID $member_id, month $month $year.";
            header("Location: " . strtok($_SERVER['REQUEST_URI'], '?') . "?year=$year&page=$current_page");
            exit();
        } else {
            $error = "Invalid input for edit payment.";
        }
    }
}

// Handle payment status update from GET (mark pending as paid)
if (isset($_GET['confirm_id'], $_GET['confirm_month'], $_GET['confirm_year'])) {
    $confirm_id = (int)$_GET['confirm_id'];
    $confirm_month = $_GET['confirm_month'];
    $confirm_year = (int)$_GET['confirm_year'];

    if (in_array($confirm_month, $all_months)) {
        $stmt = $conn->prepare("UPDATE payment SET status='paid', added_by=? WHERE id=? AND month=? AND year=? AND status='pending'");
        $stmt->bind_param("sisi", $admin_name, $confirm_id, $confirm_month, $confirm_year);
        $stmt->execute();
        $stmt->close();

        $_SESSION['message'] = "Payment status updated to paid for $confirm_month $confirm_year.";
        header("Location: " . strtok($_SERVER['REQUEST_URI'], '?') . "?year=$confirm_year&page=$current_page");
        exit();
    }
}

// Fetch payments for selected year grouped by member, month, and status
$payments_stmt = $conn->prepare("
    SELECT id, month, status, SUM(amount) AS total_amount, 
           MAX(added_by) as added_by
    FROM payment
    WHERE year = ?
    GROUP BY id, month, status
");

if (!$payments_stmt) {
    die("Database prepare failed: " . $conn->error . "<br><br>Please run this SQL first:<br><code>ALTER TABLE payment ADD COLUMN year INT NOT NULL DEFAULT " . $current_year . ";<br>ALTER TABLE payment ADD COLUMN payment_date DATE NULL;<br>ALTER TABLE payment ADD COLUMN added_by VARCHAR(100) NULL;</code>");
}

$payments_stmt->bind_param("i", $selected_year);
$payments_stmt->execute();
$payments_result = $payments_stmt->get_result();

$payments_data = [];
if ($payments_result) {
    while ($row = $payments_result->fetch_assoc()) {
        $payments_data[$row['id']][$row['month']][$row['status']] = [
            'amount' => $row['total_amount'],
            'added_by' => $row['added_by']
        ];
    }
}

// Fetch all members
$members_result = $conn->query("SELECT id, name FROM member");

$members = [];
if ($members_result) {
    while ($member = $members_result->fetch_assoc()) {
        $id = $member['id'];
        $has_pending = false;
        $has_paid = false;

        foreach ($allowed_months as $m) {
            if (!empty($payments_data[$id][$m]['pending']['amount']) && $payments_data[$id][$m]['pending']['amount'] > 0) {
                $has_pending = true;
                break;
            }
            if (!empty($payments_data[$id][$m]['paid']['amount']) && $payments_data[$id][$m]['paid']['amount'] > 0) {
                $has_paid = true;
            }
        }

        if ($has_pending) {
            $sort_priority = 0;
        } elseif ($has_paid) {
            $sort_priority = 1;
        } else {
            $sort_priority = 2;
        }

        $members[] = [
            'id' => $id,
            'name' => $member['name'],
            'sort_priority' => $sort_priority,
        ];
    }

    usort($members, function ($a, $b) {
        if ($a['sort_priority'] !== $b['sort_priority']) {
            return $a['sort_priority'] - $b['sort_priority'];
        }
        return strcmp($a['name'], $b['name']);
    });
}

// Calculate month-wise totals (paid only) and grand total
$month_totals = [];
$grand_total = 0.0;
foreach ($allowed_months as $month) {
    $month_total = 0;
    foreach ($payments_data as $member_id => $months) {
        $month_total += $months[$month]['paid']['amount'] ?? 0;
    }
    $month_totals[$month] = $month_total;
    $grand_total += $month_total;
}

// Current editing target
$editing_member_id = $_GET['edit_member_id'] ?? null;
$editing_month = $_GET['edit_month'] ?? null;

// Get available years from database
$years_result = $conn->query("SELECT DISTINCT year FROM payment ORDER BY year DESC");
$available_years = [];

// Add range of years (2 years before current to 2 years after current)
for ($y = $current_year - 2; $y <= $current_year + 2; $y++) {
    $available_years[] = $y;
}

// Add years from database
if ($years_result) {
    while ($row = $years_result->fetch_assoc()) {
        if (!in_array($row['year'], $available_years)) {
            $available_years[] = $row['year'];
        }
    }
}

sort($available_years);
$available_years = array_reverse($available_years);

// Fetch payment date for editing
$edit_payment_date = date('Y-m-d'); // Default to today
if ($editing_member_id && $editing_month) {
    $date_stmt = $conn->prepare("SELECT payment_date FROM payment WHERE id = ? AND month = ? AND year = ? LIMIT 1");
    if ($date_stmt) {
        $date_stmt->bind_param("isi", $editing_member_id, $editing_month, $selected_year);
        $date_stmt->execute();
        $date_result = $date_stmt->get_result();
        if ($date_row = $date_result->fetch_assoc()) {
            $edit_payment_date = $date_row['payment_date'] ?: date('Y-m-d');
        }
        $date_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Member List and Payment</title>
<link rel="stylesheet" href="memberlist_and_payment.css" />
<style>
.user-info {
    font-size: 10px;
    color: #666;
    margin-top: 2px;
    font-style: italic;
}
</style>
<script>
function confirmPayment(memberId, month, year) {
    if (confirm(`Are you sure you want to mark pending payments for ${month} ${year} as PAID?`)) {
        window.location.href = `?confirm_id=${memberId}&confirm_month=${month}&confirm_year=${year}&year=${year}&page=<?= $current_page ?>`;
    }
}
function validateMonthSelect(form) {
    if (!form.edit_month.value) {
        alert("Please select a month to edit.");
        return false;
    }
    return true;
}
function changeYear(year) {
    window.location.href = `?year=${year}&page=<?= $current_page ?>`;
}
function changePage(page) {
    window.location.href = `?year=<?= $selected_year ?>&page=${page}`;
}

// Auto-fill month based on selected date
function updateMonthFromDate(input, monthSelect) {
    const date = new Date(input.value);
    const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const monthName = monthNames[date.getMonth()];
    monthSelect.value = monthName;
}

// Set default date to first day of selected month/year
window.addEventListener('DOMContentLoaded', function() {
    const dateInputs = document.querySelectorAll('input[name="payment_date"]');
    const year = <?= $selected_year ?>;
    const currentPage = <?= $current_page ?>;
    
    // Get first month of current page
    const monthsPerPage = 3;
    const startIndex = (currentPage - 1) * monthsPerPage;
    const monthIndex = startIndex; // First month of the page
    
    const defaultDate = `${year}-${String(monthIndex + 1).padStart(2, '0')}-01`;
    
    dateInputs.forEach(input => {
        if (!input.value) {
            input.value = defaultDate;
        }
    });
});
</script>
</head>
<body>

<div class="header-controls">
  
  
  <div class="controls-wrapper">
    <div class="year-selector">
      <label for="year">Year:</label>
      <select id="year" onchange="changeYear(this.value)">
        <?php foreach ($available_years as $year): ?>
          <option value="<?= $year ?>" <?= $year == $selected_year ? 'selected' : '' ?>><?= $year ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="pagination">
      <?php for ($i = 1; $i <= 4; $i++): ?>
        <?php
          $page_start = ($i - 1) * 3;
          $page_months = array_slice($all_months, $page_start, 3);
          $page_label = implode(', ', $page_months);
        ?>
        <button 
          class="page-btn <?= $i == $current_page ? 'active' : '' ?>" 
          onclick="changePage(<?= $i ?>)"
          title="<?= $page_label ?>">
          <?= $page_label ?>
        </button>
      <?php endfor; ?>
    </div>
  </div>
</div>

<?php if ($message): ?>
  <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if ($error): ?>
  <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="table-container">
<table>
  <thead>
    <tr>
      <th style="min-width: 140px;">Add Money</th>
      <th style="min-width: 150px;">Member Name</th>
      <?php foreach ($allowed_months as $m): ?>
        <th style="min-width: 120px;"><?= $m ?></th>
      <?php endforeach; ?>
      <th style="min-width: 130px;">Total (Paid)</th>
      <th style="min-width: 150px;">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($members)): ?>
      <?php foreach ($members as $member): ?>
        <?php
          $id = $member['id'];
          $total_paid = 0;
          foreach ($allowed_months as $m) {
            $total_paid += $payments_data[$id][$m]['paid']['amount'] ?? 0;
          }
        ?>
        <tr>
          <td>
            <form method="POST">
              <input type="hidden" name="action" value="add_payment" />
              <input type="hidden" name="member_id" value="<?= $id ?>" />

              <div class="add-money-row1">
                <input type="date" name="payment_date" required aria-label="Payment Date" style="flex: 1;" />
                <input type="number" name="amount" min="0.01" step="any" placeholder="Amount" required aria-label="Enter amount" style="flex: 1;" />
              </div>

              <div class="add-money-row2">
                <select name="status" required aria-label="Select Status">
                  <option value="pending">Pending</option>
                  <option value="paid">Paid</option>
                </select>

                <button type="submit">Add</button>
              </div>
            </form>
          </td>
          <td><?= htmlspecialchars($member['name']) ?></td>

          <?php foreach ($allowed_months as $m):
            $paid_data = $payments_data[$id][$m]['paid'] ?? null;
            $pending_data = $payments_data[$id][$m]['pending'] ?? null;
            $paid = $paid_data['amount'] ?? 0;
            $pending = $pending_data['amount'] ?? 0;
          ?>
            <td style="text-align:center;">
              <?php if ($editing_member_id == $id && $editing_month == $m): ?>
                <form method="POST">
                  <input type="hidden" name="action" value="edit_payment" />
                  <input type="hidden" name="edit_member_id" value="<?= $id ?>" />
                  <input type="hidden" name="edit_month" value="<?= $m ?>" />
                  <input type="date" name="edit_payment_date" value="<?= $edit_payment_date ?>" required style="width: 100%; margin-bottom: 5px;" />
                  <input type="number" name="edit_amount" min="0" step="0.01" value="<?= number_format($paid + $pending, 2, '.', '') ?>" required style="width: 100%; margin-bottom: 5px;" />
                  <select name="edit_status" required style="width: 100%; margin-bottom: 5px;">
                    <?php foreach ($allowed_statuses as $status): ?>
                      <option value="<?= $status ?>" <?= (($pending > 0 && $status == 'pending') || ($paid > 0 && $status == 'paid')) ? 'selected' : '' ?>>
                        <?= ucfirst($status) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <button type="submit" style="width: 100%; margin-bottom: 3px;">Save</button>
                  <a href="?year=<?= $selected_year ?>&page=<?= $current_page ?>" style="display: block; text-align: center;">Cancel</a>
                </form>
              <?php else: ?>
                <?php if ($pending > 0): ?>
                  <div class="pending-cell" style="margin-bottom:4px; cursor:pointer;" onclick="confirmPayment(<?= $id ?>, '<?= $m ?>', <?= $selected_year ?>)">
                    Pending: <?= number_format($pending, 2) ?>
                    <?php if ($pending_data['added_by']): ?>
                      <div class="user-info">by: <?= htmlspecialchars($pending_data['added_by']) ?></div>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
                <?php if ($paid > 0): ?>
                  <div class="paid-cell">
                    Paid: <?= number_format($paid, 2) ?>
                    <?php if ($paid_data['added_by']): ?>
                      <div class="user-info">by: <?= htmlspecialchars($paid_data['added_by']) ?></div>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
                <?php if ($pending == 0 && $paid == 0): ?>
                  &mdash;
                <?php endif; ?>
              <?php endif; ?>
            </td>
          <?php endforeach; ?>

          <td><strong><?= number_format($total_paid, 2) ?></strong></td>

          <td>
            <?php
              // Get months that have data for this member
              $available_edit_months = [];
              foreach ($allowed_months as $m) {
                $has_data = ($payments_data[$id][$m]['paid']['amount'] ?? 0) > 0 || ($payments_data[$id][$m]['pending']['amount'] ?? 0) > 0;
                if ($has_data) {
                  $available_edit_months[] = $m;
                }
              }
            ?>
            
            <?php if (!empty($available_edit_months)): ?>
              <form method="GET" onsubmit="return validateMonthSelect(this);">
                <input type="hidden" name="edit_member_id" value="<?= $id ?>" />
                <input type="hidden" name="year" value="<?= $selected_year ?>" />
                <input type="hidden" name="page" value="<?= $current_page ?>" />
                <select name="edit_month" required>
                  <option value="">Select month</option>
                  <?php foreach ($available_edit_months as $m): ?>
                    <option value="<?= $m ?>"><?= $m ?></option>
                  <?php endforeach; ?>
                </select>
                <button type="submit" class="edit-button">Edit</button>
              </form>
            <?php else: ?>
              <span style="color: #999; font-size: 13px;">No data</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="<?= 4 + count($allowed_months) ?>">No members found.</td></tr>
    <?php endif; ?>
  </tbody>
  <tfoot>
    <tr>
      <th colspan="2">Month-wise Total (Paid)</th>
      <?php foreach ($allowed_months as $m): ?>
        <th><?= number_format($month_totals[$m], 2) ?></th>
      <?php endforeach; ?>
      <th><?= number_format($grand_total, 2) ?></th>
      <th></th>
    </tr>
  </tfoot>
</table>
</div>

</body>
</html>