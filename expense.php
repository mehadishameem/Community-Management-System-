<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'partials/_dbconnect2.php';

    $title = $_POST['title'];
    $date = $_POST['date'];
    $description = $_POST['description'];
    $uploaded_by = $_SESSION['username']; // Get logged in username
    $total_cost = $_POST['total_cost'];

    // File upload setup
    $targetDir = "uploads/expenses/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = basename($_FILES["pdf_file"]["name"]);
    $targetFilePath = $targetDir . time() . "_" . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    if ($fileType != "pdf") {
        $error = "Only PDF files are allowed.";
    } else {
        if (move_uploaded_file($_FILES["pdf_file"]["tmp_name"], $targetFilePath)) {
            $stmt = $conn->prepare("INSERT INTO expenses (title, date, description, total_cost, pdf_path, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $title, $date, $description, $total_cost, $targetFilePath, $uploaded_by);

            if ($stmt->execute()) {
                $message = "✅ Expense added successfully!";
            } else {
                $error = "Database error: " . $conn->error;
            }

            $stmt->close();
        } else {
            $error = "❌ File upload failed.";
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Expense</title>
    <link rel="stylesheet" href="memberlist_and_payment.css">
    <style>
        /* Add a nice glassy box for the form */
        .expense-form {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 800px;
            margin: 0 auto;
            animation: fadeIn 0.8s ease;
        }

        .expense-form h2 {
            text-align: center;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .expense-form input[type="text"],
        .expense-form input[type="number"],
        .expense-form input[type="date"],
        .expense-form textarea,
        .expense-form input[type="file"] {
            width: 100%;
            padding: 12px 14px;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            margin-bottom: 15px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .expense-form input:focus,
        .expense-form textarea:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            outline: none;
        }

        .expense-form button {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            font-weight: 700;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
        }

        .expense-form button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(99, 102, 241, 0.45);
        }

        .message, .error {
            max-width: 800px;
            margin: 15px auto;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            font-weight: 600;
        }

        .message {
            background-color: #d1fae5;
            color: #065f46;
            border: 2px solid #10b981;
        }

        .error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 2px solid #ef4444;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="expense-form">
        <h2>💰 Add New Expense</h2>

        <?php if (!empty($message)): ?>
            <div class="message"><?= $message ?></div>
        <?php elseif (!empty($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form id="expenseForm" method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Expense Title" required>
            
            <input type="date" name="date" required>
            
            <input type="number" name="total_cost" placeholder="Total Cost (৳)" step="0.01" required>

            <textarea name="description" placeholder="Description" rows="4"></textarea>

            <label style="font-weight:600; color:#2d3748;">Upload PDF Document:</label>
            <input type="file" name="pdf_file" accept="application/pdf" required>

            <button type="submit">Save Expense</button>
        </form>
    </div>

</body>
</html>