<?php
// session_start();
include('../config/config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] == 'Staff') {
    header("Location: ../auth/login.php");
    exit();
}
global $conn;
$eid = (int)$_GET['updateid'];

// Fetch the exam details
$sql1 = "SELECT e.*, d.name as dept_name FROM exam e LEFT JOIN department d ON e.department_id = d.id WHERE e.id = ?";
$stmt = $conn->prepare($sql1);
$stmt->bind_param("i", $eid);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$code = $row['code'] ?? '';
$name = $row['name'] ?? '';
$description = $row['description'] ?? '';
$departmentId = $row['department_id'] ?? '';
$durationMinutes = $row['duration_minutes'] ?? 60;
$scheduledAt = $row['scheduled_at'] ? date('Y-m-d\TH:i', strtotime($row['scheduled_at'])) : '';
$totalQuestions = $row['total_questions'] ?? 10;
$maxScore = $row['max_score'] ?? 100.00;
$createdBy = $row['created_by'] ?? '';

// Check if form is submitted
if (isset($_POST['submit'])) {
    $code = mysqli_real_escape_string($conn, $_POST["code"]);
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $description = mysqli_real_escape_string($conn, $_POST["description"]);
    $departmentId = (int)$_POST["department_id"];
    $durationMinutes = (int)$_POST["duration"];
    $scheduledAt = $_POST["scheduled_at"] ?? null;
    $totalQuestions = (int)$_POST["total_questions"];
    $maxScore = (float)$_POST["max_score"];

    // Handle password update (only if provided)
    $passwordChanged = !empty($_POST["quiz_password"]);

    if ($passwordChanged) {
        $quizPassword = $_POST["quiz_password"];
        $quizPasswordHash = password_hash($quizPassword, PASSWORD_DEFAULT);

        $sql2 = "UPDATE exam SET code=?, name=?, description=?, department_id=?, quiz_password_hash=?,
                duration_minutes=?, scheduled_at=?, total_questions=?, max_score=?, updated_at=NOW()
                WHERE id=?";
        $stmt = $conn->prepare($sql2);
        $stmt->bind_param(
            "sssisssidi",
            $code,
            $name,
            $description,
            $departmentId,
            $quizPasswordHash,
            $durationMinutes,
            $scheduledAt,
            $totalQuestions,
            $maxScore,
            $eid
        );
    } else {
        $sql2 = "UPDATE exam SET code=?, name=?, description=?, department_id=?,
                duration_minutes=?, scheduled_at=?, total_questions=?, max_score=?, updated_at=NOW()
                WHERE id=?";
        $stmt = $conn->prepare($sql2);
        $stmt->bind_param(
            "ssisissdi",
            $code,
            $name,
            $description,
            $departmentId,
            $durationMinutes,
            $scheduledAt,
            $totalQuestions,
            $maxScore,
            $eid
        );
    }

    if ($stmt->execute()) {
        echo '<script>alert("Updated Successfully");</script>';

        // Redirect based on user role
        if ($_SESSION['role'] == 'Manager') {
            echo '<script>window.location.href = "../dashboards/manager_dashboard.php";</script>';
        } elseif ($_SESSION['role'] == 'Admin') {
            echo '<script>window.location.href = "../dashboards/admin_dashboard.php";</script>';
        } elseif ($_SESSION['role'] == 'Examiner') {
            echo '<script>window.location.href = "../dashboards/examiner_dashboard.php";</script>';
        }
        exit;
    } else {
        die($conn->error);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Exam - ExamPro</title>
    <link rel="stylesheet" href="../styles/core.css">
    <link rel="stylesheet" href="../styles/components.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .exam-form-wrapper {
            max-width: 800px;
            margin: 0 auto;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-lg);
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php
    include('../includes/header.php')
    ?>
    <div class="container mt-xl mb-xl">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title"><i class="fas fa-edit"></i> Update Exam</h1>
                <p class="card-subtitle">Modify exam details</p>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?updateid=' . htmlspecialchars($eid); ?>" method="post">
                    <div class="form-group">
                        <label for="eid" class="form-label">Exam ID</label>
                        <input type="text" id="eid" class="form-control" value="<?php echo htmlspecialchars($eid); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label for="code" class="form-label">Exam Code</label>
                        <input type="text" id="code" name="code" class="form-control" value="<?php echo htmlspecialchars($code); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="name" class="form-label">Exam Name</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="department_id" class="form-label">Department</label>
                        <?php
                        $deptSql = "SELECT id, name FROM department ORDER BY name";
                        $deptResult = $conn->query($deptSql);
                        if ($deptResult && $deptResult->num_rows > 0) {
                        ?>
                            <select name="department_id" id="department_id" class="form-control" required>
                                <option value="">Select Department</option>
                                <?php
                                while ($dept = $deptResult->fetch_assoc()) {
                                    $selected = ($departmentId == $dept['id']) ? 'selected' : '';
                                    echo "<option value=\"" . htmlspecialchars($dept['id']) . "\" $selected>" . htmlspecialchars($dept['name']) . "</option>";
                                }
                                ?>
                            </select>
                        <?php
                        } else {
                            echo "<p class='text-error'>No departments found.</p>";
                        }
                        ?>
                    </div>

                    <div class="form-group">
                        <label for="quiz_password" class="form-label">Quiz Password</label>
                        <input type="text" id="quiz_password" name="quiz_password" class="form-control" placeholder="Leave empty to keep current password">
                        <small class="field-hint">Only fill this if you want to change the password</small>
                    </div>

                    <div class="form-group">
                        <label for="duration" class="form-label">Duration (minutes)</label>
                        <input type="number" id="duration" name="duration" class="form-control" min="1" max="300" value="<?php echo htmlspecialchars($durationMinutes); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="scheduled_at" class="form-label">Scheduled Date & Time</label>
                        <input type="datetime-local" id="scheduled_at" name="scheduled_at" class="form-control" value="<?php echo htmlspecialchars($scheduledAt); ?>">
                    </div>

                    <div class="form-group">
                        <label for="total_questions" class="form-label">Total Questions</label>
                        <input type="number" id="total_questions" name="total_questions" class="form-control" min="1" value="<?php echo htmlspecialchars($totalQuestions); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="max_score" class="form-label">Maximum Score</label>
                        <input type="number" step="0.01" id="max_score" name="max_score" class="form-control" value="<?php echo htmlspecialchars($maxScore); ?>" required>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary">Update Exam</button>
                </form>
            </div>
        </div>
    </div>
    <?php
    include('../includes/footer.php');
    ?>
</body>

</html>
