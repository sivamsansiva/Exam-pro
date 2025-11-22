<?php
include('../config/config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch Manager Details for display
$stmt = $conn->prepare("SELECT first_name, last_name FROM users WHERE id=? AND role='manager'");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

$firstName = $row['first_name'] ?? '';
$lastName = $row['last_name'] ?? '';

// Fetch Statistics
$totalExamsQuery = "SELECT COUNT(*) as total FROM exam";
$totalExamsResult = mysqli_query($conn, $totalExamsQuery);
$totalExams = mysqli_fetch_assoc($totalExamsResult)['total'];

$totalUsersQuery = "SELECT COUNT(*) as total FROM users WHERE role='staff'";
$totalUsersResult = mysqli_query($conn, $totalUsersQuery);
$totalStaff = mysqli_fetch_assoc($totalUsersResult)['total'];

$totalMessagesQuery = "SELECT COUNT(*) as total FROM message WHERE status='open'";
$totalMessagesResult = mysqli_query($conn, $totalMessagesQuery);
$totalMessages = mysqli_fetch_assoc($totalMessagesResult)['total'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard - ExamPro</title>
    <link rel="stylesheet" href="../styles/core.css">
    <link rel="stylesheet" href="../styles/components.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, var(--color-warning-600) 0%, var(--color-primary-600) 100%);
            color: white;
            padding: var(--spacing-xl);
            border-radius: var(--radius-xl);
            margin-bottom: var(--spacing-xl);
            box-shadow: var(--shadow-lg);
        }

        .dashboard-header h1 {
            margin: 0 0 var(--spacing-xs) 0;
            font-size: var(--font-size-3xl);
            font-weight: var(--font-weight-bold);
        }

        .dashboard-header p {
            margin: 0;
            opacity: 0.95;
            font-size: var(--font-size-base);
        }

        .card-header {
            background: linear-gradient(to right, var(--color-secondary-50), white);
            border-bottom: 2px solid var(--color-secondary-100);
            padding: var(--spacing-lg);
        }

        .quick-actions {
            padding: var(--spacing-lg);
            display: flex;
            flex-wrap: wrap;
            gap: var(--spacing-md);
        }

        @media (max-width: 768px) {
            .quick-actions {
                flex-direction: column;
            }

            .quick-actions .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <?php include('../includes/header.php'); ?>

    <div class="container mt-xl">
        <div class="mb-xl">
            <h1>Manager Dashboard</h1>
            <p>Manage exams, staff, and view reports.</p>
        </div>

        <?php if (isset($_SESSION['delete_success'])): ?>
            <div class="alert alert-success mb-lg">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['delete_success']);
                                                    unset($_SESSION['delete_success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['delete_error'])): ?>
            <div class="alert alert-error mb-lg">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['delete_error']);
                                                            unset($_SESSION['delete_error']); ?>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="grid-3 mb-xl">
            <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div style="padding: 1.5rem;">
                    <div style="font-size: 2.5rem; font-weight: bold;"><?php echo $totalExams; ?></div>
                    <div style="font-size: 1rem; opacity: 0.9;">Total Exams</div>
                </div>
            </div>

            <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                <div style="padding: 1.5rem;">
                    <div style="font-size: 2.5rem; font-weight: bold;"><?php echo $totalStaff; ?></div>
                    <div style="font-size: 1rem; opacity: 0.9;">Total Staff</div>
                </div>
            </div>

            <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                <div style="padding: 1.5rem;">
                    <div style="font-size: 2.5rem; font-weight: bold;"><?php echo $totalMessages; ?></div>
                    <div style="font-size: 1rem; opacity: 0.9;">Open Messages</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="card mb-xl">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-bolt"></i> Quick Actions</h2>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; padding: 1.5rem;">
                <a href="../users/profile.php" class="btn btn-primary" style="justify-content: flex-start;">
                    <i class="fas fa-user-circle"></i> My Profile
                </a>
                <a href="#exams" class="btn btn-secondary" style="justify-content: flex-start;">
                    <i class="fas fa-list"></i> View All Exams
                </a>
                <a href="#messages" class="btn btn-outline" style="justify-content: flex-start;">
                    <i class="fas fa-envelope"></i> View Messages
                </a>
            </div>
        </div>

        <!-- Exams Section -->
        <div class="card mb-xl" id="exams">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-file-alt"></i> Recent Exams</h2>
            </div>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Scheduled</th>
                            <th>Duration</th>
                            <th>Max Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $examsQuery = "
                            SELECT e.id, e.code, e.name, d.name as dept_name, e.scheduled_at, e.duration_minutes, e.max_score
                            FROM exam e
                            LEFT JOIN department d ON e.department_id = d.id
                            ORDER BY e.created_at DESC
                            LIMIT 10
                        ";
                        $examsResult = mysqli_query($conn, $examsQuery);

                        if (mysqli_num_rows($examsResult) > 0) {
                            while ($exam = mysqli_fetch_assoc($examsResult)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($exam['code']) . "</td>";
                                echo "<td>" . htmlspecialchars($exam['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($exam['dept_name'] ?? 'N/A') . "</td>";
                                echo "<td>" . ($exam['scheduled_at'] ? date('Y-m-d H:i', strtotime($exam['scheduled_at'])) : 'Not scheduled') . "</td>";
                                echo "<td>" . htmlspecialchars($exam['duration_minutes']) . " mins</td>";
                                echo "<td>" . htmlspecialchars($exam['max_score']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>No exams found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Messages Section -->
        <div class="card mb-xl" id="messages">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-envelope"></i> Recent Messages</h2>
            </div>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Type</th>
                            <th>Content</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $messagesQuery = "
                            SELECT m.*, u.first_name, u.last_name, u.email
                            FROM message m
                            LEFT JOIN users u ON m.user_id = u.id
                            ORDER BY m.created_at DESC
                            LIMIT 10
                        ";
                        $messagesResult = mysqli_query($conn, $messagesQuery);

                        if (mysqli_num_rows($messagesResult) > 0) {
                            while ($msg = mysqli_fetch_assoc($messagesResult)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($msg['first_name'] . ' ' . $msg['last_name']) . "</td>";
                                echo "<td><span class='badge badge-info'>" . ucfirst($msg['type']) . "</span></td>";
                                echo "<td>" . htmlspecialchars(substr($msg['content'], 0, 50)) . "...</td>";
                                echo "<td><span class='badge " . ($msg['status'] == 'open' ? 'badge-warning' : 'badge-success') . "'>" . ucfirst($msg['status']) . "</span></td>";
                                echo "<td>" . date('Y-m-d H:i', strtotime($msg['created_at'])) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>No messages found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>

</html>
