<?php
session_start();
include 'partials/_dbconnect2.php';




if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $number = trim($_POST['number'] ?? '');
    $type = trim($_POST['type'] ?? 'general');
    $address = trim($_POST['address'] ?? '');

    if ($name === '') {
        $error = 'Please enter a name.';
    } else {
        $stmt = $conn->prepare("INSERT INTO member (name, number, type, address) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $number, $type, $address);
        if ($stmt->execute()) {
            $success = "Member '$name' added successfully.";
        } else {
            $error = "Database error: " . htmlspecialchars($stmt->error);
        }
        $stmt->close();
    }
}
?>

<style>
    /* Add to your existing admin.css or style tag */
    :root {
        --primary: #192F59;
        --accent: #D4A043;
        --light-bg: #f3f4f6;
    }

    .add-member-form {
        max-width: 600px;
        margin: 40px auto 20px;
        padding: 30px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-top: 5px solid var(--accent);
    }
    .add-member-form h3 {
        color: var(--primary);
        text-align: center;
        margin-bottom: 25px;
        font-size: 1.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--primary), #2563eb);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .form-group {
        margin-bottom: 18px;
    }
    .form-group label {
        display: block;
        color: var(--primary);
        font-weight: 600;
        margin-bottom: 8px;
        font-size: 0.95rem;
        position: relative;
        padding-left: 8px;
    }
    .form-group label::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 70%;
        background: var(--accent);
        border-radius: 2px;
    }
    .add-member-form input[type="text"],
    .add-member-form input[type="tel"],
    .add-member-form select {
        width: 100%;
        padding: 12px 15px;
        font-size: 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-family: inherit;
        transition: all 0.3s;
        background: white;
    }
    .add-member-form input[type="text"]:focus,
    .add-member-form input[type="tel"]:focus,
    .add-member-form select:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(212, 160, 67, 0.15);
        background: #fffef5;
    }
    .add-member-form select {
        cursor: pointer;
        background-color: white;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23192F59' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 35px;
    }
    .add-member-form button {
        background: linear-gradient(135deg, var(--primary) 0%, #2563eb 100%);
        color: white;
        font-weight: 600;
        font-size: 1rem;
        padding: 12px 0;
        width: 100%;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 10px;
        box-shadow: 0 4px 12px rgba(25, 47, 89, 0.2);
        position: relative;
        overflow: hidden;
    }
    .add-member-form button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    .add-member-form button:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        box-shadow: 0 6px 20px rgba(25, 47, 89, 0.35);
        transform: translateY(-2px);
    }
    .add-member-form button:hover::before {
        left: 100%;
    }
    .message {
        margin-bottom: 20px;
        padding: 12px 15px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        border-left: 4px solid;
    }
    .success {
        color: #1b5e20;
        background-color: #e8f5e9;
        border-left-color: #4caf50;
    }
    .error {
        color: #b71c1c;
        background-color: #ffebee;
        border-left-color: #f44336;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .add-member-form {
            max-width: 100%;
            margin: 20px 16px;
            padding: 20px 16px;
        }
        .add-member-form h3 {
            font-size: 1.25rem;
            margin-bottom: 20px;
        }
        .form-group label {
            font-size: 0.9rem;
        }
        .add-member-form input[type="text"],
        .add-member-form select {
            font-size: 16px;
            padding: 10px 12px;
        }
        .add-member-form button {
            padding: 10px 0;
            font-size: 0.95rem;
        }
    }

    @media (max-width: 480px) {
        .add-member-form {
            margin: 15px 12px;
            padding: 16px 12px;
        }
        .add-member-form h3 {
            font-size: 1.1rem;
            margin-bottom: 18px;
        }
    }
</style>

<div class="add-member-form">
    <h3>Add New Member</h3>

    <?php if (!empty($success)): ?>
        <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form id="addUserForm" method="POST" action="adduser.php">
        <div class="form-group">
            <label for="name">Member Name *</label>
            <input type="text" id="name" name="name" placeholder="Enter member name" required />
        </div>

        <div class="form-group">
            <label for="number">Phone Number</label>
            <input type="tel" id="number" name="number" placeholder="Enter phone number" pattern="[0-9\-+\s()]*" />
        </div>

        <div class="form-group">
            <label for="type">Member Type</label>
            <select id="type" name="type">
                <option value="general" selected>General</option>
                <option value="permanent">Permanent</option>
            </select>
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" id="address" name="address" placeholder="Enter address" />
        </div>

        <button type="submit">Add Member</button>
    </form>
</div>

<script>
    // Restrict phone number field to only numbers and formatting characters
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.getElementById('number');
        if (phoneInput) {
            // Prevent non-numeric characters
            phoneInput.addEventListener('keypress', function(e) {
                const char = String.fromCharCode(e.which);
                if (!/[0-9+\-() ]/.test(char)) {
                    e.preventDefault();
                }
            });
            
            // Also clean up on paste
            phoneInput.addEventListener('paste', function(e) {
                e.preventDefault();
                const text = (e.clipboardData || window.clipboardData).getData('text');
                const cleaned = text.replace(/[^0-9+\-() ]/g, '');
                this.value = cleaned;
            });
        }
    });
</script>