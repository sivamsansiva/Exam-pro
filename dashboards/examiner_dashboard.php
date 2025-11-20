<?php
include('../config/config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'examiner') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch Examiner Details for display
$stmt = $conn->prepare("SELECT first_name, last_name FROM users WHERE id=? AND role='examiner'");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

$firstName = $row['first_name'] ?? '';
$lastName = $row['last_name'] ?? '';

// Fetch Statistics (exams created by this examiner)
$myExamsQuery = "SELECT COUNT(*) as total FROM exam WHERE created_by=?";
$stmt = $conn->prepare($myExamsQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$myExams = $result->fetch_assoc()['total'];
$stmt->close();

$totalExamsQuery = "SELECT COUNT(*) as total FROM exam";
$totalExamsResult = mysqli_query($conn, $totalExamsQuery);
$totalExams = mysqli_fetch_assoc($totalExamsResult)['total'];

$totalAttemptsQuery = "SELECT COUNT(*) as total FROM exam_attempt WHERE completed=1";
$totalAttemptsResult = mysqli_query($conn, $totalAttemptsQuery);
$totalAttempts = mysqli_fetch_assoc($totalAttemptsResult)['total'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examiner Dashboard - ExamPro</title>
</head>

<body>
    <?php include('../includes/header.php'); ?>

    <div class="container mt-xl">
        <div class="mb-xl">
            <h1>Examiner Dashboard</h1>
            <p>Create and manage exams, view student performance.</p>
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
                    <div style="font-size: 2.5rem; font-weight: bold;"><?php echo $myExams; ?></div>
                    <div style="font-size: 1rem; opacity: 0.9;">My Exams</div>
                </div>
            </div>

            <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                <div style="padding: 1.5rem;">
                    <div style="font-size: 2.5rem; font-weight: bold;"><?php echo $totalExams; ?></div>
                    <div style="font-size: 1rem; opacity: 0.9;">Total Exams</div>
                </div>
            </div>

            <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                <div style="padding: 1.5rem;">
                    <div style="font-size: 2.5rem; font-weight: bold;"><?php echo $totalAttempts; ?></div>
                    <div style="font-size: 1rem; opacity: 0.9;">Completed Attempts</div>
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
                <a href="../exams/addExam.php" class="btn btn-secondary" style="justify-content: flex-start;">
                    <i class="fas fa-plus-circle"></i> Create New Exam
                </a>
                <a href="#my-exams" class="btn btn-outline" style="justify-content: flex-start;">
                    <i class="fas fa-list"></i> My Exams
                </a>
            </div>
        </div>

        <!-- My Exams Section -->
        <div class="card mb-xl" id="my-exams">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-file-alt"></i> My Created Exams</h2>
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $examsQuery = "
                            SELECT e.id, e.code, e.name, d.name as dept_name, e.scheduled_at, e.duration_minutes, e.max_score
                            FROM exam e
                            LEFT JOIN department d ON e.department_id = d.id
                            WHERE e.created_by = ?
                            ORDER BY e.created_at DESC
                            LIMIT 10
                        ";
                        $stmt = $conn->prepare($examsQuery);
                        $stmt->bind_param("i", $userId);
                        $stmt->execute();
                        $examsResult = $stmt->get_result();

                        if ($examsResult->num_rows > 0) {
                            while ($exam = $examsResult->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($exam['code']) . "</td>";
                                echo "<td>" . htmlspecialchars($exam['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($exam['dept_name'] ?? 'N/A') . "</td>";
                                echo "<td>" . ($exam['scheduled_at'] ? date('Y-m-d H:i', strtotime($exam['scheduled_at'])) : 'Not scheduled') . "</td>";
                                echo "<td>" . htmlspecialchars($exam['duration_minutes']) . " mins</td>";
                                echo "<td>" . htmlspecialchars($exam['max_score']) . "</td>";
                                echo "<td>";
                                echo "<a href='../exams/updateExam.php?updateid=" . $exam['id'] . "' class='btn btn-sm btn-primary'>Edit</a> ";
                                echo "<a href='../exams/deleteExam.php?deleteid=" . $exam['id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>No exams created yet</td></tr>";
                        }
                        $stmt->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Exam Attempts -->
        <div class="card mb-xl">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-chart-bar"></i> Recent Exam Attempts</h2>
            </div>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Exam</th>
                            <th>Student</th>
                            <th>Attempt</th>
                            <th>Score</th>
                            <th>Percentage</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $attemptsQuery = "
                            SELECT ea.*, e.code, e.name as exam_name, e.max_score, u.first_name, u.last_name
                            FROM exam_attempt ea
                            JOIN exam e ON ea.exam_id = e.id
                            JOIN users u ON ea.user_id = u.id
                            WHERE e.created_by = ? AND ea.completed = 1
                            ORDER BY ea.started_at DESC
                            LIMIT 15
                        ";
                        $stmt = $conn->prepare($attemptsQuery);
                        $stmt->bind_param("i", $userId);
                        $stmt->execute();
                        $attemptsResult = $stmt->get_result();

                        if ($attemptsResult->num_rows > 0) {
                            while ($attempt = $attemptsResult->fetch_assoc()) {
                                $percentage = ($attempt['score'] / $attempt['max_score']) * 100;
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($attempt['code']) . " - " . htmlspecialchars($attempt['exam_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($attempt['first_name'] . ' ' . $attempt['last_name']) . "</td>";
                                echo "<td>#" . htmlspecialchars($attempt['attempt_no']) . "</td>";
                                echo "<td>" . htmlspecialchars($attempt['score']) . "/" . htmlspecialchars($attempt['max_score']) . "</td>";
                                echo "<td>" . number_format($percentage, 2) . "%</td>";
                                echo "<td>" . date('Y-m-d H:i', strtotime($attempt['started_at'])) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>No attempts yet</td></tr>";
                        }
                        $stmt->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>

</html>
