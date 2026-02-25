<?php
session_start();

// Redirect to login if not authenticated as admin
if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'admin') {
  header("Location: login.php");
  exit();
}

// If logout button clicked
if (isset($_GET['logout'])) {
  session_unset();
  session_destroy();
  header("Location: index.php");
  exit();
}

// -------------------------------------------------------
// Handle memberlist_and_payment.php form submissions HERE
// (before any HTML output, so header() redirects work)
// -------------------------------------------------------
include_once 'partials/_dbconnect2.php';

$all_months_admin = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
$allowed_statuses_admin = ['pending', 'paid'];
$admin_name_admin = $_SESSION['username'];

$current_year_admin = date('Y');
$selected_year_admin = isset($_GET['year']) ? (int)$_GET['year'] : $current_year_admin;
$current_page_admin = isset($_GET['page']) ? max(1, min(4, (int)$_GET['page'])) : (floor(((int)date('n') - 1) / 3) + 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

  if ($_POST['action'] === 'add_payment' && isset($_POST['member_id'], $_POST['amount'], $_POST['status'], $_POST['payment_date'])) {
    $member_id = (int)$_POST['member_id'];
    $amount = floatval($_POST['amount']);
    $status = $_POST['status'];
    $payment_date = $_POST['payment_date'];
    $date_obj = new DateTime($payment_date);
    $year = (int)$date_obj->format('Y');
    $month = $date_obj->format('F');

    if (in_array($month, $all_months_admin) && $amount > 0 && in_array($status, $allowed_statuses_admin)) {
      $stmt = $conn->prepare("INSERT INTO payment (id, month, year, amount, status, payment_date, added_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("isiisss", $member_id, $month, $year, $amount, $status, $payment_date, $admin_name_admin);
      if ($stmt->execute()) {
        $_SESSION['message'] = "Added $amount to $month $year for member ID $member_id with status $status.";
        $stmt->close();
        header("Location: admin.php?year=$year&page=$current_page_admin");
        exit();
      }
      $stmt->close();
    }
  }

  elseif ($_POST['action'] === 'edit_payment' && isset($_POST['edit_member_id'], $_POST['edit_month'], $_POST['edit_amount'], $_POST['edit_status'], $_POST['edit_payment_date'])) {
    $member_id = (int)$_POST['edit_member_id'];
    $month = $_POST['edit_month'];
    $amount = floatval($_POST['edit_amount']);
    $status = $_POST['edit_status'];
    $payment_date = $_POST['edit_payment_date'];
    $date_obj = new DateTime($payment_date);
    $year = (int)$date_obj->format('Y');

    if (in_array($month, $all_months_admin) && $amount >= 0 && in_array($status, $allowed_statuses_admin)) {
      $del = $conn->prepare("DELETE FROM payment WHERE id = ? AND month = ? AND year = ?");
      $del->bind_param("isi", $member_id, $month, $year);
      $del->execute();
      $del->close();

      if ($amount > 0) {
        $ins = $conn->prepare("INSERT INTO payment (id, month, year, amount, status, payment_date, added_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $ins->bind_param("isiisss", $member_id, $month, $year, $amount, $status, $payment_date, $admin_name_admin);
        $ins->execute();
        $ins->close();
      }

      $_SESSION['message'] = "Payment updated for member ID $member_id, month $month $year.";
      header("Location: admin.php?year=$year&page=$current_page_admin");
      exit();
    }
  }
}

// Handle confirm payment (GET)
if (isset($_GET['confirm_id'], $_GET['confirm_month'], $_GET['confirm_year'])) {
  $confirm_id = (int)$_GET['confirm_id'];
  $confirm_month = $_GET['confirm_month'];
  $confirm_year = (int)$_GET['confirm_year'];

  if (in_array($confirm_month, $all_months_admin)) {
    $stmt = $conn->prepare("UPDATE payment SET status='paid', added_by=? WHERE id=? AND month=? AND year=? AND status='pending'");
    $stmt->bind_param("sisi", $admin_name_admin, $confirm_id, $confirm_month, $confirm_year);
    $stmt->execute();
    $stmt->close();

    $_SESSION['message'] = "Payment status updated to paid for $confirm_month $confirm_year.";
    header("Location: admin.php?year=$confirm_year&page=$current_page_admin");
    exit();
  }
}
?>






<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <title>adminpage</title>

    <style>
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 9999; /* Ensure it stays on top */
        }


      .navbar-collapse {
       background-color: #192F59; /* Matches the navbar color */
       color: white;
       }
      /* Custom Styles */
      .navbar {
        background-color: #192F59; /* Dark blue background */
        height: 80px;
      }
      .navbar-brand {
        color: white !important;
        font-weight: bold;
        font-size: 1.2em;
        position: relative;
      }
      .navbar-nav .nav-link {
        color: white !important;
        font-weight: 500;
        padding: 10px 20px;
        transition: background-color 0.3s ease;
      }
      .navbar-nav .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.15);
      }
      .navbar-toggler-icon {
        filter: invert(1);
      }
      .navbar-nav .nav-item.active .nav-link {
        background-color: rgba(97, 218, 251, 0.2);
      }
      
      /* Mobile Side Nav Styles */
      @media (max-width: 991.98px) {
        .navbar-toggler {
          width: calc(100% - 30px);
          margin: 10px auto;
          background-color: #8b5cf6;
          border: none;
          border-radius: 6px;
          padding: 8px 0;
          display: flex;
          justify-content: center;
          align-items: center;
        }
        .navbar-toggler:focus {
          outline: none;
          box-shadow: none;
        }
        .navbar-toggler-icon {
          filter: none !important;
        }
        #navbarSupportedContent {
          position: fixed;
          top: 0;
          right: -300px; /* Start hidden on the right */
          width: 300px;
          height: 100vh;
          background-color: #192F59; /* Matching the navbar color */
          transition: right 0.4s ease;
          display: block !important; /* Override bootstrap collapse */
          z-index: 10000;
          padding-top: 60px;
          box-shadow: -5px 0 15px rgba(0,0,0,0.5);
          overflow-y: auto;
        }
        #navbarSupportedContent.openSideNav {
          right: 0; /* Slide in */
        }
        .navbar-nav {
          padding: 0 20px;
        }
        .navbar-nav .nav-item {
          border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .navbar-nav .nav-link {
          color: #fff !important;
          font-size: 14px;
          font-weight: 600;
          letter-spacing: 1px;
          padding: 15px 0 !important;
          text-transform: uppercase;
        }
        .dropdown-menu {
          background-color: #122345;
          border: none;
          margin-top: 0;
        }
        .dropdown-item {
          color: #ddd;
          border-bottom: 1px solid rgba(255, 255, 255, 0.1);
          padding: 10px 15px;
        }
        .dropdown-item:hover {
          background-color: #213F7A;
          color: #fff;
        }
        .btn-admin-login {
          margin: 20px;
          display: inline-block;
          text-align: center;
        }
        /* Mobile close button */
        .btn-close-offcanvas {
          display: block !important;
          position: absolute;
          top: 15px;
          left: 20px;
          background: transparent;
          border: none;
          color: #888;
          font-size: 32px;
          line-height: 1;
          cursor: pointer;
          padding: 0;
        }
        .btn-close-offcanvas:hover {
           color: #fff;
        }
      }
      .btn-close-offcanvas {
        display: none;
      }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


  </head>
  <body>

    <nav class="navbar navbar-expand-lg navbar-dark">
      
      <button class="navbar-toggler" type="button" onclick="document.getElementById('navbarSupportedContent').classList.add('openSideNav')">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <!-- Close button visible only on mobile -->
        <button class="btn-close-offcanvas" type="button" onclick="document.getElementById('navbarSupportedContent').classList.remove('openSideNav')">
          &times;
        </button>
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="#" data-page="memberlist_and_payment.php"> ACCOUNTS  <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#" data-page="contribution_current_month.php">MONTHWISE CONTRIBUTION</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#" data-page="adduser.php">ADD MEMBER</a>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              OTHERS
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="#" data-page="expense.php">ADD EXPENSE</a>
              <a class="dropdown-item" href="#" data-page="view_expenses.php">VIEW ALL EXPENSES</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#" data-page="analytics.php">ANALYTICS</a>
            </div>
          </li>
          
        </ul>
        <div class="d-flex align-items-center" style="padding-right: 40px;">
         

         	 <form method="get">
    		<button name="logout" value="1" class="btn btn-danger my-2 my-sm-0" type="submit">
       		 Logout
   			 </button>
			</form>
            
        </div>

        
      </div>
    </nav>

    

     <div id="content" class="container-fluid" style="margin-top: 80px; padding: 0;">
    <!-- Default content load (HomePage.php) -->
    <?php include 'memberlist_and_payment.php'; ?>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
      

    <script>
        // Attach click listeners to navbar links
        document.querySelectorAll('a.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                let page = this.getAttribute('data-page');
                if (!page) return;

                // Optional: highlight active link
                document.querySelectorAll('a.nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');

                // Fetch the content of the page and load into #content div
                fetch(page)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not OK');
                    return response.text();
                })
                .then(html => {
                    document.getElementById('content').innerHTML = html;
                })
                .catch(error => {
                    document.getElementById('content').innerHTML = '<p class="text-danger">Failed to load page.</p>';
                    console.error('Error loading page:', error);
                });
            });
        });

        // Attach click listeners to dropdown items
        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                let page = this.getAttribute('data-page');
                if (!page) return;

                // Optional: highlight active dropdown item
                document.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');

                // Fetch the content of the page and load into #content div
                fetch(page)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not OK');
                    return response.text();
                })
                .then(html => {
                    document.getElementById('content').innerHTML = html;
                })
                .catch(error => {
                    document.getElementById('content').innerHTML = '<p class="text-danger">Failed to load page.</p>';
                    console.error('Error loading page:', error);
                });
            });
        });
    </script>

    <script>
        // Handle monthYearForm submission
        document.addEventListener('submit', function(e) {
            if (e.target.matches('#monthYearForm')) {
                e.preventDefault(); // stop normal page reload
                const formData = new FormData(e.target);
                const query = new URLSearchParams(formData).toString();

                fetch('contribution_current_month.php?' + query)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('content').innerHTML = html;
                })
                .catch(err => {
                    console.error('Error loading month data:', err);
                    document.getElementById('content').innerHTML = '<p class="text-danger">Failed to load data.</p>';
                });
            }

            // Handle expenseForm submission (dynamically loaded)
            if (e.target.matches('#expenseForm')) {
                e.preventDefault();
                
                const formData = new FormData(e.target);
                
                fetch('expense.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(html => {
                    // Replace the content div with the updated page
                    document.getElementById('content').innerHTML = html;
                })
                .catch(error => {
                    console.error('Error submitting form:', error);
                    alert('Failed to submit expense. Please try again.');
                });
            }

            // Handle addUserForm submission (dynamically loaded)
            if (e.target.matches('#addUserForm')) {
                e.preventDefault();
                
                const formData = new FormData(e.target);
                
                fetch('adduser.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(html => {
                    // Replace the content div with the updated page
                    document.getElementById('content').innerHTML = html;
                })
                .catch(error => {
                    console.error('Error submitting form:', error);
                    alert('Failed to add member. Please try again.');
                });
            }
        });
    </script>

    <script>
document.addEventListener('click', function(e) {
    const target = e.target.closest('a.nav-link, .dropdown-item');
    if (!target) return;

    const page = target.getAttribute('data-page');
    if (!page) return;

    const content = document.getElementById('content');

    // Function to load analytics and attach listener
    function loadAnalytics(year) {
        fetch('analytics.php' + (year ? '?year=' + year : ''))
            .then(response => response.text())
            .then(html => {
    content.innerHTML = html;

    // ✅ Re-run scripts inside dynamically loaded analytics.php (important for charts)
    content.querySelectorAll("script").forEach(oldScript => {
        const newScript = document.createElement("script");
        if (oldScript.src) {
            newScript.src = oldScript.src;
        } else {
            newScript.textContent = oldScript.textContent;
        }
        document.body.appendChild(newScript);
        oldScript.remove();
    });

    // Reattach listener for year dropdown
    const yearSelect = content.querySelector('#yearSelect');
    if (yearSelect) {
        yearSelect.addEventListener('change', function() {
            loadAnalytics(this.value);
        });
    }
})

            .catch(err => {
                console.error('Analytics reload error:', err);
                content.innerHTML = '<p class="text-danger">Failed to load analytics data.</p>';
            });
    }

    // If Analytics page clicked
    if (page.includes('analytics.php')) {
        e.preventDefault(); // prevent default link action
        loadAnalytics();    // initial load
    }
});
</script>

<script>
// Auto-close mobile side nav when a link is clicked
document.querySelectorAll('.nav-link:not(.dropdown-toggle), .dropdown-item').forEach(item => {
    item.addEventListener('click', () => {
        const navContent = document.getElementById('navbarSupportedContent');
        if (navContent && navContent.classList.contains('openSideNav')) {
            navContent.classList.remove('openSideNav');
        }
    });
});
</script>

  </body>
</html>