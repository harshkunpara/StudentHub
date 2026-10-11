<?php
session_start();

require __DIR__ . "/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Login PHP reached, but request is not POST.");
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

$stmt = $pdo->prepare(
    "SELECT id, name, email, password_hash
     FROM students
     WHERE email = :email
     LIMIT 1"
);

$stmt->execute([
    ":email" => $email
]);

$student = $stmt->fetch();

$studentFound = (bool)$student;
$passwordVerified = false;

if ($student) {
    $passwordVerified = password_verify(
        $password,
        $student["password_hash"]
    );
}

if ($student && $passwordVerified) {
    session_regenerate_id(true);

    $_SESSION["student_id"] = (int)$student["id"];
    $_SESSION["student_name"] = $student["name"];
    $_SESSION["student_email"] = $student["email"];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Debug | StudentHub</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f7fb;
            color: #1e293b;
        }

        .debug-card {
            width: 100%;
            max-width: 680px;
            padding: 34px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.08);
        }

        .brand {
            margin-bottom: 8px;
            color: #4361ee;
            font-size: 25px;
            font-weight: 800;
        }

        .subtitle {
            margin-bottom: 28px;
            color: #64748b;
            font-size: 14px;
        }

        h1 {
            margin-bottom: 24px;
            font-size: 27px;
        }

        .info-row {
            margin-bottom: 14px;
            padding: 15px 16px;
            overflow-wrap: anywhere;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            line-height: 1.6;
        }

        .label {
            display: block;
            margin-bottom: 4px;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status {
            margin: 18px 0;
            padding: 15px;
            border-radius: 10px;
            font-weight: 700;
        }

        .success {
            color: #15803d;
            background: #dcfce7;
            border: 1px solid #bbf7d0;
        }

        .error {
            color: #b91c1c;
            background: #fee2e2;
            border: 1px solid #fecaca;
        }

        pre {
            margin-top: 12px;
            padding: 18px;
            overflow-x: auto;
            background: #0f172a;
            color: #e2e8f0;
            border-radius: 12px;
            font-size: 13px;
            line-height: 1.7;
        }

        .session-heading {
            margin-top: 24px;
            font-size: 18px;
        }

        .test-link {
            display: inline-block;
            margin-top: 24px;
            padding: 13px 20px;
            border-radius: 10px;
            background: #4361ee;
            color: #ffffff;
            text-decoration: none;
            font-weight: 700;
            transition: background 0.2s ease;
        }

        .test-link:hover {
            background: #354edb;
        }

        @media (max-width: 480px) {
            .debug-card {
                padding: 23px 18px;
            }

            h1 {
                font-size: 23px;
            }
        }
    </style>
</head>

<body>
    <main class="debug-card">
        <div class="brand">StudentHub</div>
        <p class="subtitle">Your Digital Campus Portal</p>

        <h1>Login Debug</h1>

        <div class="info-row">
            <span class="label">Email Received</span>
            <?= htmlspecialchars($email, ENT_QUOTES, "UTF-8") ?>
        </div>

        <div class="info-row">
            <span class="label">Password Received</span>
            <?= !empty($password) ? "YES" : "NO" ?>
        </div>

        <?php if (!$student): ?>

            <div class="status error">Student Not Found</div>

        <?php else: ?>

            <div class="info-row">
                <span class="label">Student Found</span>
                <?= htmlspecialchars($student["name"], ENT_QUOTES, "UTF-8") ?>
            </div>

            <?php if (!$passwordVerified): ?>

                <div class="status error">Password Verify Failed</div>

            <?php else: ?>

                <div class="status success">Password Verified</div>
                <div class="status success">Session Created</div>

                <h2 class="session-heading">Session Details</h2>

                <pre><?php
                    echo htmlspecialchars(
                        print_r($_SESSION, true),
                        ENT_QUOTES,
                        "UTF-8"
                    );
                ?></pre>

                <a class="test-link" href="session_test.php">
                    Open Session Test →
                </a>

            <?php endif; ?>

        <?php endif; ?>
    </main>
</body>
</html>