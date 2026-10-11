<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Test | StudentHub</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f7fb;
            color: #1e293b;
        }

        .session-card {
            width: 100%;
            max-width: 620px;
            padding: 36px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.08);
        }

        .brand {
            margin-bottom: 8px;
            color: #4361ee;
            font-size: 26px;
            font-weight: 800;
        }

        .subtitle {
            margin-bottom: 28px;
            color: #64748b;
            font-size: 14px;
        }

        .status-icon {
            width: 68px;
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            border-radius: 50%;
            font-size: 34px;
            font-weight: bold;
        }

        .success-icon {
            color: #15803d;
            background: #dcfce7;
        }

        .error-icon {
            color: #b91c1c;
            background: #fee2e2;
        }

        h1 {
            margin-bottom: 12px;
            font-size: 28px;
        }

        .success-title {
            color: #15803d;
        }

        .error-title {
            color: #b91c1c;
        }

        .message {
            margin-bottom: 22px;
            color: #64748b;
            font-size: 15px;
            line-height: 1.7;
        }

        .session-heading {
            margin: 24px 0 12px;
            font-size: 17px;
        }

        pre {
            padding: 18px;
            overflow-x: auto;
            border-radius: 12px;
            background: #0f172a;
            color: #e2e8f0;
            font-size: 13px;
            line-height: 1.7;
        }

        .action-btn {
            display: inline-block;
            margin-top: 24px;
            padding: 14px 20px;
            border-radius: 10px;
            color: #fff;
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .dashboard-btn {
            background: #4361ee;
        }

        .dashboard-btn:hover {
            background: #354edb;
            transform: translateY(-2px);
        }

        .login-btn {
            background: #dc3545;
        }

        .login-btn:hover {
            background: #bb2d3b;
            transform: translateY(-2px);
        }

        .footer {
            margin-top: 26px;
            color: #94a3b8;
            font-size: 12px;
        }

        @media (max-width: 480px) {
            .session-card {
                padding: 26px 20px;
            }

            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>
    <main class="session-card">
        <div class="brand">StudentHub</div>
        <p class="subtitle">Your Digital Campus Portal</p>

        <?php if (isset($_SESSION['student_id'])): ?>

            <div class="status-icon success-icon">✓</div>

            <h1 class="success-title">Session Working!</h1>

            <p class="message">
                Login successful. Your student session is active.
                You can continue to your dashboard.
            </p>

            <h2 class="session-heading">Session Details</h2>

            <pre><?php
                echo htmlspecialchars(
                    print_r($_SESSION, true),
                    ENT_QUOTES,
                    "UTF-8"
                );
            ?></pre>

            <a class="action-btn dashboard-btn"
               href="../pages/dashboard.html">
                Go to Dashboard →
            </a>

        <?php else: ?>

            <div class="status-icon error-icon">!</div>

            <h1 class="error-title">No Student Session</h1>

            <p class="message">
                You are not logged in or your session has expired.
                Please log in to access your dashboard.
            </p>

            <a class="action-btn login-btn"
               href="../pages/login.html">
                Go to Login →
            </a>

        <?php endif; ?>

        <p class="footer">
            StudentHub · Your Digital Campus Portal
        </p>
    </main>
</body>
</html>