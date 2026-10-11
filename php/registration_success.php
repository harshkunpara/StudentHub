<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful | StudentHub</title>

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

        .success-card {
            width: 100%;
            max-width: 480px;
            padding: 42px 32px;
            text-align: center;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.08);
        }

        .brand {
            margin-bottom: 30px;
            font-size: 25px;
            font-weight: 800;
            color: #2563eb;
            letter-spacing: -0.5px;
        }

        .success-icon {
            width: 76px;
            height: 76px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            border-radius: 50%;
            background: #dcfce7;
            color: #16a34a;
            font-size: 40px;
            font-weight: bold;
        }

        h1 {
            margin-bottom: 14px;
            font-size: 28px;
            line-height: 1.3;
        }

        .message {
            margin-bottom: 28px;
            color: #64748b;
            font-size: 15px;
            line-height: 1.7;
        }

        .login-btn {
            display: block;
            width: 100%;
            padding: 14px 20px;
            border-radius: 10px;
            background: #2563eb;
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .login-btn:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }

        .footer {
            margin-top: 24px;
            color: #94a3b8;
            font-size: 12px;
        }

        @media (max-width: 480px) {
            .success-card {
                padding: 34px 22px;
            }

            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>
    <main class="success-card">
        <div class="brand">StudentHub</div>

        <div class="success-icon" aria-hidden="true">✓</div>

        <h1>Registration Successful!</h1>

        <p class="message">
            Your StudentHub account has been created successfully.
            You can now log in and explore your student dashboard.
        </p>

        <a class="login-btn" href="../pages/login.html">
            Continue to Login →
        </a>

        <p class="footer">StudentHub · Your Digital Campus Portal</p>
    </main>
</body>
</html>
