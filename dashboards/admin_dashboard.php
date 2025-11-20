<?php
    include('../config/config.php');

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Admin'){
        header("Location: ../auth/login.php");
        exit();
    }

    // Handle Profile Update
    $updateSuccess = false;
    if (isset($_POST['update_profile'])) {
        $userId = $_SESSION['user_id'];
        $firstName = mysqli_real_escape_string($conn, $_POST["first_name"]);
        $lastName = mysqli_real_escape_string($conn, $_POST["last_name"]);
        $email = mysqli_real_escape_string($conn, $_POST["email"]);
        $dob = mysqli_real_escape_string($conn, $_POST["dob"]);
        $gender = mysqli_real_escape_string($conn, $_POST["gender"]);
        $phoneNo = mysqli_real_escape_string($conn, $_POST['phone']);

        $sql2 = "UPDATE users SET first_name=?, last_name=?, gender=?, email=?, dob=?, updated_at=NOW() WHERE id=?";
        $stmt = $conn->prepare($sql2);
        $stmt->bind_param("sssssi", $firstName, $lastName, $gender, $email, $dob, $userId);
        $result = $stmt->execute();

        // Update phone number
        $checkPhone = "SELECT * FROM user_phone WHERE user_id=?";
        $stmt = $conn->prepare($checkPhone);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $phoneResult = $stmt->get_result();

        if ($phoneResult->num_rows > 0) {
            $updatePhone = "UPDATE user_phone SET phone=? WHERE user_id=?";
            $stmt = $conn->prepare($updatePhone);
            $stmt->bind_param("si", $phoneNo, $userId);
            $stmt->execute();
        } else {
            $insertPhone = "INSERT INTO user_phone (user_id, phone) VALUES (?, ?)";
            $stmt = $conn->prepare($insertPhone);
            $stmt->bind_param("is", $userId, $phoneNo);
            $stmt->execute();
        }

        if ($result) {
            $updateSuccess = true;
        }
    }

    // Fetch Admin Details
    $userId = $_SESSION['user_id'];
    $sql1 = "SELECT * FROM users WHERE id=? AND role='admin'";
    $stmt = $conn->prepare($sql1);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $firstName = $row['first_name'] ?? '';
    $lastName = $row['last_name'] ?? '';
    $dob = $row['dob'] ?? '';
    $email = $row['email'] ?? '';
    $gender = $row['gender'] ?? '';
    $nic = $row['nic'] ?? '';

    // Fetch Admin Phone
    $sql1 = "SELECT phone FROM user_phone WHERE user_id=? LIMIT 1";
    $stmt = $conn->prepare($sql1);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $phoneResult = $stmt->get_result();
    $phoneRow = $phoneResult->fetch_assoc();
    $phoneNo = $phoneRow['phone'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ExamPro</title>
    <link rel="stylesheet" href="../styles/theme.css">
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>

    <div class="container mt-xl">
        <div class="mb-xl">
            <h1>Admin Dashboard</h1>
            <p>Manage exams, users, and system settings.</p>
        </div>

        <?php if ($updateSuccess): ?>
            <div class="alert alert-success mb-lg">
                <i class="fas fa-check-circle"></i> Profile updated successfully!
            </div>
        <?php endif; ?>

        <div class="grid-2">
            <!-- Admin Profile Section -->
            <div class="card mb-xl">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-user-shield"></i> Admin Profile</h2>
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
                                <input type="radio" name="gender" value="male" <?php if ($gender == "male") echo "checked"; ?>> Male
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="radio" name="gender" value="female" <?php if ($gender == "female") echo "checked"; ?>> Female
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

            <!-- Quick Stats or Actions -->
            <div class="card mb-xl">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-bolt"></i> Quick Actions</h2>
                </div>
                <div style="display: grid; gap: 1rem;">
                    <a href="../exams/addExam.php" class="btn btn-secondary" style="justify-content: flex-start;">
                        <i class="fas fa-plus-circle"></i> Add New Exam
                    </a>
                    <a href="#manage-exams" class="btn btn-outline" style="justify-content: flex-start;">
                        <i class="fas fa-list"></i> Manage Exams
                    </a>
                    <a href="#staff" class="btn btn-outline" style="justify-content: flex-start;">
                        <i class="fas fa-user-tie"></i> Manage Users
                    </a>
                    <a href="#messages" class="btn btn-outline" style="justify-content: flex-start;">
                        <i class="fas fa-envelope"></i> View Messages
                    </a>
                </div>
            </div>
        </div>

        <!-- Manage Exams Section -->
        <div class="card mb-xl" id="manage-exams">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-file-alt"></i> Manage Exams</h2>
            </div>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Exam Name</th>
                            <th>Department</th>
                            <th>Duration (min)</th>
                            <th>Created By</th>
                            <th>Scheduled</th>
                            <th>Operations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT e.id, e.code, e.name, e.duration_minutes, e.scheduled_at, 
                                       u.first_name, u.last_name, d.name as dept_name
                                FROM exam e
                                LEFT JOIN users u ON e.created_by = u.id
                                LEFT JOIN department d ON e.department_id = d.id
                                ORDER BY e.scheduled_at DESC";
                        $result = mysqli_query($conn, $sql);

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<tr>
                                      <td>' . htmlspecialchars($row['code']) . '</td>
                                      <td>' . htmlspecialchars($row['name']) . '</td>
                                      <td>' . htmlspecialchars($row['dept_name'] ?? 'N/A') . '</td>
                                      <td>' . htmlspecialchars($row['duration_minutes']) . '</td>
                                      <td>' . htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) . '</td>
                                      <td>' . htmlspecialchars($row['scheduled_at'] ?? 'Not scheduled') . '</td>
                                      <td>
                                          <div style="display: flex; gap: 0.5rem;">
                                              <a href="../exams/updateExam.php?updateid=' . $row['id'] . '" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                              <a href="../exams/deleteExam.php?deleteid=' . $row['id'] . '" class="btn btn-sm btn-secondary" onclick="return confirm(\'Are you sure you want to delete this exam?\')"><i class="fas fa-trash"></i></a>
                                          </div>
                                      </td>
                                  </tr>';
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>No exams found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- All Users Section -->
        <div class="card mb-xl" id="staff">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-users"></i> All Users</h2>
            </div>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Operations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT u.id, u.first_name, u.last_name, u.email, u.role, u.is_active, d.name as dept_name
                                FROM users u
                                LEFT JOIN department d ON u.department_id = d.id
                                ORDER BY u.role, u.first_name";
                        $result = mysqli_query($conn, $sql);

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $statusBadge = $row['is_active'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-error">Inactive</span>';
                                echo '<tr>
                                    <td>' . htmlspecialchars($row['id']) . '</td>
                                    <td>' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '</td>
                                    <td>' . htmlspecialchars($row['email']) . '</td>
                                    <td><span class="badge badge-info">' . htmlspecialchars(ucfirst($row['role'])) . '</span></td>
                                    <td>' . htmlspecialchars($row['dept_name'] ?? 'N/A') . '</td>
                                    <td>' . $statusBadge . '</td>
                                    <td>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <a href="../users/updateStaff.php?updateid=' . $row['id'] . '" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                            <a href="../users/deleteStaff.php?deleteid=' . $row['id'] . '" class="btn btn-sm btn-secondary" onclick="return confirm(\'Are you sure you want to delete this user?\')"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
                                    </tr>';
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>No users found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Messages Section (Complaints, Feedback, Reports) -->
        <div class="card mb-xl" id="messages">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-envelope"></i> User Messages</h2>
            </div>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>From</th>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT m.id, m.type, m.title, m.body, m.status, m.created_at,
                                       u.first_name, u.last_name, u.email
                                FROM message m
                                JOIN users u ON m.user_id = u.id
                                ORDER BY m.created_at DESC
                                LIMIT 50";
                        $result = mysqli_query($conn, $sql);

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $statusClass = $row['status'] == 'open' ? 'error' : ($row['status'] == 'resolved' ? 'success' : 'warning');
                                echo '<tr>
                                    <td>' . htmlspecialchars($row['id']) . '</td>
                                    <td>' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '</td>
                                    <td><span class="badge badge-info">' . htmlspecialchars(ucfirst($row['type'])) . '</span></td>
                                    <td>' . htmlspecialchars($row['title'] ?? substr($row['body'], 0, 30) . '...') . '</td>
                                    <td><span class="badge badge-' . $statusClass . '">' . htmlspecialchars(ucfirst($row['status'])) . '</span></td>
                                    <td>' . htmlspecialchars(date('Y-m-d H:i', strtotime($row['created_at']))) . '</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline">View</a>
                                    </td>
                                    </tr>';
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>No messages found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include("../includes/footer.php"); ?>
</body>
</html>
