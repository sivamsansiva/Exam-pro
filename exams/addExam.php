<?php
include("../config/config.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || ($_SESSION['role'] != 'manager' && $_SESSION['role'] != 'admin' && $_SESSION['role'] != 'examiner')) {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = mysqli_real_escape_string($conn, $_POST["code"]);
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $description = mysqli_real_escape_string($conn, $_POST["description"]);
    $durationMinutes = (int)$_POST["duration"];
    $departmentId = (int)$_POST["department_id"];
    $quizPassword = $_POST["quiz_password"];
    $scheduledAt = $_POST["scheduled_at"];
    $totalQuestions = (int)$_POST["total_questions"];
    $maxScore = (float)$_POST["max_score"];

    $createdBy = $_SESSION['user_id'];
    $quizPasswordHash = password_hash($quizPassword, PASSWORD_DEFAULT);

    $sql = "INSERT INTO exam (code, name, description, department_id, created_by, quiz_password_hash,
                                  duration_minutes, scheduled_at, total_questions, max_score, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssiisissd",
        $code,
        $name,
        $description,
        $departmentId,
        $createdBy,
        $quizPasswordHash,
        $durationMinutes,
        $scheduledAt,
        $totalQuestions,
        $maxScore
    );

    if ($stmt->execute()) {
        $message = "New Exam added successfully!";

        // Redirect based on role
        if ($_SESSION['role'] == 'manager') {
            echo '<script>alert("' . $message . '"); window.location.href = "../dashboards/manager_dashboard.php";</script>';
        } elseif ($_SESSION['role'] == 'admin') {
            echo '<script>alert("' . $message . '"); window.location.href = "../dashboards/admin_dashboard.php";</script>';
        } elseif ($_SESSION['role'] == 'examiner') {
            echo '<script>alert("' . $message . '"); window.location.href = "../dashboards/examiner_dashboard.php";</script>';
        }
        exit;
    } else {
        $error = "Error adding exam: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Exam - ExamPro</title>
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

        .form-grid-full {
            grid-column: 1 / -1;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <!-- Header -->
    <?php include("../includes/header.php"); ?>

    <!-- Add Exam Content -->
    <div class="container mt-xl mb-xl">
        <div class="exam-form-wrapper">
            <div class="card">
                <div class="card-header">
                    <h1 class="card-title"><i class="fas fa-plus-circle"></i> Add Exam</h1>
                    <p class="card-subtitle">Create a new examination</p>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-error mb-lg">
                            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="code" class="form-label">Exam Code</label>
                                <input type="text" id="code" name="code" class="form-control" required placeholder="e.g., IT101">
                            </div>

                            <div class="form-group">
                                <label for="department_id" class="form-label">Department</label>
                                <?php
                                $sql = "SELECT id, name FROM department ORDER BY name";
                                $result = $conn->query($sql);
                                if ($result && $result->num_rows > 0) {
                                ?>
                                    <select name="department_id" id="department_id" class="form-control" required>
                                        <option value="" disabled selected>Select Department</option>
                                        <?php
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value=\"" . htmlspecialchars($row['id']) . "\">" . htmlspecialchars($row['name']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                <?php
                                } else {
                                    echo "<p class='text-error'>No departments found.</p>";
                                }
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="name" class="form-label">Exam Name</label>
                            <input type="text" id="name" name="name" class="form-control" required placeholder="Enter Exam Name">
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="3" placeholder="Enter exam description"></textarea>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="quiz_password" class="form-label">Quiz Password</label>
                                <input type="text" id="quiz_password" name="quiz_password" class="form-control" required placeholder="Enter Quiz Password">
                                <small class="field-hint">Candidates will need this password to start the exam</small>
                            </div>

                            <div class="form-group">
                                <label for="duration" class="form-label">Duration (minutes)</label>
                                <input type="number" name="duration" id="duration" class="form-control" min="1" max="300" value="60" required>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="scheduled_at" class="form-label">Scheduled Date & Time</label>
                                <input type="datetime-local" id="scheduled_at" name="scheduled_at" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="total_questions" class="form-label">Total Questions</label>
                                <input type="number" id="total_questions" name="total_questions" class="form-control" min="1" value="10" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="max_score" class="form-label">Maximum Score</label>
                            <input type="number" step="0.01" id="max_score" name="max_score" class="form-control" value="100.00" required>
                        </div>

                        <div style="display: flex; gap: var(--spacing-md); margin-top: var(--spacing-xl);">
                            <button type="submit" name="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Exam
                            </button>
                            <a href="javascript:history.back()" class="btn btn-outline">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include("../includes/footer.php"); ?>

</body>

</html>
