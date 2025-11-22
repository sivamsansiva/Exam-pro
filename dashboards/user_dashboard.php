<?php
// User Dashboard
include("../config/config.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../auth/login.php");
    exit();
}
$userId = $_SESSION['user_id'];
$email = $_SESSION['email'];

// Fetch user information
$query = "SELECT id, first_name, last_name, email, department_id FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

// Fetch registered exams with exam codes
$registeredExams = [];
$regQuery = "SELECT e.id, e.code, e.name, e.description, e.duration_minutes, e.scheduled_at, e.total_questions, e.max_score,
                    u.first_name as examiner_fname, u.last_name as examiner_lname, d.name as dept_name,
                    r.registered_at
             FROM exam_registration r
             JOIN exam e ON r.exam_id = e.id
             JOIN users u ON e.created_by = u.id
             JOIN department d ON e.department_id = d.id
             WHERE r.user_id = ? AND r.status = 'registered'
             ORDER BY e.scheduled_at ASC";
$stmt = $conn->prepare($regQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$registeredExams = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch upcoming exams (registered exams that are scheduled in the future)
$upcomingExams = [];
$upcomingQuery = "SELECT e.id, e.code, e.name, e.duration_minutes, e.scheduled_at, e.total_questions,
                         u.first_name as examiner_fname, u.last_name as examiner_lname, d.name as dept_name
                  FROM exam_registration r
                  JOIN exam e ON r.exam_id = e.id
                  JOIN users u ON e.created_by = u.id
                  JOIN department d ON e.department_id = d.id
                  WHERE r.user_id = ? AND r.status = 'registered' AND e.scheduled_at > NOW()
                  ORDER BY e.scheduled_at ASC";
$stmt = $conn->prepare($upcomingQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$upcomingExams = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch past exam results
$pastExams = [];
$pastQuery = "SELECT e.id, e.code, e.name, e.max_score, e.duration_minutes,
                     a.score, a.attempt_no, a.started_at, a.ended_at,
                     u.first_name as examiner_fname, u.last_name as examiner_lname, d.name as dept_name
              FROM exam_attempt a
              JOIN exam e ON a.exam_id = e.id
              JOIN users u ON e.created_by = u.id
              JOIN department d ON e.department_id = d.id
              WHERE a.user_id = ? AND a.completed = 1
              ORDER BY a.ended_at DESC";
$stmt = $conn->prepare($pastQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$pastExams = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate statistics
$totalRegistered = count($registeredExams);
$totalUpcoming = count($upcomingExams);
$totalCompleted = count($pastExams);

// Calculate average score
$averageScore = 0;
if ($totalCompleted > 0) {
    $totalScore = 0;
    foreach ($pastExams as $exam) {
        $percentage = ($exam['score'] / $exam['max_score']) * 100;
        $totalScore += $percentage;
    }
    $averageScore = $totalScore / $totalCompleted;
}

// Get recent activity (last 3 completed exams)
$recentActivity = array_slice($pastExams, 0, 3);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - ExamPro</title>
    <link rel="stylesheet" href="../styles/core.css">
    <link rel="stylesheet" href="../styles/components.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php include("../includes/header.php"); ?>

    <div class="container mt-xl mb-xl">
        <!-- Welcome Section -->
        <div class="card mb-xl" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
            <div style="padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h1 style="margin: 0 0 0.25rem 0; font-size: 1.5rem; font-weight: 600;">
                            <i class="fas fa-tachometer-alt"></i> My Dashboard
                        </h1>
                        <p style="opacity: 0.95; font-size: 0.95rem; margin: 0;">
                            Welcome back, <strong><?php echo htmlspecialchars($user_data['first_name'] . ' ' . $user_data['last_name']); ?></strong>!
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <p style="opacity: 0.9; margin: 0; font-size: 0.85rem;">
                            <i class="fas fa-calendar"></i> <?php echo date('l, F j, Y'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid-4 mb-xl">
            <div class="card" style="border-left: 4px solid #667eea;">
                <div style="padding: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <p style="color: var(--text-muted); margin: 0 0 0.35rem 0; font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Registered</p>
                            <h2 style="margin: 0; font-size: 1.75rem; color: #667eea;"><?php echo $totalRegistered; ?></h2>
                        </div>
                        <div style="width: 45px; height: 45px; background: rgba(102, 126, 234, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-clipboard-check" style="font-size: 1.25rem; color: #667eea;"></i>
                        </div>
                    </div>
                    <p style="margin: 0.75rem 0 0 0; font-size: 0.875rem; color: var(--text-muted);">
                        <i class="fas fa-arrow-up" style="color: #10b981;"></i> Total exams enrolled
                    </p>
                </div>
            </div>

            <div class="card" style="border-left: 4px solid #f59e0b;">
                <div style="padding: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <p style="color: var(--text-muted); margin: 0 0 0.35rem 0; font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Upcoming</p>
                            <h2 style="margin: 0; font-size: 1.75rem; color: #f59e0b;"><?php echo $totalUpcoming; ?></h2>
                        </div>
                        <div style="width: 45px; height: 45px; background: rgba(245, 158, 11, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-calendar-alt" style="font-size: 1.25rem; color: #f59e0b;"></i>
                        </div>
                    </div>
                    <p style="margin: 0.75rem 0 0 0; font-size: 0.875rem; color: var(--text-muted);">
                        <i class="fas fa-clock" style="color: #f59e0b;"></i> Scheduled exams
                    </p>
                </div>
            </div>

            <div class="card" style="border-left: 4px solid #10b981;">
                <div style="padding: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <p style="color: var(--text-muted); margin: 0 0 0.35rem 0; font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Completed</p>
                            <h2 style="margin: 0; font-size: 1.75rem; color: #10b981;"><?php echo $totalCompleted; ?></h2>
                        </div>
                        <div style="width: 45px; height: 45px; background: rgba(16, 185, 129, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-check-circle" style="font-size: 1.25rem; color: #10b981;"></i>
                        </div>
                    </div>
                    <p style="margin: 0.75rem 0 0 0; font-size: 0.875rem; color: var(--text-muted);">
                        <i class="fas fa-trophy" style="color: #10b981;"></i> Exams finished
                    </p>
                </div>
            </div>

            <div class="card" style="border-left: 4px solid #8b5cf6;">
                <div style="padding: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <p style="color: var(--text-muted); margin: 0 0 0.35rem 0; font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Avg Score</p>
                            <h2 style="margin: 0; font-size: 1.75rem; color: #8b5cf6;"><?php echo number_format($averageScore, 1); ?>%</h2>
                        </div>
                        <div style="width: 45px; height: 45px; background: rgba(139, 92, 246, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-chart-line" style="font-size: 1.25rem; color: #8b5cf6;"></i>
                        </div>
                    </div>
                    <p style="margin: 0.75rem 0 0 0; font-size: 0.875rem; color: var(--text-muted);">
                        <i class="fas fa-star" style="color: #8b5cf6;"></i> Overall performance
                    </p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid-3 mb-xl">
            <a href="../exams/registerExam.php" class="card" style="text-decoration: none; color: inherit; transition: all 0.3s; cursor: pointer; border: 2px solid transparent;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='#667eea'; this.style.boxShadow='0 8px 20px rgba(102, 126, 234, 0.2)'" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='transparent'; this.style.boxShadow=''">
                <div style="text-align: center; padding: 1.25rem;">
                    <div style="width: 55px; height: 55px; margin: 0 auto 0.75rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);">
                        <i class="fas fa-user-plus" style="font-size: 1.4rem; color: white;"></i>
                    </div>
                    <h3 style="margin: 0 0 0.35rem 0; font-size: 1rem; font-weight: 600;">Register for Exam</h3>
                    <p style="color: var(--text-muted); margin: 0; font-size: 0.85rem;">Enroll in new exams</p>
                </div>
            </a>
            <a href="../exams/attemptExam.php" class="card" style="text-decoration: none; color: inherit; transition: all 0.3s; cursor: pointer; border: 2px solid transparent;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='#10b981'; this.style.boxShadow='0 8px 20px rgba(16, 185, 129, 0.2)'" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='transparent'; this.style.boxShadow=''">
                <div style="text-align: center; padding: 1.25rem;">
                    <div style="width: 55px; height: 55px; margin: 0 auto 0.75rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                        <i class="fas fa-pen-to-square" style="font-size: 1.4rem; color: white;"></i>
                    </div>
                    <h3 style="margin: 0 0 0.35rem 0; font-size: 1rem; font-weight: 600;">Take Exam</h3>
                    <p style="color: var(--text-muted); margin: 0; font-size: 0.85rem;">Start your registered exams</p>
                </div>
            </a>
            <a href="../users/profile.php" class="card" style="text-decoration: none; color: inherit; transition: all 0.3s; cursor: pointer; border: 2px solid transparent;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='#f59e0b'; this.style.boxShadow='0 8px 20px rgba(245, 158, 11, 0.2)'" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='transparent'; this.style.boxShadow=''">
                <div style="text-align: center; padding: 1.25rem;">
                    <div style="width: 55px; height: 55px; margin: 0 auto 0.75rem; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
                        <i class="fas fa-user-edit" style="font-size: 1.4rem; color: white;"></i>
                    </div>
                    <h3 style="margin: 0 0 0.35rem 0; font-size: 1rem; font-weight: 600;">My Profile</h3>
                    <p style="color: var(--text-muted); margin: 0; font-size: 0.85rem;">Update your details</p>
                </div>
            </a>
        </div>

        <!-- Registered Exams with Codes -->
        <div class="card mb-xl">
            <div class="card-header" style="border-bottom: 2px solid #f3f4f6;">
                <h2 class="card-title" style="font-size: 1.25rem; font-weight: 600;"><i class="fas fa-clipboard-check"></i> My Registered Exams</h2>
                <p class="card-subtitle">Exams you have registered for with access codes</p>
            </div>
            <?php if (count($registeredExams) > 0): ?>
                <div style="padding: 1.25rem;">
                    <div class="grid-2" style="gap: 1.5rem;">
                        <?php foreach ($registeredExams as $exam): ?>
                            <div class="card" style="border: 1px solid #e5e7eb; transition: all 0.3s;" onmouseover="this.style.borderColor='#667eea'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.15)'" onmouseout="this.style.borderColor='#e5e7eb'; this.style.boxShadow=''">
                                <div style="padding: 1.5rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                        <div style="flex: 1;">
                                            <span class="badge badge-info" style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($exam['code']); ?></span>
                                            <h3 style="margin: 0.5rem 0; font-size: 1.1rem; font-weight: 600; color: #1f2937;"><?php echo htmlspecialchars($exam['name']); ?></h3>
                                            <p style="margin: 0.25rem 0; color: var(--text-muted); font-size: 0.875rem;">
                                                <i class="fas fa-building"></i> <?php echo htmlspecialchars($exam['dept_name']); ?>
                                            </p>
                                        </div>
                                    </div>

                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin: 1rem 0; padding: 1rem; background: #f9fafb; border-radius: 8px;">
                                        <div>
                                            <p style="margin: 0; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Duration</p>
                                            <p style="margin: 0.25rem 0 0 0; font-weight: 600; color: #1f2937;">
                                                <i class="fas fa-clock" style="color: #667eea;"></i> <?php echo htmlspecialchars($exam['duration_minutes']); ?> min
                                            </p>
                                        </div>
                                        <div>
                                            <p style="margin: 0; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Questions</p>
                                            <p style="margin: 0.25rem 0 0 0; font-weight: 600; color: #1f2937;">
                                                <i class="fas fa-question-circle" style="color: #10b981;"></i> <?php echo htmlspecialchars($exam['total_questions']); ?> Qs
                                            </p>
                                        </div>
                                        <div style="grid-column: 1 / -1;">
                                            <p style="margin: 0; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Scheduled</p>
                                            <p style="margin: 0.25rem 0 0 0; font-weight: 600; color: #1f2937;">
                                                <i class="fas fa-calendar" style="color: #f59e0b;"></i>
                                                <?php echo $exam['scheduled_at'] ? htmlspecialchars(date('M d, Y H:i', strtotime($exam['scheduled_at']))) : 'Not scheduled'; ?>
                                            </p>
                                        </div>
                                    </div>

                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem;">
                                        <p style="margin: 0 0 0.25rem 0; font-size: 0.75rem; color: rgba(255,255,255,0.9); text-transform: uppercase; font-weight: 600;">Quiz Access Code</p>
                                        <code style="background: rgba(255,255,255,0.2); padding: 0.5rem 0.75rem; border-radius: 6px; font-weight: bold; color: white; font-size: 1.1rem; display: inline-block; letter-spacing: 1px;">
                                            <?php echo htmlspecialchars($exam['code']); ?>
                                        </code>
                                    </div>

                                    <a href="../exams/attemptExam.php?id=<?php echo $exam['id']; ?>" class="btn btn-primary" style="width: 100%; justify-content: center;">
                                        <i class="fas fa-play"></i> Start Exam
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 2.5rem 1.5rem;">
                    <div style="width: 65px; height: 65px; margin: 0 auto 1rem; background: rgba(102, 126, 234, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-inbox" style="font-size: 2rem; color: #667eea;"></i>
                    </div>
                    <h3 style="margin: 0 0 0.35rem 0; color: #1f2937; font-size: 1.1rem;">No Registered Exams</h3>
                    <p style="color: var(--text-muted); margin: 0 0 1.5rem 0;">You haven't registered for any exams yet. Start by enrolling in an exam.</p>
                    <a href="../exams/registerExam.php" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Register for an Exam
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Upcoming Exams -->
        <?php if (count($upcomingExams) > 0): ?>
            <div class="card mb-xl">
                <div class="card-header" style="border-bottom: 2px solid #f3f4f6;">
                    <h2 class="card-title" style="font-size: 1.25rem; font-weight: 600;"><i class="fas fa-calendar-alt"></i> Upcoming Exams</h2>
                    <p class="card-subtitle">Exams scheduled in the near future</p>
                </div>
                <div style="padding: 1.25rem;">
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <?php foreach ($upcomingExams as $exam):
                            $scheduledDate = strtotime($exam['scheduled_at']);
                            $now = time();
                            $daysUntil = floor(($scheduledDate - $now) / (60 * 60 * 24));
                            $hoursUntil = floor(($scheduledDate - $now) / (60 * 60));
                        ?>
                            <div class="card" style="border: 1px solid #e5e7eb; border-left: 4px solid #f59e0b;">
                                <div style="padding: 1.25rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                                    <div style="flex: 1; min-width: 200px;">
                                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                                            <span class="badge badge-warning"><?php echo htmlspecialchars($exam['code']); ?></span>
                                            <h4 style="margin: 0; font-size: 1.05rem; font-weight: 600;"><?php echo htmlspecialchars($exam['name']); ?></h4>
                                        </div>
                                        <p style="margin: 0; color: var(--text-muted); font-size: 0.875rem;">
                                            <i class="fas fa-building"></i> <?php echo htmlspecialchars($exam['dept_name']); ?>
                                        </p>
                                    </div>

                                    <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                                        <div>
                                            <p style="margin: 0; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Date & Time</p>
                                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">
                                                <i class="fas fa-calendar" style="color: #f59e0b;"></i>
                                                <?php echo htmlspecialchars(date('M d, Y H:i', strtotime($exam['scheduled_at']))); ?>
                                            </p>
                                        </div>
                                        <div>
                                            <p style="margin: 0; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Duration</p>
                                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">
                                                <i class="fas fa-clock" style="color: #667eea;"></i>
                                                <?php echo htmlspecialchars($exam['duration_minutes']); ?> min
                                            </p>
                                        </div>
                                        <div>
                                            <p style="margin: 0; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Questions</p>
                                            <p style="margin: 0.25rem 0 0 0; font-weight: 600;">
                                                <i class="fas fa-question-circle" style="color: #10b981;"></i>
                                                <?php echo htmlspecialchars($exam['total_questions']); ?> Qs
                                            </p>
                                        </div>
                                        <div>
                                            <p style="margin: 0; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Time Until</p>
                                            <p style="margin: 0.25rem 0 0 0; font-weight: 600; color: #f59e0b;">
                                                <i class="fas fa-hourglass-half"></i>
                                                <?php
                                                if ($daysUntil > 0) {
                                                    echo $daysUntil . ' day' . ($daysUntil > 1 ? 's' : '');
                                                } else {
                                                    echo $hoursUntil . ' hour' . ($hoursUntil > 1 ? 's' : '');
                                                }
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Past Exam Results -->
        <div class="card mb-xl">
            <div class="card-header" style="border-bottom: 2px solid #f3f4f6;">
                <h2 class="card-title" style="font-size: 1.25rem; font-weight: 600;"><i class="fas fa-chart-line"></i> Past Exam Results</h2>
                <p class="card-subtitle">Your completed exams and performance</p>
            </div>
            <?php if (count($pastExams) > 0): ?>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Exam</th>
                                <th>Department</th>
                                <th>Attempt</th>
                                <th>Score</th>
                                <th>Percentage</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pastExams as $exam):
                                $percentage = ($exam['score'] / $exam['max_score']) * 100;
                                $status = $percentage >= 60 ? 'Passed' : 'Failed';
                                $badgeClass = $percentage >= 60 ? 'badge-success' : 'badge-danger';
                                $progressColor = $percentage >= 80 ? '#10b981' : ($percentage >= 60 ? '#f59e0b' : '#ef4444');
                            ?>
                                <tr>
                                    <td>
                                        <div>
                                            <span class="badge badge-secondary" style="margin-bottom: 0.25rem;"><?php echo htmlspecialchars($exam['code']); ?></span>
                                            <div style="font-weight: 600;"><?php echo htmlspecialchars($exam['name']); ?></div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($exam['dept_name']); ?></td>
                                    <td>
                                        <span style="background: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 4px; font-weight: 600;">
                                            #<?php echo htmlspecialchars($exam['attempt_no']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo number_format($exam['score'], 1); ?> / <?php echo number_format($exam['max_score'], 1); ?></td>
                                    <td>
                                        <div style="min-width: 120px;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                                <span style="font-weight: 600; color: <?php echo $progressColor; ?>;"><?php echo number_format($percentage, 1); ?>%</span>
                                            </div>
                                            <div style="width: 100%; height: 6px; background: #e5e7eb; border-radius: 10px; overflow: hidden;">
                                                <div style="width: <?php echo $percentage; ?>%; height: 100%; background: <?php echo $progressColor; ?>; transition: width 0.3s;"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $status; ?></span></td>
                                    <td style="color: var(--text-muted); font-size: 0.875rem;">
                                        <?php echo htmlspecialchars(date('M d, Y', strtotime($exam['ended_at']))); ?>
                                        <div style="font-size: 0.75rem;"><?php echo htmlspecialchars(date('H:i', strtotime($exam['ended_at']))); ?></div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 2.5rem 1.5rem;">
                    <div style="width: 65px; height: 65px; margin: 0 auto 1rem; background: rgba(139, 92, 246, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-line" style="font-size: 2rem; color: #8b5cf6;"></i>
                    </div>
                    <h3 style="margin: 0 0 0.35rem 0; color: #1f2937; font-size: 1.1rem;">No Exam Results Yet</h3>
                    <p style="color: var(--text-muted); margin: 0 0 1.5rem 0;">Complete your first exam to see your performance here.</p>
                    <a href="../exams/attemptExam.php" class="btn btn-primary">
                        <i class="fas fa-pen-to-square"></i> Take an Exam
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include("../includes/footer.php"); ?>
</body>

</html>
