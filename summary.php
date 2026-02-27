<?php
$visualImages = glob(__DIR__ . '/visuals/*.{jpg,jpeg,png,webp}', GLOB_BRACE);
// Convert absolute paths to relative for use in src attributes
$visualImagesRelative = [];
if ($visualImages) {
    foreach ($visualImages as $path) {
        $visualImagesRelative[] = 'visuals/' . basename($path);
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yousuf Ali Bari Foundation – Summary</title>

    <style>
        :root {
            --primary: #192F59;
            --accent: #D4A043;
            --light-bg: #f3f4f6;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: var(--light-bg);
            color: #111827;
        }

        .summary-wrapper {
            margin-top: 0;
        }

        .hero {
            background: radial-gradient(circle at top left, #314b8d, #192F59);
            color: #fff;
            padding: 40px 16px 32px;
        }

        .hero-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: minmax(0, 3fr) minmax(0, 2fr);
            gap: 32px;
            align-items: center;
        }

        .hero-title {
            font-size: 2.1rem;
            line-height: 1.3;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .hero-subtitle {
            font-size: 1rem;
            line-height: 1.7;
            opacity: 0.95;
            margin-bottom: 18px;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(212, 160, 67, 0.12);
            border: 1px solid rgba(250, 204, 21, 0.35);
            font-size: 0.8rem;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-primary-hero,
        .btn-ghost-hero {
            border-radius: 999px;
            padding: 8px 18px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-primary-hero {
            background: #facc15;
            color: #1f2937;
            box-shadow: 0 6px 18px rgba(0,0,0,0.25);
        }

        .btn-ghost-hero {
            background: transparent;
            color: #e5e7eb;
            border: 1px solid rgba(229, 231, 235, 0.4);
        }

        .hero-image-card {
            background: rgba(15, 23, 42, 0.9);
            border-radius: 18px;
            padding: 5px 5px 5px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.7);
            position: relative;
            overflow: hidden;
        }

        .hero-image {
            width: 100%;
            border-radius: 14px;
            display: block;
            object-fit: cover;
            max-height: 260px;
        }

        .hero-image-caption {
            margin-top: 10px;
            font-size: 0.85rem;
            color: #e5e7eb;
        }

        .impact-strip {
            background: #f9fafb;
            padding: 18px 16px 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .impact-grid {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 14px;
        }

        .impact-card {
            background: #fff;
            border-radius: 12px;
            padding: 12px 14px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
            border-left: 3px solid var(--primary);
        }

        .impact-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .impact-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #111827;
        }

        .main-content {
            max-width: 1100px;
            margin: 24px auto 40px;
            padding: 0 16px 4px;
            display: grid;
            grid-template-columns: minmax(0, 3fr) minmax(0, 2fr);
            gap: 24px;
        }

        .section {
            margin-bottom: 24px;
            background: #fff;
            border-radius: 16px;
            padding: 18px 18px 16px;
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.06);
        }

        .section-header {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-header span.icon {
            width: 26px;
            height: 26px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(212, 160, 67, 0.15);
            font-size: 0.9rem;
        }

        .section-body {
            font-size: 0.95rem;
            line-height: 1.7;
            color: #374151;
        }

        .story-timeline {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .story-step {
            position: relative;
            padding-left: 26px;
            margin-bottom: 10px;
        }

        .story-step::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 4px;
            width: 4px;
            height: 4px;
            border-radius: 999px;
            background: var(--primary);
        }

        .story-step-title {
            font-weight: 600;
            margin-bottom: 2px;
        }

        .story-step-text {
            font-size: 0.9rem;
            color: #4b5563;
        }

        .list-check {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .list-check li {
            display: flex;
            align-items: flex-start;
            gap: 6px;
            margin-bottom: 6px;
            font-size: 0.9rem;
        }

        .list-check span.bullet {
            font-size: 0.9rem;
            color: #16a34a;
            margin-top: 2px;
        }

        .highlight-box {
            background: #fefce8;
            border-radius: 12px;
            padding: 10px 10px 8px;
            border: 1px solid #facc15;
            font-size: 0.9rem;
            color: #854d0e;
        }

        .updates-list {
            list-style: none;
            padding: 0;
            margin: 0;
            font-size: 0.9rem;
        }

        .updates-list li {
            padding: 6px 0;
            border-bottom: 1px dashed #e5e7eb;
        }

        .updates-list li:last-child {
            border-bottom: none;
        }

        .update-tag {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 999px;
            font-size: 0.7rem;
            background: #ecfdf5;
            color: #15803d;
            margin-right: 6px;
        }

        /* Page cards for quick navigation */
        .page-links-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 10px;
            margin-top: 12px;
        }

        .page-card {
            background: #f9fafb;
            border-radius: 12px;
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
        }

        .page-card-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }

        .page-card-text {
            font-size: 0.85rem;
            color: #4b5563;
            margin-bottom: 8px;
        }

        .page-card-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border-radius: 999px;
            padding: 5px 12px;
            font-size: 0.8rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            background: #facc15;
            color: #1f2937;
            text-decoration: none;
        }

        .page-card-btn span.arrow {
            font-size: 0.9rem;
        }

        /* Donation & contact section */
        .donation-contact {
            background: #0f172a;
            color: #e5e7eb;
            padding: 28px 16px 20px;
            margin-top: 8px;
        }

        .donation-inner {
            max-width: 1100px;
            margin: 0 auto;
        }

        .donation-header-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #f9fafb;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .donation-header-text {
            font-size: 0.9rem;
            color: #9ca3af;
            margin-bottom: 14px;
        }

        .donation-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 14px;
        }

        .donation-card {
            background: #020617;
            border-radius: 14px;
            padding: 14px 14px 12px;
            border: 1px solid rgba(148, 163, 184, 0.45);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.6);
        }

        .donation-card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
        }

        .donation-logo {
            width: 40px;
            height: 40px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #0b1120;
        }

        .donation-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .donation-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 9px;
            border-radius: 999px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            background: rgba(148, 163, 184, 0.15);
            color: #e5e7eb;
            margin-bottom: 6px;
        }

        .donation-card-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 6px;
            color: #f9fafb;
        }

        .donation-card-body {
            font-size: 0.85rem;
            color: #cbd5f5;
            line-height: 1.6;
        }

        .donation-rows {
            margin-top: 4px;
        }

        .donation-row-label {
            font-weight: 600;
            color: #e5e7eb;
        }

        .donation-row-text {
            color: #cbd5e1;
        }

        .donation-cta-row {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .donation-cta {
            border-radius: 999px;
            padding: 6px 14px;
            border: none;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .donation-cta.primary {
            background: #facc15;
            color: #0f172a;
        }

        .donation-cta.outline {
            background: transparent;
            color: #e5e7eb;
            border: 1px solid rgba(148, 163, 184, 0.8);
        }

        .donation-cta-icon {
            font-size: 0.9rem;
        }

        /* Footer */
        .site-footer {
            background: #020617;
            color: #9ca3af;
            padding: 10px 16px 14px;
            border-top: 1px solid #111827;
        }

        .footer-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            font-size: 0.8rem;
        }

        .footer-links {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .footer-link {
            color: #e5e7eb;
            text-decoration: none;
            font-weight: 500;
        }

        .footer-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 900px) {
            .hero-inner {
                grid-template-columns: minmax(0, 1fr);
            }

            .main-content {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        @media (max-width: 640px) {
            .hero {
                padding-top: 28px;
            }

            .hero-title {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
<div class="summary-wrapper">
    <section class="hero" id="top">
        <div class="hero-inner">
            <div>
                <div class="hero-tag">
                    🌱 ছোট উদ্যোগ, বড় স্বপ্ন
                </div>
                <h1 class="hero-title">
                Yousuf Ali Bari Foundation
                </h1>
                <p class="hero-subtitle">
                    একটি ছোট উদ্যোগ,
                    যার লক্ষ্য আমাদের এলাকার আর্থিকভাবে ও সামাজিকভাবে পিছিয়ে থাকা
                    মানুষদের পাশে দাঁড়ানো – মাসিক কন্ট্রিবিউশন, স্বচ্ছ হিসাব
                    এবং ভালোবাসা দিয়ে।
                </p>
                <div class="hero-actions">
                    <a href="#what-we-do" class="btn-primary-hero">
                        আমাদের কাজ কী
                    </a>
                    <a href="#how-to-join" class="btn-ghost-hero">
                        কীভাবে যুক্ত হবেন
                    </a>
                    <button type="button" class="btn-primary-hero summary-nav" data-page="donate.php">
                        অনুদান দিন
                    </button>
                </div>
            </div>

            <div class="hero-image-card">
                <img
                    class="hero-image"
                    id="heroImage"
                    src="<?php echo !empty($visualImagesRelative)
                        ? htmlspecialchars($visualImagesRelative[0])
                        : 'https://images.unsplash.com/photo-1542810634-71277d95dcbb?auto=format&fit=crop&w=900&q=80'; ?>"
                    alt="Foundation activity visual"
                >
                
            </div>
        </div>
    </section>

    <section class="impact-strip">
        <div class="impact-grid">
            <div class="impact-card">
                <div class="impact-label">মাসিক কন্ট্রিবিউশন</div>
                <div class="impact-value">নিয়মিত সহায়তার তহবিল</div>
            </div>
            <div class="impact-card">
                <div class="impact-label">ব্যয়ের স্বচ্ছ হিসাব</div>
                <div class="impact-value">ডিজিটাল রিপোর্ট ও Analytics</div>
            </div>
            <div class="impact-card">
                <div class="impact-label">সহায়তার ধরন</div>
                <div class="impact-value">চিকিৎসা, শিক্ষা, জরুরি সাহায্য,ঈদ উপহার</div>
            </div>
        </div>
    </section>

    <main class="main-content">
        <div>
            <section class="section" id="what-we-do">
                <h2 class="section-header">
                    <span class="icon">📖</span>
                    আমরা কী করছি – সংক্ষিপ্ত গল্প
                </h2>
                <div class="section-body">
                    <p>
                    আমাদের পার্মানেন্ট সদস্যদের মাসিক কন্ট্রিবিউশনের মাধ্যমে একটি তহবিল গঠিত হয়েছে। পার্মানেন্ট সদস্য নন এমন ব্যক্তিরাও চাইলে এই ফান্ডে সহযোগিতা করতে পারেন।

এই তহবিল থেকে আমরা অতিদরিদ্র ও বিপদগ্রস্ত আত্মীয়, প্রতিবেশী ও এলাকার মানুষের প্রয়োজনে সহায়তা করার চেষ্টা করি। পাশাপাশি ঈদে সামর্থ্য অনুযায়ী উপহার দেওয়ার উদ্যোগ নেওয়া হয়।
                    </p>
                    <ul class="story-timeline">
                        <li class="story-step">
                            <div class="story-step-title">১. নিয়মিত কন্ট্রিবিউশন</div>
                            <div class="story-step-text">
                                প্রতিমাসে পার্মানেন্ট  সদস্যরা নির্দিষ্ট পরিমাণ অর্থ ফাউন্ডেশনের ফান্ডে
                                জমা দেন।
                            </div>
                        </li>
                        <li class="story-step">
                            <div class="story-step-title">২. প্রয়োজন চিহ্নিত করা</div>
                            <div class="story-step-text">
                                কার প্রয়োজন সবচেয়ে বেশি – তা বোঝার জন্য আমরা কথা বলি,
                                যাচাই করি এবং প্রাধান্য ঠিক করি।
                            </div>
                        </li>
                        <li class="story-step">
                            <div class="story-step-title">৩. স্বচ্ছ ভাবে ব্যয়</div>
                            <div class="story-step-text">
                                প্রতিটি টাকা কোথায় গেল তার বিস্তারিত হিসাব এই ওয়েবসাইটে সংরক্ষণ
                                ও প্রদর্শন করা হয় – যাতে যে কেউ সহজে দেখে নিতে পারে।
                            </div>
                        </li>
                    </ul>
                </div>
            </section>

            <section class="section" id="how-to-join">
                <h2 class="section-header">
                    <span class="icon">🤝</span>
                    আপনি কীভাবে এই যাত্রায় অংশ নেবেন
                </h2>
                <div class="section-body">
                    <p>
                        আপাতত এই ফাউন্ডেশনটির পরিসর সীমিত।
                        আমরা আরও মানুষকে যুক্ত করার পরিকল্পনা রাখি।
                    </p>
                    <ul class="list-check">
                        <li>
                            <span class="bullet">✔</span>
                            <span> এককালীন কন্ট্রিবিউশন করা।</span>
                        </li>
                        <li>
                            <span class="bullet">✔</span>
                            <span>কাউকে চেনেন যিনি সত্যিই বিপদে আছেন – আমাদের জানানো।</span>
                        </li>
                        <li>
                            <span class="bullet">✔</span>
                            <span>শিক্ষা, চাকরি বা চিকিৎসা বিষয়ক পরামর্শ দিয়ে সহায়তায় অংশ নেওয়া।</span>
                        </li>
                    </ul>
                    <div style="margin-top:10px;">
                        <div class="highlight-box">
                            এই ওয়েবসাইটের ভেতরের বিভিন্ন পাতায় আপনি বিস্তারিত হিসাব, Analytics
                            এবং মাসিক কন্ট্রিবিউশন দেখার লিঙ্ক পাবেন – সেগুলো ব্যবহার করে
                            যে কেউ আমাদের অগ্রগতি বুঝতে পারবেন।
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <aside>
            <section class="section">
                <h2 class="section-header">
                    <span class="icon">📌</span>
                    এই ওয়েবসাইট থেকে কী দেখবেন?
                </h2>
                <div class="section-body">
                    <p style="font-size:0.9rem; color:#4b5563; margin-bottom:10px;">
                        নিচের প্রতিটি সেকশন থেকে আলাদা পাতায় গিয়ে বিস্তারিত দেখতে পারবেন।
                    </p>

                    <div class="page-links-grid">
                        <div class="page-card">
                            <div class="page-card-title">মাসিক কন্ট্রিবিউশন</div>
                            <div class="page-card-text">
                                প্রতি মাসে পার্মানেন্ট সদস্যরা কত টাকা দিয়েছেন, কার স্ট্যাটাস Paid / Pending – সব কিছু এক জায়গায় দেখতে পারবেন।
                            </div>
                            <button type="button" class="page-card-btn summary-nav" data-page="contribution_current_month.php">
                                কন্ট্রিবিউশন দেখুন
                                <span class="arrow">↗</span>
                            </button>
                        </div>

                        <div class="page-card">
                            <div class="page-card-title">আর্থিক সারসংক্ষেপ</div>
                            <div class="page-card-text">
                                বছরে মোট ইনকাম, খরচ ও বর্তমান ব্যালান্স – সহজ ভাষায় একটি সারসংক্ষেপ রিপোর্ট।
                            </div>
                            <button type="button" class="page-card-btn summary-nav" data-page="view_expenses.php">
                                সারসংক্ষেপ খুলুন
                                <span class="arrow">↗</span>
                            </button>
                        </div>

                        <div class="page-card">
                            <div class="page-card-title">Analytics</div>
                            <div class="page-card-text">
                                মাসভিত্তিক গ্রাফ, কতজন Paid / Pending, কোন বছরে কত সহায়তা হয়েছে – সব কিছুর ভিজুয়াল চিত্র।
                            </div>
                            <button type="button" class="page-card-btn summary-nav" data-page="analytics.php">
                                Analytics দেখুন
                                <span class="arrow">↗</span>
                            </button>
                        </div>

                        <div class="page-card">
                            <div class="page-card-title">আমাদের সম্পর্কে</div>
                            <div class="page-card-text">
                                এই ফাউন্ডেশনের পেছনের গল্প, উদ্দেশ্য ও ভবিষ্যৎ পরিকল্পনা নিয়ে একটু বিস্তারিত লেখা।
                            </div>
                            <button type="button" class="page-card-btn summary-nav" data-page="about.php">
                                বিস্তারিত জানুন
                                <span class="arrow">↗</span>
                            </button>
                        </div>

                        <div class="page-card">
                            <div class="page-card-title">অনুদান</div>
                            <div class="page-card-text">
                                যদি আপনি বা আপনার পরিচিত কেউ এককালীন বা নিয়মিতভাবে সহায়তা পাঠাতে চান, এই পাতায় প্রয়োজনীয় তথ্য পাবেন।
                            </div>
                            <button type="button" class="page-card-btn summary-nav" data-page="donate.php">
                                অনুদানের অপশন
                                <span class="arrow">↗</span>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section">
                <h2 class="section-header">
                    <span class="icon">📝</span>
                    সাম্প্রতিক কিছু আপডেট
                </h2>
                <div class="section-body">
                    <ul class="updates-list">
                        <li>
                            <span class="update-tag">নতুন</span>
                            ওয়েবসাইটে Analytics ড্যাশবোর্ড যুক্ত হয়েছে – এখন থেকে বছরভিত্তিক অবস্থা সহজে দেখা যাবে।
                        </li>
                        <li>
                            <span class="update-tag">চলমান</span>
                            মাসিক কন্ট্রিবিউশনের টেবিল এখন মোবাইল স্ক্রিনে আরও সহজে পড়া যাবে।
                        </li>
                        <li>
                            <span class="update-tag">পরিকল্পনা</span>
                            শিক্ষাবৃত্তি ও জরুরি মেডিকেল ফান্ডের জন্য আলাদা সেগমেন্ট করার কাজ চলছে।
                        </li>
                    </ul>
                </div>
            </section>
        </aside>
    </main>

    <section class="donation-contact" id="donation">
        <div class="donation-inner">
            <div class="donation-header-title">
                💝 অনুদান দেয়ার উপায়
            </div>
            <div class="donation-header-text">
                আপনার ছোট একটি নিয়মিত অনুদানও কারও বড় কষ্ট লাঘব করতে পারে। নিচে bKash ও ব্যাংক–দুইভাবে তথ্য রাখা হয়েছে (নিজের তথ্য অনুযায়ী পরিবর্তন করুন)।
            </div>

            <div class="donation-grid">
                <div class="donation-card">
                    <div class="donation-card-header">
                        <div class="donation-logo">
                            <img src="bkash.png" alt="bKash Logo">
                        </div>
                        <div>
                            <div class="donation-badge">bKash</div>
                            <div class="donation-card-title">পার্সোনাল বিকাশ নম্বর</div>
                        </div>
                    </div>
                    <div class="donation-card-body">
                        <div class="donation-rows">
                            <div>
                                <span class="donation-row-label">বিকাশ নম্বর:</span>
                                <span class="donation-row-text">০১৬৪৭০৫২১৬৮</span>
                            </div>
                            <div>
                                <span class="donation-row-label">অ্যাকাউন্ট টাইপ:</span>
                                <span class="donation-row-text">Personal</span>
                            </div>
                            <div style="margin-top:4px; font-size:0.8rem; color:#9ca3af;">
                                * দয়া করে বর্ণনায় “Donation – Yousuf Ali Bari Foundation” লিখে পাঠান।
                            </div>
                        </div>
                        <div class="donation-cta-row">
                            <button type="button" class="donation-cta primary summary-nav" data-page="donate.php">
                                বিকাশে অনুদান করুন
                                <span class="donation-cta-icon">↗</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="donation-card">
                    <div class="donation-card-header">
                        <div class="donation-logo">
                            <img src="sonali.png" alt="Bank Logo">
                        </div>
                        <div>
                            <div class="donation-badge">Bank Transfer</div>
                            <div class="donation-card-title">ব্যাংক অ্যাকাউন্টের তথ্য</div>
                        </div>
                    </div>
                    <div class="donation-card-body">
                        <div class="donation-rows">
                            <div>
                                <span class="donation-row-label">Bank Name:</span>
                                <span class="donation-row-text">Islami Bank PLC</span>
                            </div>
                            <div>
                                <span class="donation-row-label">Account No:</span>
                                <span class="donation-row-text">20507770204046625</span>
                            </div>
                        </div>
                        <div class="donation-cta-row">
                            <button type="button" class="donation-cta outline summary-nav" data-page="donate.php">
                                পূর্ণ ডিটেইলস দেখুন
                                <span class="donation-cta-icon">↗</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="donation-card">
                    <div class="donation-card-header">
                        <div class="donation-logo">
                            <span style="font-size:0.9rem; font-weight:700; color:#facc15;">CL</span>
                        </div>
                        <div>
                            <div class="donation-badge">Contact &amp; Location</div>
                            <div class="donation-card-title">যোগাযোগ ও অফিসের ঠিকানা</div>
                        </div>
                    </div>
                    <div class="donation-card-body">
                        <div class="donation-rows">
                            <div>
                                <span class="donation-row-label">মোবাইল:</span>
                                <span class="donation-row-text">
                                    01959564847 (Shameem), 01910147518 (Motalib),
                                    01568914535 (Yousuf), 01829113968 (Sojol)
                                </span>
                            </div>
                            <div>
                                <span class="donation-row-label">ইমেইল:</span>
                                <span class="donation-row-text">srshamim0.23@gmail.com</span>
                            </div>
                            <div style="margin-top:4px;">
                                <span class="donation-row-label">ঠিকানা:</span>
                                <span class="donation-row-text">
                                    ইউসুফ আলী মুন্সি বাড়ি, গুনাইঘর উত্তর-৩৫৩০, দেবিদ্ধার, কুমিল্লা
                                </span>
                            </div>
                        </div>
                        <div class="donation-cta-row">
                            <a
                                href="https://www.google.com/maps?q=23.5772993579408,90.96407983925467"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="donation-cta primary"
                            >
                                📍 ম্যাপে দেখুন
                            </a>
                            <a href="#top" class="donation-cta outline">
                                উপরে ফিরে যান
                                <span class="donation-cta-icon">↑</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<script>
(function () {
    if (window.__heroSliderInitialized) return;
    window.__heroSliderInitialized = true;

    var images = <?php echo json_encode($visualImagesRelative, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
    var fallback = "https://images.unsplash.com/photo-1542810634-71277d95dcbb?auto=format&fit=crop&w=900&q=80";
    var imgEl = document.getElementById("heroImage");

    if (!imgEl) return;

    if (!images || images.length === 0) {
        images = [fallback];
    }

    var index = 0;
    imgEl.src = images[0] || fallback;

    if (images.length > 1) {
        setInterval(function () {
            index = (index + 1) % images.length;
            imgEl.src = images[index] || fallback;
        }, 1500);
    }
})();
</script>

</div>

<footer class="site-footer">
    <div class="footer-inner">
        <div>
            © <?php echo date('Y'); ?> Yousuf Ali Bari Foundation. All rights reserved.
            Developed by <a href="https://www.facebook.com/shamim0.25.inf/" target="_blank"><strong>Mehadi Hasan Shameem</strong></a>.
        </div>
        <div class="footer-links">
            <a href="#top" class="footer-link">মূলপাতা</a>
            <a href="#" class="footer-link summary-nav" data-page="donate.php">Donate</a>
            <a href="#" class="footer-link summary-nav" data-page="about.php">About</a>
        </div>
    </div>
</footer>
</body>
</html>

