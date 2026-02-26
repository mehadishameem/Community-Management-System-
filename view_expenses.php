<?php


include 'partials/_dbconnect2.php';

// Fetch all expenses ordered by date (newest first)
$sql = "SELECT * FROM expenses ORDER BY date DESC, id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Expenses</title>
    <link rel="stylesheet" href="memberlist_and_payment.css">
    <style>
        .expenses-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .expenses-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .expenses-header h2 {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .financial-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            max-width: 900px;
            margin: 0 auto;
        }

        .summary-card {
            padding: 20px 25px;
            border-radius: 12px;
            color: white;
            font-weight: 700;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .summary-card:hover {
            transform: translateY(-3px);
        }

        .summary-label {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-amount {
            font-size: 1.8rem;
            font-weight: 800;
        }

        .card-contribution {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .card-expenses {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .card-surplus {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .card-deficit {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .expenses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }

        .expense-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border-left: 5px solid #6366f1;
            position: relative;
            overflow: hidden;
        }

        .expense-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(99, 102, 241, 0.25);
        }

        .expense-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(168, 85, 247, 0.1) 100%);
            border-radius: 0 0 0 100%;
        }

        .expense-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #192F59;
            margin-bottom: 15px;
            position: relative;
        }

        .expense-details {
            position: relative;
        }

        .expense-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .expense-row:last-child {
            border-bottom: none;
        }

        .expense-label {
            font-weight: 600;
            color: #6b7280;
            font-size: 0.95rem;
        }

        .expense-value {
            color: #1f2937;
            font-weight: 500;
        }

        .expense-cost {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .expense-description {
            margin-top: 15px;
            padding: 12px;
            background: #f9fafb;
            border-radius: 8px;
            color: #4b5563;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .pdf-link {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-align: center;
        }

        .pdf-link:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.45);
            color: white;
            text-decoration: none;
        }

        .no-expenses {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .no-expenses h3 {
            color: #6b7280;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .no-expenses p {
            color: #9ca3af;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .expense-card {
            animation: fadeIn 0.5s ease backwards;
        }

        .expense-card:nth-child(1) { animation-delay: 0.1s; }
        .expense-card:nth-child(2) { animation-delay: 0.2s; }
        .expense-card:nth-child(3) { animation-delay: 0.3s; }
        .expense-card:nth-child(4) { animation-delay: 0.4s; }
        .expense-card:nth-child(5) { animation-delay: 0.5s; }
        .expense-card:nth-child(6) { animation-delay: 0.6s; }

        .expense-date {
            color: #6366f1;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .expenses-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <div class="expenses-container">
        <div class="expenses-header">
            <h2>📊 আর্থিক সারসংক্ষেপ</h2>
            
            <?php
            // Calculate total contributions
            $contributionSql = "SELECT SUM(amount) as total FROM payment";
            $contributionResult = $conn->query($contributionSql);
            $contributionRow = $contributionResult->fetch_assoc();
            $totalContribution = $contributionRow['total'] ?? 0;

            // Calculate total expenses
            $totalSql = "SELECT SUM(total_cost) as total FROM expenses";
            $totalResult = $conn->query($totalSql);
            $totalRow = $totalResult->fetch_assoc();
            $totalExpenses = $totalRow['total'] ?? 0;

            // Calculate balance (surplus or deficit)
            $balance = $totalContribution - $totalExpenses;
            $isSurplus = $balance >= 0;
            ?>
            
            <div class="financial-summary">
                <div class="summary-card card-contribution">
                    <div class="summary-label">💰 মোট সংগ্রহকৃত চাঁদা</div>
                    <div class="summary-amount">৳<?= number_format($totalContribution, 2) ?></div>
                </div>

                <div class="summary-card card-expenses">
                    <div class="summary-label">📉 মোট ব্যয়</div>
                    <div class="summary-amount">৳<?= number_format($totalExpenses, 2) ?></div>
                </div>

                <div class="summary-card <?= $isSurplus ? 'card-surplus' : 'card-deficit' ?>">
                    <div class="summary-label"><?= $isSurplus ? '📈 উদ্বৃত্ত' : '⚠️ ঘাটতি' ?></div>
                    <div class="summary-amount">৳<?= number_format(abs($balance), 2) ?></div>
                </div>
            </div>
        </div>

        <?php if ($result && $result->num_rows > 0): ?>
            <div class="expenses-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="expense-card">
                        <div class="expense-title">
                            <?= htmlspecialchars($row['title']) ?>
                        </div>
                        
                        <div class="expense-details">
                            <div class="expense-row">
                                <span class="expense-label">📅 Date:</span>
                                <span class="expense-value expense-date">
                                    <?= date('d M Y', strtotime($row['date'])) ?>
                                </span>
                            </div>
                            
                            <div class="expense-row">
                                <span class="expense-label">💰 Total Cost:</span>
                                <span class="expense-cost">
                                    ৳<?= number_format($row['total_cost'], 2) ?>
                                </span>
                            </div>
                            
                            <div class="expense-row">
                                <span class="expense-label">👤 Uploaded By:</span>
                                <span class="expense-value">
                                    <?= htmlspecialchars($row['uploaded_by']) ?>
                                </span>
                            </div>
                        </div>

                        <?php if (!empty($row['description'])): ?>
                            <div class="expense-description">
                                <strong>📝 Description:</strong><br>
                                <?= nl2br(htmlspecialchars($row['description'])) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($row['pdf_path'])): ?>
                            <a href="<?= htmlspecialchars($row['pdf_path']) ?>" 
                               target="_blank" 
                               class="pdf-link">
                               📄 View PDF Document →
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-expenses">
                <h3>📭 No Expenses Found</h3>
                <p>Start by adding your first expense!</p>
            </div>
        <?php endif; ?>
    </div>

    <?php $conn->close(); ?>

</body>
</html>