<?php
session_start();

// Only logged-in users can access (typically admin via admin.php)
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic validation
    if (!isset($_FILES['visual_file']) || $_FILES['visual_file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please choose an image file to upload.';
    } else {
        $targetDir = __DIR__ . DIRECTORY_SEPARATOR . 'visuals' . DIRECTORY_SEPARATOR;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $originalName = basename($_FILES['visual_file']['name']);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed, true)) {
            $error = 'Only JPG, JPEG, PNG or WEBP images are allowed.';
        } else {
            $safeName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $originalName);
            $newName = time() . '_' . $safeName;
            $targetPath = $targetDir . $newName;

            if (move_uploaded_file($_FILES['visual_file']['tmp_name'], $targetPath)) {
                $relativePath = 'visuals/' . $newName;
                $message = '✅ Image uploaded successfully to "' . $relativePath . '". It will rotate on the home page banner.';
            } else {
                $error = '❌ Failed to move uploaded file.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Visuals</title>
    <link rel="stylesheet" href="memberlist_and_payment.css">
    <style>
        .visual-upload-wrapper {
            max-width: 700px;
            margin: 20px auto;
            padding: 24px 20px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.18);
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .visual-upload-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #192F59;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .visual-upload-subtitle {
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 18px;
        }

        .visual-upload-form input[type="file"] {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            font-size: 0.9rem;
            background: #f9fafb;
        }

        .visual-upload-form input[type="file"]:focus {
            border-color: #6366f1;
            outline: none;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.25);
        }

        .visual-upload-button {
            margin-top: 14px;
            width: 100%;
            padding: 10px 0;
            border-radius: 999px;
            border: none;
            cursor: pointer;
            font-weight: 700;
            font-size: 0.95rem;
            background: #facc15;
            color: #1f2937;
            box-shadow: 0 4px 14px rgba(250, 204, 21, 0.4);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .visual-upload-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(250, 204, 21, 0.55);
        }

        .visual-msg,
        .visual-error {
            margin-bottom: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .visual-msg {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }

        .visual-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        .visual-hint {
            margin-top: 10px;
            font-size: 0.8rem;
            color: #6b7280;
        }
    </style>
</head>
<body>

<div class="visual-upload-wrapper">
    <div class="visual-upload-title">
        🖼 Home Page Visuals
    </div>
    <div class="visual-upload-subtitle">
        এখানে আপলোড করা ছবিগুলো হোম পেজের উপরের ভিজ্যুয়াল স্লাইডে একের পর এক ঘুরে ঘুরে দেখানো হবে।
    </div>

    <?php if ($message): ?>
        <div class="visual-msg"><?= htmlspecialchars($message) ?></div>
    <?php elseif ($error): ?>
        <div class="visual-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form id="visualUploadForm" class="visual-upload-form" method="POST" enctype="multipart/form-data">
        <label style="font-weight:600; font-size:0.9rem; color:#374151; margin-bottom:6px; display:block;">
            একটি ইমেজ ফাইল নির্বাচন করুন (JPG, PNG, WEBP):
        </label>
        <input type="file" name="visual_file" accept="image/png,image/jpeg,image/webp" required>

        <button type="submit" class="visual-upload-button">
            ছবি আপলোড করুন
        </button>

        <div class="visual-hint">
            টিপস: ১৬:৯ অনুপাতের হাই রেজুলেশন ছবি ব্যবহার করলে হোম পেজে ভালো দেখাবে।
        </div>
    </form>
</div>

</body>
</html>

