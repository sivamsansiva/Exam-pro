<?php
include('../config/config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch Admin Details for display
$sql1 = "SELECT first_name, last_name FROM users WHERE id=? AND role='admin'";
$stmt = $conn->prepare($sql1);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$firstName = $row['first_name'] ?? '';
$lastName = $row['last_name'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ExamPro</title>
    <link rel="stylesheet" href="../styles/core.css">
    <link rel="stylesheet" href="../styles/components.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, var(--color-primary-600) 0%, var(--color-accent-600) 100%);
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

    <div class="container mt-xl mb-xl">
        <div class="dashboard-header">
            <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
            <p>Welcome back, <strong><?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></strong>! Manage exams, users, and system settings.</p>
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

        <!-- Quick Actions Card -->
        <div class="card mb-xl">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-bolt"></i> Quick Actions</h2>
                <p class="card-subtitle">Common administrative tasks</p>
            </div>
            <div class="quick-actions">
                <a href="../users/profile.php" class="btn btn-primary">
                    <i class="fas fa-user-circle"></i> My Profile
                </a>
                <a href="../exams/addExam.php" class="btn btn-success">
                    <i class="fas fa-plus-circle"></i> Add New Exam
                </a>
                <a href="#manage-exams" class="btn btn-outline">
                    <i class="fas fa-list"></i> Manage Exams
                </a>
                <a href="#staff" class="btn btn-outline">
                    <i class="fas fa-user-tie"></i> Manage Users
                </a>
                <a href="#messages" class="btn btn-outline">
                    <i class="fas fa-envelope"></i> View Messages
                </a>
            </div>
        </div>

        <!-- Manage Exams Section -->
        <div class="card mb-xl" id="manage-exams">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-file-alt"></i> Manage Exams</h2>
                <p class="card-subtitle">All examinations in the system</p>
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
                                              <a href="../exams/updateExam.php?updateid=' . $row['id'] . '" class="btn btn-sm btn-primary" title="Edit Exam"><i class="fas fa-edit"></i></a>
                                              <a href="../exams/deleteExam.php?deleteid=' . $row['id'] . '" class="btn btn-sm btn-error" onclick="return confirm(\'Are you sure you want to delete this exam?\')" title="Delete Exam"><i class="fas fa-trash"></i></a>
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
                <p class="card-subtitle">Manage system users and their roles</p>
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
                                            <a href="../users/updateStaff.php?updateid=' . $row['id'] . '" class="btn btn-sm btn-primary" title="Edit User"><i class="fas fa-edit"></i></a>
                                            <a href="../users/deleteStaff.php?deleteid=' . $row['id'] . '" class="btn btn-sm btn-error" onclick="return confirm(\'Are you sure you want to delete this user?\')" title="Delete User"><i class="fas fa-trash"></i></a>
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
                <p class="card-subtitle">Complaints, feedback, and reports from users</p>
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
