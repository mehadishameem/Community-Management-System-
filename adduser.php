<?php
session_start();
include 'partials/_dbconnect2.php';




if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    if ($name === '') {
        $error = 'Please enter a name.';
    } else {
        $stmt = $conn->prepare("INSERT INTO member (name) VALUES (?)");
        $stmt->bind_param("s", $name);
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
    .add-member-form {
        max-width: 480px;
        margin: 20px auto;
        padding: 20px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .add-member-form h3 {
        color: #192F59;
        text-align: center;
        margin-bottom: 20px;
    }
    .add-member-form input[type="text"] {
        width: 100%;
        padding: 10px 15px;
        font-size: 1.1rem;
        margin-bottom: 15px;
        border: 1.5px solid #192F59;
        border-radius: 8px;
    }
    .add-member-form button {
        background-color: #192F59;
        color: white;
        font-weight: 600;
        font-size: 1.1rem;
        padding: 10px 0;
        width: 100%;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .add-member-form button:hover {
        background-color: #2563eb;
    }
    .message {
        margin-bottom: 15px;
        padding: 10px;
        border-radius: 5px;
        font-weight: 600;
    }
    .success {
        color: #2e7d32;
        background-color: #edf7ed;
    }
    .error {
        color: #d32f2f;
        background-color: #fde8e8;
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
        <input type="text" name="name" placeholder="Enter member name" required />
        <button type="submit">Add Member</button>
    </form>
</div>