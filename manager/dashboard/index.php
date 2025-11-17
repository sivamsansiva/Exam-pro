<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role_key']) || $_SESSION['role_key'] !== 'manager') {
    header('Location: /auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 40px 16px;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f766e 0%, #0f172a 100%);
            color: #f1f5f9;
        }
        .card {
            width: min(760px, 100%);
            background: rgba(15, 23, 42, 0.82);
            border-radius: 18px;
            padding: 40px;
            border: 1px solid rgba(148, 163, 184, 0.25);
            box-shadow: 0 36px 68px rgba(15, 23, 42, 0.42);
        }
        h1 {
            font-size: 2rem;
            margin-bottom: 12px;
        }
        p {
            line-height: 1.65;
            color: rgba(226, 232, 240, 0.92);
            margin-bottom: 16px;
        }
        .actions {
            margin-top: 24px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        a {
            color: #38bdf8;
            text-decoration: none;
            font-weight: 600;
        }
        .actions a {
            padding: 12px 18px;
            border-radius: 12px;
            background: rgba(56, 189, 248, 0.15);
            border: 1px solid rgba(56, 189, 248, 0.45);
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <article class="card">
        <h1>Manager Dashboard</h1>
        <p>
            Hello <strong><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></strong>.
            The manager experience is getting a refresh. In the meantime, you can continue working via
            the legacy dashboard while we migrate features to the new UI.
        </p>
        <nav class="actions" aria-label="Manager navigation">
            <a href="/users/manager.php">Open Legacy Manager Dashboard</a>
            <a href="/auth/logout.php">Sign Out</a>
        </nav>
    </article>
</body>
</html>
