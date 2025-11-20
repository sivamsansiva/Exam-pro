<?php
    include('../config/config.php');

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Examiner'){
        header("Location: ../auth/login.php");
        exit();
    }

    // Handle Profile Update
    $updateSuccess = false;
    if (isset($_POST['update_profile'])) {
        $userId = $_SESSION['user_id'];
        $firstName = $_POST["first_name"];
        $lastName = $_POST["last_name"];
        $email = $_POST["email"];
        $dob = $_POST["dob"];
        $gender = $_POST["gender"];
        $phoneNo = $_POST['phone'];

        $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, gender=?, email=?, dob=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("sssssi", $firstName, $lastName, $gender, $email, $dob, $userId);
        $result = $stmt->execute();
        $stmt->close();

        // Update or insert phone number
        $stmt = $conn->prepare("SELECT * FROM user_phone WHERE user_id=?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $phoneResult = $stmt->get_result();
        $stmt->close();

        if ($phoneResult->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE user_phone SET phone_number=? WHERE user_id=?");
            $stmt->bind_param("si", $phoneNo, $userId);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $conn->prepare("INSERT INTO user_phone (user_id, phone_number) VALUES (?, ?)");
            $stmt->bind_param("is", $userId, $phoneNo);
            $stmt->execute();
            $stmt->close();
        }

        if ($result) {
            $updateSuccess = true;
        }
    }

    // Fetch Examiner Details
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id=? AND role='Examiner'");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    $firstName = $row['first_name'] ?? '';
    $lastName = $row['last_name'] ?? '';
    $dob = $row['dob'] ?? '';
    $email = $row['email'] ?? '';
    $gender = $row['gender'] ?? '';

    // Fetch Examiner Phone
    $stmt = $conn->prepare("SELECT phone_number FROM user_phone WHERE user_id=? LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $phoneResult = $stmt->get_result();
    $phoneRow = $phoneResult->fetch_assoc();
    $stmt->close();
    $phoneNo = $phoneRow['phone_number'] ?? '';

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
    <link rel="stylesheet" href="../styles/theme.css">
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>

    <div class="container mt-xl">
        <div class="mb-xl">
            <h1>Examiner Dashboard</h1>
            <p>Create and manage exams, view student performance.</p>
        </div>

        <?php if ($updateSuccess): ?>
            <div class="alert alert-success mb-lg">
                <i class="fas fa-check-circle"></i> Profile updated successfully!
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

        <div class="grid-2">
            <!-- Examiner Profile Section -->
            <div class="card mb-xl">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-user-graduate"></i> Examiner Profile</h2>
                </div>
                <form method="post" id="profile-form">
                    <div class="form-group">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($firstName); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($lastName); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <div style="display: flex; gap: 1.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="radio" name="gender" value="Male" <?php if ($gender == "Male") echo "checked"; ?>> Male
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="radio" name="gender" value="Female" <?php if ($gender == "Female") echo "checked"; ?>> Female
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mobile Number</label>
                        <input type="tel" name="phone" class="form-control" pattern="[0-9]{10}" value="<?php echo htmlspecialchars($phoneNo); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" value="<?php echo htmlspecialchars($dob); ?>" required>
                    </div>

                    <button type="submit" name="update_profile" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-xl">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-bolt"></i> Quick Actions</h2>
                </div>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <a href="../exams/addExam.php" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-plus-circle"></i> Create New Exam
                    </a>
                    <a href="../users/profile.php" class="btn btn-secondary" style="width: 100%;">
                        <i class="fas fa-user"></i> View Full Profile
                    </a>
                    <a href="../pages/aboutUs.php" class="btn btn-secondary" style="width: 100%;">
                        <i class="fas fa-info-circle"></i> About System
                    </a>
                </div>
            </div>
        </div>

        <!-- My Exams Section -->
        <div class="card mb-xl">
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
