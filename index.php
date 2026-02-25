<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <title>Yousuf Ali Munshi Bari</title>

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
        line-height: 1.2;
        margin-right: auto;
      }
      .navbar-brand-text {
        display: block;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.5px;
      }
      .btn-donate {
        background-color: #D4A043;
        color: #192F59 !important;
        border: none;
        font-weight: 700;
        padding: 8px 18px;
        border-radius: 4px;
        font-size: 0.95em;
        transition: background-color 0.3s ease, transform 0.2s ease;
        text-decoration: none !important;
        margin: 0 10px;
        display: none;
      }
      .btn-donate:hover {
        background-color: #C99A3E;
        transform: scale(1.03);
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
      .btn-admin-login {
        color: #192F59 !important;
        background-color: #ffffff;
        border: none;
        font-weight: 600;
        padding: 8px 18px;
        border-radius: 4px;
        font-size: 0.9em;
        transition: background-color 0.3s ease, transform 0.2s ease;
        text-decoration: none !important;
      }
      .btn-admin-login:hover {
        background-color: #d0e4f7;
        transform: scale(1.03);
      }
      
      /* Mobile Side Nav Styles */
      @media (max-width: 991.98px) {
        .navbar {
          display: flex;
          justify-content: space-between;
          align-items: center;
        }
        .navbar-brand {
          font-size: 14px;
          margin-right: auto;
          flex-grow: 0;
        }
        .navbar-toggler {
          position: absolute;
          right: 15px;
          width: auto;
          margin: 0;
          background-color: transparent;
          border: none;
          border-radius: 0;
          padding: 0;
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
          background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba(255,255,255, 0.55)' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E") !important;
          background-repeat: no-repeat;
          display: inline-block;
          vertical-align: middle;
          content: "";
          width: 1.5em;
          height: 1.5em;
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
      @media (min-width: 992px) {
        /* Donate button is hidden */
      }
      .btn-close-offcanvas {
        display: none;
      }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


  </head>
  <body>

    <nav class="navbar navbar-expand-lg navbar-dark">
      <a class="navbar-brand" href="#">
        <span class="navbar-brand-text">Yousuf Ali Bari</span>
        <span class="navbar-brand-text">Foundation</span>
      </a>
      
      <button class="navbar-toggler" type="button" onclick="document.getElementById('navbarSupportedContent').classList.add('openSideNav')">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <!-- Close button visible only on mobile -->
        <button class="btn-close-offcanvas" type="button" onclick="document.getElementById('navbarSupportedContent').classList.remove('openSideNav')">
          &times;
        </button>
        <ul class="navbar-nav mr-auto">
          
          <li class="nav-item">
            <a class="nav-link" href="#" data-page="contribution_current_month.php">মাসিক কন্ট্রিবিউশন</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#" data-page="view_expenses.php">আর্থিক সারসংক্ষেপ</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#" data-page="donate.php">অর্থদান</a>
          </li>
         

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              অন্যান্য
            </a>
            
             <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="#" data-page="about.php">আমাদের সম্পর্কে</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#" data-page="analytics.php">Analytics</a>
            </div>
          </li>
          
        </ul>
     
        <!-- Admin Login button on the right -->
        <div class="d-flex align-items-center" style="padding: 20px; border-top: 1px solid rgba(255, 255, 255, 0.1);">
          <a href="login.php" class="btn-admin-login" style="margin: 0; display: block; width: 100%; text-align: center;">🔐 Admin Login</a>
        </div>

        
      </div>
    </nav>

    

     <div id="content" class="container-fluid" style="margin-top: 80px; padding: 0;">
    <!-- Default content load -->
    <?php include 'contribution_current_month.php'; ?>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
      

    <script>
        // Generic loader for pages into #content (also re-runs any scripts inside)
        function loadPage(page) {
            const content = document.getElementById('content');
            if (!content || !page) return;

            fetch(page)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not OK');
                    return response.text();
                })
                .then(html => {
                    content.innerHTML = html;

                    // Re-run any scripts that came with the loaded page (needed for analytics/charts)
                    content.querySelectorAll('script').forEach(oldScript => {
                        const newScript = document.createElement('script');
                        if (oldScript.src) {
                            newScript.src = oldScript.src;
                        } else {
                            newScript.textContent = oldScript.textContent;
                        }
                        document.body.appendChild(newScript);
                        oldScript.remove();
                    });
                })
                .catch(error => {
                    content.innerHTML = '<p class="text-danger">Failed to load page.</p>';
                    console.error('Error loading page:', error);
                });
        }

        // Handle navbar and dropdown clicks via event delegation
        document.addEventListener('click', function(e) {
            const target = e.target.closest('a.nav-link, .dropdown-item');
            if (!target) return;

            const page = target.getAttribute('data-page');
            if (!page) return;

            e.preventDefault();

            // Highlight active main nav link
            if (target.classList.contains('nav-link')) {
                document.querySelectorAll('a.nav-link').forEach(l => l.classList.remove('active'));
                target.classList.add('active');
            }

            // Auto-close mobile side nav if open
            const navContent = document.getElementById('navbarSupportedContent');
            if (navContent && navContent.classList.contains('openSideNav')) {
                navContent.classList.remove('openSideNav');
            }

            loadPage(page);
        });

        // Handle year change for analytics when loaded inside index.php
        document.addEventListener('change', function(e) {
            if (e.target && e.target.id === 'yearSelect') {
                const year = e.target.value;
                loadPage('analytics.php?year=' + encodeURIComponent(year));
            }
        });
    </script>

    <script>
        // Handle monthYearForm and other dynamic form submissions
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

  </body>
</html>
