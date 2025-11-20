<?php
    include('../config/config.php');

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Manager'){
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
            $stmt = $conn->prepare("UPDATE user_phone SET phone=? WHERE user_id=?");
            $stmt->bind_param("si", $phoneNo, $userId);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $conn->prepare("INSERT INTO user_phone (user_id, phone) VALUES (?, ?)");
            $stmt->bind_param("is", $userId, $phoneNo);
            $stmt->execute();
            $stmt->close();
        }

        if ($result) {
            $updateSuccess = true;
        }
    }

    // Fetch Manager Details
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id=? AND role='Manager'");
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

    // Fetch Manager Phone
    $stmt = $conn->prepare("SELECT phone FROM user_phone WHERE user_id=? LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $phoneResult = $stmt->get_result();
    $phoneRow = $phoneResult->fetch_assoc();
    $stmt->close();
    $phoneNo = $phoneRow['phone'] ?? '';

    // Fetch Statistics
    $totalExamsQuery = "SELECT COUNT(*) as total FROM exam";
    $totalExamsResult = mysqli_query($conn, $totalExamsQuery);
    $totalExams = mysqli_fetch_assoc($totalExamsResult)['total'];

    $totalUsersQuery = "SELECT COUNT(*) as total FROM users WHERE role='Staff'";
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
    <link rel="stylesheet" href="../styles/theme.css">
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>

    <div class="container mt-xl">
        <div class="mb-xl">
            <h1>Manager Dashboard</h1>
            <p>Manage exams, staff, and view reports.</p>
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

        <div class="grid-2">
            <!-- Manager Profile Section -->
            <div class="card mb-xl">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-user-tie"></i> Manager Profile</h2>
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

        <!-- Exams Section -->
        <div class="card mb-xl">
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
        <div class="card mb-xl">
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
