<?php
include('../config/config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['user_id'] ?? null;
$userDepartment = $_SESSION['department'] ?? null;

// Check if profile is complete (NIC is mandatory)
$profileCheck = "SELECT nic, first_name, last_name FROM users WHERE id = ?";
$stmt = $conn->prepare($profileCheck);
$stmt->bind_param("i", $userId);
$stmt->execute();
$profileData = $stmt->get_result()->fetch_assoc();

if (!$profileData || empty($profileData['nic']) || empty($profileData['first_name']) || empty($profileData['last_name'])) {
    header("Location: ../users/profile.php?complete=1&redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

// Get exam details if ID is provided
$examDetails = null;
$selectedExamId = isset($_GET['id']) ? (int)$_GET['id'] : null;
if ($selectedExamId) {
    $examQuery = "SELECT e.id, e.code, e.name, e.description, e.duration_minutes, e.scheduled_at,
                             e.total_questions, e.max_score, e.department_id,
                             u.first_name, u.last_name, d.name as dept_name
                      FROM exam e
                      JOIN users u ON e.created_by = u.id
                      LEFT JOIN department d ON e.department_id = d.id
                      WHERE e.id = ? AND (e.department_id = ? OR e.department_id IS NULL)";
    $stmt = $conn->prepare($examQuery);
    $stmt->bind_param("ii", $selectedExamId, $userDepartment);
    $stmt->execute();
    $examDetails = $stmt->get_result()->fetch_assoc();
}

// Handle form submission
$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && $userId) {
    $examId = (int)$_POST['exam_id'];

    // Verify user can register for this exam (department or general exam only)
    $verifyQuery = "SELECT id FROM exam WHERE id = ? AND (department_id = ? OR department_id IS NULL)";
    $stmt = $conn->prepare($verifyQuery);
    $stmt->bind_param("ii", $examId, $userDepartment);
    $stmt->execute();
    $verifyResult = $stmt->get_result();

    if ($verifyResult->num_rows === 0) {
        $message = "You cannot register for this exam. Only exams from your department or general exams are allowed.";
        $messageType = "error";
    } else {
        // Check if already registered
        $check_query = "SELECT * FROM exam_registration WHERE exam_id = ? AND user_id = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("ii", $examId, $userId);
        $stmt->execute();
        $check_result = $stmt->get_result();

        if ($check_result && $check_result->num_rows > 0) {
            $registration = $check_result->fetch_assoc();
            if ($registration['status'] == 'registered') {
                $message = "You are already registered for this exam!";
                $messageType = "warning";
            } else {
                // Update cancelled to registered
                $update_query = "UPDATE exam_registration SET status = 'registered', registered_at = NOW() WHERE exam_id = ? AND user_id = ?";
                $stmt = $conn->prepare($update_query);
                $stmt->bind_param("ii", $examId, $userId);
                if ($stmt->execute()) {
                    $message = "Successfully re-registered for the exam!";
                    $messageType = "success";
                } else {
                    $message = "Error re-registering for exam: " . $conn->error;
                    $messageType = "error";
                }
            }
        } else {
            // Register for exam
            $register_query = "INSERT INTO exam_registration (exam_id, user_id, registered_at, status) VALUES (?, ?, NOW(), 'registered')";
            $stmt = $conn->prepare($register_query);
            $stmt->bind_param("ii", $examId, $userId);

            if ($stmt->execute()) {
                $message = "Successfully registered for the exam!";
                $messageType = "success";
            } else {
                $message = "Error registering for exam: " . $conn->error;
                $messageType = "error";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Registration - ExamPro</title>
    <link rel="stylesheet" href="../styles/core.css">
    <link rel="stylesheet" href="../styles/components.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <!-- Header -->
    <?php include("../includes/header.php"); ?>

    <!-- Exam registration container -->
    <div class="container mt-xl mb-xl">
        <div class="grid-2">
            <!-- Exam Details Section -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-info-circle"></i> Exam Details</h2>
                    <p class="card-subtitle">Information about the selected exam</p>
                </div>
                <div class="card-body">
                    <?php if ($examDetails): ?>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-file-alt"></i> Exam Name</label>
                            <div class="form-control"><?php echo htmlspecialchars($examDetails['name']); ?></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-clock"></i> Duration</label>
                            <div class="form-control"><?php echo htmlspecialchars($examDetails['duration_minutes']); ?> minutes</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-user-tie"></i> Examiner</label>
                            <div class="form-control"><?php echo htmlspecialchars($examDetails['first_name'] . ' ' . $examDetails['last_name']); ?></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-building"></i> Department</label>
                            <div class="form-control">
                                <?php echo $examDetails['dept_name'] ? htmlspecialchars($examDetails['dept_name']) : 'General (All Departments)'; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-calendar"></i> Scheduled At</label>
                            <div class="form-control"><?php echo $examDetails['scheduled_at'] ? htmlspecialchars(date('M d, Y H:i', strtotime($examDetails['scheduled_at']))) : 'Not scheduled'; ?></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-align-left"></i> Description</label>
                            <div class="form-control" style="background-color: #f0f0f0; height: auto; min-height: 100px;"><?php echo isset($examDetails['description']) ? htmlspecialchars($examDetails['description']) : 'No description available'; ?></div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Please select an exam from the registration form to view its details.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Registration Form Section -->
            <div class="card">
                <div class="card-header">
                    <h1 class="card-title"><i class="fas fa-user-plus"></i> Register Exam</h1>
                    <p class="card-subtitle">Select an exam to register</p>
                </div>
                <div class="card-body">
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?php echo $messageType === 'error' ? 'error' : ($messageType === 'warning' ? 'warning' : 'success'); ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <form action="registerExam.php<?php echo $selectedExamId ? '?id=' . htmlspecialchars($selectedExamId) : ''; ?>" method="post">

                        <div class="form-group">
                            <label for="exam" class="form-label">Select Exam</label>
                            <?php
                            // Only show exams from user's department or general exams (department_id IS NULL)
                            $sql = "SELECT e.id, e.code, e.name, e.department_id, d.name as dept_name
                                        FROM exam e
                                        LEFT JOIN department d ON e.department_id = d.id
                                        WHERE e.department_id = ? OR e.department_id IS NULL
                                        ORDER BY e.scheduled_at DESC, e.name";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $userDepartment);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result && $result->num_rows > 0) {
                            ?>
                                <select name="exam_id" id="exam" class="form-control" required onchange="if(this.value) window.location.href='registerExam.php?id=' + this.value">
                                    <option value="" disabled <?php echo !$selectedExamId ? 'selected' : ''; ?>>Select an exam</option>
                                    <?php
                                    while ($row = $result->fetch_assoc()) {
                                        $selected = ($examDetails && $examDetails['id'] == $row['id']) ? 'selected' : '';
                                        $deptLabel = $row['dept_name'] ? $row['dept_name'] : 'General';
                                        echo "<option value=\"" . htmlspecialchars($row['id']) . "\" $selected>" .
                                            htmlspecialchars($row['name']) . " (" . htmlspecialchars($deptLabel) . ")</option>";
                                    }
                                    ?>
                                </select>
                            <?php
                            } else {
                                echo "<p class='text-error'>No exams available for your department.</p>";
                            }
                            ?>
                        </div>

                        <?php if ($examDetails): ?>
                            <button type="submit" class="btn btn-primary" style="width: 100%;">
                                <i class="fas fa-check"></i> Confirm Registration
                            </button>
                        <?php endif; ?>
                    </form>

                    <div class="mt-lg">
                        <a href="../index.php" class="btn btn-outline" style="width: 100%; justify-content: center;">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include("../includes/footer.php"); ?>

    <script src="../scripts/script.js"></script>

</body>

</html>
