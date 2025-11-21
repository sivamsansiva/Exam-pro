<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('config/config.php');

if (!isset($_SESSION['email'])) {
    header("Location: auth/login.php");
    exit();
}

// Get user information
$userEmail = $_SESSION['email'];
$userRole = $_SESSION['role'] ?? 'staff';
$userDepartment = $_SESSION['department'] ?? null;
$userId = $_SESSION['user_id'] ?? null;

// Fetch user's full information
$userQuery = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$userResult = $stmt->get_result();
$userData = $userResult->fetch_assoc();

// Fetch department exams (exams from user's department only)
$departmentExams = [];
if ($userDepartment) {
    $deptExamQuery = "SELECT e.id, e.code, e.name, e.description, e.duration_minutes, e.scheduled_at,
                             u.first_name, u.last_name, u.email as examiner_email, d.name as dept_name
                      FROM exam e
                      JOIN users u ON e.created_by = u.id
                      JOIN department d ON e.department_id = d.id
                      WHERE e.department_id = ?
                      ORDER BY e.scheduled_at DESC, e.id DESC";
    $stmt = $conn->prepare($deptExamQuery);
    $stmt->bind_param("i", $userDepartment);
    $stmt->execute();
    $departmentExams = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Fetch general/common exams (exams with no specific department - available to all)
$generalExams = [];
$generalExamQuery = "SELECT e.id, e.code, e.name, e.description, e.duration_minutes, e.scheduled_at,
                            u.first_name, u.last_name, u.email as examiner_email
                     FROM exam e
                     JOIN users u ON e.created_by = u.id
                     WHERE e.department_id IS NULL
                     ORDER BY e.scheduled_at DESC, e.id DESC
                     LIMIT 6";
$generalExams = $conn->query($generalExamQuery)->fetch_all(MYSQLI_ASSOC);

// Fetch registered exams
$registeredExams = [];
if ($userId) {
    $regQuery = "SELECT e.id, e.code, e.name, e.duration_minutes, e.scheduled_at,
                        u.first_name, u.last_name, d.name as dept_name, r.registered_at
                 FROM exam_registration r
                 JOIN exam e ON r.exam_id = e.id
                 JOIN users u ON e.created_by = u.id
                 JOIN department d ON e.department_id = d.id
                 WHERE r.user_id = ? AND r.status = 'registered'
                 ORDER BY r.registered_at DESC";
    $stmt = $conn->prepare($regQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $registeredExams = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Fetch attended exams (completed attempts)
$attendedExams = [];
if ($userId) {
    $attendedQuery = "SELECT e.id, e.code, e.name, e.duration_minutes,
                             u.first_name, u.last_name, d.name as dept_name,
                             a.score, a.ended_at
                      FROM exam_attempt a
                      JOIN exam e ON a.exam_id = e.id
                      JOIN users u ON e.created_by = u.id
                      JOIN department d ON e.department_id = d.id
                      WHERE a.user_id = ? AND a.completed = 1
                      ORDER BY a.score DESC, a.ended_at DESC";
    $stmt = $conn->prepare($attendedQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $attendedExams = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ExamPro</title>
    <link rel="stylesheet" href="styles/core.css">

    <style>
        /* ============================================
       INDEX PAGE STYLES
       ============================================ */

        /* Hero Section */
        .main-content {
            min-height: calc(100vh - 72px);
        }

        .hero-section {
            background: linear-gradient(135deg, var(--color-primary-600) 0%, var(--color-primary-700) 50%, var(--color-accent-600) 100%);
            padding: var(--spacing-4xl) 0;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-3xl);
            align-items: center;
        }

        .hero-text {
            color: var(--text-inverse);
        }

        .hero-title {
            font-size: var(--font-size-5xl);
            font-weight: var(--font-weight-extrabold);
            margin-bottom: var(--spacing-md);
            line-height: var(--line-height-tight);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .hero-subtitle {
            font-size: var(--font-size-xl);
            margin-bottom: var(--spacing-2xl);
            opacity: 0.95;
            line-height: var(--line-height-relaxed);
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: var(--spacing-md);
        }

        .hero-image img {
            width: 100%;
            height: auto;
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-2xl);
            transition: transform var(--transition-slow);
        }

        .hero-image img:hover {
            transform: scale(1.02) translateY(-8px);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: var(--spacing-sm);
            padding: var(--spacing-sm) var(--spacing-lg);
            font-size: var(--font-size-sm);
            font-weight: var(--font-weight-semibold);
            text-decoration: none;
            border-radius: var(--radius-lg);
            border: 2px solid transparent;
            cursor: pointer;
            transition: all var(--transition-base);
            white-space: nowrap;
        }

        .btn i {
            margin-right: 0;
        }

        .btn-primary {
            background: var(--color-white);
            color: var(--color-primary-700);
            border-color: var(--color-white);
        }

        .btn-primary:hover {
            background: var(--color-primary-50);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary {
            background: var(--color-accent-600);
            color: var(--text-inverse);
            border-color: var(--color-accent-600);
        }

        .btn-secondary:hover {
            background: var(--color-accent-700);
            border-color: var(--color-accent-700);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-outline {
            background: transparent;
            color: var(--text-inverse);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--color-white);
            transform: translateY(-2px);
        }

        .btn-lg {
            padding: var(--spacing-md) var(--spacing-xl);
            font-size: var(--font-size-base);
        }

        /* Stats Section */
        .stats-section {
            padding: var(--spacing-3xl) 0;
            background: var(--color-white);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: var(--spacing-lg);
        }

        .stat-card {
            background: var(--color-white);
            padding: var(--spacing-xl);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            gap: var(--spacing-lg);
            transition: all var(--transition-base);
            border: 1px solid var(--color-border);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
            border-color: var(--color-primary-200);
        }

        .stat-icon {
            width: 64px;
            height: 64px;
            border-radius: var(--radius-xl);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            flex-shrink: 0;
        }

        .stat-card:nth-child(1) .stat-icon {
            background: linear-gradient(135deg, var(--color-primary-500), var(--color-primary-600));
            color: var(--text-inverse);
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3);
        }

        .stat-card:nth-child(2) .stat-icon {
            background: linear-gradient(135deg, var(--color-info), #2563EB);
            color: var(--text-inverse);
            box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
        }

        .stat-card:nth-child(3) .stat-icon {
            background: linear-gradient(135deg, var(--color-warning), #D97706);
            color: var(--text-inverse);
            box-shadow: 0 8px 16px rgba(245, 158, 11, 0.3);
        }

        .stat-card:nth-child(4) .stat-icon {
            background: linear-gradient(135deg, var(--color-success), #059669);
            color: var(--text-inverse);
            box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
        }

        .stat-icon i {
            margin-right: 0;
        }

        .stat-content {
            flex: 1;
        }

        .stat-number {
            font-size: var(--font-size-3xl);
            font-weight: var(--font-weight-extrabold);
            color: var(--text-primary);
            margin: 0 0 var(--spacing-xs) 0;
            line-height: 1;
        }

        .stat-label {
            font-size: var(--font-size-sm);
            color: var(--text-secondary);
            font-weight: var(--font-weight-medium);
            margin: 0;
        }

        /* Sections */
        .exams-section {
            padding: var(--spacing-3xl) 0;
        }

        .exams-section:nth-child(even) {
            background: var(--color-secondary-50);
        }

        .section-header {
            text-align: center;
            margin-bottom: var(--spacing-3xl);
        }

        .section-header.text-center {
            text-align: center;
        }

        .section-title {
            font-size: var(--font-size-3xl);
            font-weight: var(--font-weight-bold);
            color: var(--text-primary);
            margin-bottom: var(--spacing-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--spacing-sm);
        }

        .section-title i {
            color: var(--color-primary-600);
            margin-right: 0;
        }

        .section-subtitle {
            font-size: var(--font-size-lg);
            color: var(--text-secondary);
            margin: 0;
        }

        /* Exam Cards */
        .exams-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: var(--spacing-xl);
        }

        .exam-card {
            background: var(--color-white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            transition: all var(--transition-base);
            border: 1px solid var(--color-border);
            display: flex;
            flex-direction: column;
        }

        .exam-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-2xl);
            border-color: var(--color-primary-300);
        }

        .exam-card.completed {
            border-left: 4px solid var(--color-success);
        }

        .exam-card-header {
            padding: var(--spacing-lg);
            border-bottom: 1px solid var(--color-divider);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: var(--spacing-md);
        }

        .exam-title {
            font-size: var(--font-size-lg);
            font-weight: var(--font-weight-bold);
            color: var(--text-primary);
            margin: 0;
            line-height: var(--line-height-tight);
        }

        .badge {
            display: inline-block;
            padding: var(--spacing-xs) var(--spacing-md);
            font-size: var(--font-size-xs);
            font-weight: var(--font-weight-semibold);
            border-radius: var(--radius-full);
            white-space: nowrap;
        }

        .badge-info {
            background: var(--color-info-light);
            color: var(--color-info);
        }

        .badge-success {
            background: var(--color-success-light);
            color: var(--color-success);
        }

        .exam-card-body {
            padding: var(--spacing-lg);
            flex: 1;
        }

        .exam-meta {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-sm);
        }

        .exam-meta-item {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            font-size: var(--font-size-sm);
            color: var(--text-secondary);
        }

        .exam-meta-item i {
            width: 20px;
            color: var(--color-primary-500);
            margin-right: 0;
        }

        .exam-card-footer {
            padding: var(--spacing-lg);
            border-top: 1px solid var(--color-divider);
            display: flex;
            gap: var(--spacing-sm);
        }

        .btn-sm {
            padding: var(--spacing-xs) var(--spacing-md);
            font-size: var(--font-size-xs);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: var(--spacing-4xl) var(--spacing-xl);
            background: var(--color-white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-sm);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--color-secondary-300);
            margin-bottom: var(--spacing-lg);
            margin-right: 0;
        }

        .empty-state h3 {
            font-size: var(--font-size-xl);
            font-weight: var(--font-weight-semibold);
            color: var(--text-primary);
            margin-bottom: var(--spacing-sm);
        }

        .empty-state p {
            font-size: var(--font-size-base);
            color: var(--text-secondary);
            margin: 0;
        }

        /* Features Section */
        .features-section {
            padding: var(--spacing-4xl) 0;
            background: linear-gradient(180deg, var(--color-white) 0%, var(--color-secondary-50) 100%);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: var(--spacing-2xl);
        }

        .feature-card {
            background: var(--color-white);
            padding: var(--spacing-2xl);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            transition: all var(--transition-base);
            border: 1px solid var(--color-border);
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-2xl);
            border-color: var(--color-primary-300);
        }

        .feature-icon {
            width: 72px;
            height: 72px;
            border-radius: var(--radius-xl);
            background: linear-gradient(135deg, var(--color-primary-500), var(--color-accent-500));
            color: var(--text-inverse);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: var(--spacing-lg);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
        }

        .feature-icon i {
            margin-right: 0;
        }

        .feature-title {
            font-size: var(--font-size-xl);
            font-weight: var(--font-weight-bold);
            color: var(--text-primary);
            margin-bottom: var(--spacing-md);
        }

        .feature-description {
            font-size: var(--font-size-base);
            color: var(--text-secondary);
            line-height: var(--line-height-relaxed);
            margin-bottom: var(--spacing-lg);
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            padding: var(--spacing-xs) 0;
            font-size: var(--font-size-sm);
            color: var(--text-secondary);
        }

        .feature-list li i {
            color: var(--color-success);
            font-size: 0.875rem;
            margin-right: 0;
        }

        /* Quick Links Section */
        .quick-links-section {
            padding: var(--spacing-3xl) 0;
            background: var(--color-secondary-50);
        }

        .quick-links-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-lg);
        }

        .quick-link-card {
            background: var(--color-white);
            padding: var(--spacing-2xl);
            border-radius: var(--radius-xl);
            text-align: center;
            text-decoration: none;
            color: var(--text-primary);
            transition: all var(--transition-base);
            box-shadow: var(--shadow-sm);
            border: 2px solid var(--color-border);
        }

        .quick-link-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-xl);
            border-color: var(--color-primary-500);
        }

        .quick-link-card i {
            font-size: 2.5rem;
            color: var(--color-primary-600);
            margin-bottom: var(--spacing-md);
            margin-right: 0;
        }

        .quick-link-card h3 {
            font-size: var(--font-size-lg);
            font-weight: var(--font-weight-bold);
            margin-bottom: var(--spacing-sm);
            color: var(--text-primary);
        }

        .quick-link-card p {
            font-size: var(--font-size-sm);
            color: var(--text-secondary);
            margin: 0;
        }

        /* ============================================
       RESPONSIVE DESIGN
       ============================================ */
        @media (max-width: 1024px) {
            .hero-content {
                grid-template-columns: 1fr;
                gap: var(--spacing-2xl);
            }

            .hero-image {
                order: -1;
            }

            .hero-title {
                font-size: var(--font-size-4xl);
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: var(--spacing-2xl) 0;
            }

            .hero-title {
                font-size: var(--font-size-3xl);
            }

            .hero-subtitle {
                font-size: var(--font-size-lg);
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .exams-grid {
                grid-template-columns: 1fr;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .hero-actions {
                flex-direction: column;
                width: 100%;
            }

            .hero-actions .btn {
                width: 100%;
            }

            .section-title {
                font-size: var(--font-size-2xl);
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <?php include("includes/header.php"); ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="hero-content">
                    <div class="hero-text">
                        <h1 class="hero-title">Welcome to ExamPro</h1>
                        <p class="hero-subtitle">The reliable exam platform. Secure. Easy to use. Dedicated to your success.</p>
                        <div class="hero-actions">
                            <a href="exams/registerExam.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus"></i>
                                Register for Exam
                            </a>
                            <a href="exams/attemptExam.php" class="btn btn-outline btn-lg">
                                <i class="fas fa-pen-to-square"></i>
                                Take Exam
                            </a>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'staff'): ?>
                                <a href="dashboards/user_dashboard.php" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-tachometer-alt"></i>
                                    My Dashboard
                                </a>
                            <?php elseif (isset($_SESSION['role'])): ?>
                                <a href="dashboards/<?php echo $_SESSION['role']; ?>_dashboard.php" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-tachometer-alt"></i>
                                    My Dashboard
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="hero-image">
                        <img src="assets/images/exampro.webp" alt="ExamPro Platform" onerror="this.style.display='none'">
                    </div>
                </div>
            </div>
        </section>

        <!-- User Stats Section -->
        <section class="stats-section">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number"><?php echo count($departmentExams); ?></h3>
                            <p class="stat-label">Department Exams</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number"><?php echo count($generalExams); ?></h3>
                            <p class="stat-label">Available Exams</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number"><?php echo count($registeredExams); ?></h3>
                            <p class="stat-label">Registered Exams</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number"><?php echo count($attendedExams); ?></h3>
                            <p class="stat-label">Completed Exams</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Department Exams Section -->
        <?php if (count($departmentExams) > 0): ?>
            <section class="exams-section">
                <div class="container">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-building"></i>
                            My Department Exams
                        </h2>
                        <p class="section-subtitle">Exams available in your department</p>
                    </div>
                    <div class="exams-grid">
                        <?php foreach ($departmentExams as $exam): ?>
                            <div class="exam-card">
                                <div class="exam-card-header">
                                    <h3 class="exam-title"><?php echo htmlspecialchars($exam['name']); ?></h3>
                                    <span class="badge badge-info"><?php echo htmlspecialchars($exam['dept_name']); ?></span>
                                </div>
                                <div class="exam-card-body">
                                    <div class="exam-meta">
                                        <div class="exam-meta-item">
                                            <i class="fas fa-user-tie"></i>
                                            <span><?php echo htmlspecialchars($exam['first_name'] . ' ' . $exam['last_name']); ?></span>
                                        </div>
                                        <div class="exam-meta-item">
                                            <i class="fas fa-clock"></i>
                                            <span><?php echo htmlspecialchars($exam['duration_minutes']); ?> min</span>
                                        </div>
                                        <div class="exam-meta-item">
                                            <i class="fas fa-calendar"></i>
                                            <span><?php echo $exam['scheduled_at'] ? htmlspecialchars(date('M d, Y', strtotime($exam['scheduled_at']))) : 'Not scheduled'; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="exam-card-footer">
                                    <a href="exams/registerExam.php?id=<?php echo $exam['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-user-plus"></i>
                                        Register
                                    </a>
                                    <a href="exams/registerExam.php?id=<?php echo $exam['id']; ?>" class="btn btn-sm btn-outline">
                                        <i class="fas fa-info-circle"></i>
                                        View Details
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- General/Available Exams Section -->
        <section class="exams-section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-list-alt"></i>
                        Common Exams
                    </h2>
                    <p class="section-subtitle">General exams available to all departments</p>
                </div>
                <div class="exams-grid">
                    <?php if (count($generalExams) > 0): ?>
                        <?php foreach ($generalExams as $exam): ?>
                            <div class="exam-card">
                                <div class="exam-card-header">
                                    <h3 class="exam-title"><?php echo htmlspecialchars($exam['name']); ?></h3>
                                    <span class="badge badge-success">General</span>
                                </div>
                                <div class="exam-card-body">
                                    <div class="exam-meta">
                                        <div class="exam-meta-item">
                                            <i class="fas fa-user-tie"></i>
                                            <span><?php echo htmlspecialchars($exam['first_name'] . ' ' . $exam['last_name']); ?></span>
                                        </div>
                                        <div class="exam-meta-item">
                                            <i class="fas fa-clock"></i>
                                            <span><?php echo htmlspecialchars($exam['duration_minutes']); ?> min</span>
                                        </div>
                                        <div class="exam-meta-item">
                                            <i class="fas fa-calendar"></i>
                                            <span><?php echo $exam['scheduled_at'] ? htmlspecialchars(date('M d, Y', strtotime($exam['scheduled_at']))) : 'Not scheduled'; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="exam-card-footer">
                                    <a href="exams/registerExam.php?id=<?php echo $exam['id']; ?>" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-user-plus"></i>
                                        Register
                                    </a>
                                    <a href="exams/registerExam.php?id=<?php echo $exam['id']; ?>" class="btn btn-sm btn-outline">
                                        <i class="fas fa-info-circle"></i>
                                        View Details
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h3>No Exams Available</h3>
                            <p>There are currently no exams available. Please check back later.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Attended Exams Section -->
        <?php if (count($attendedExams) > 0): ?>
            <section class="exams-section">
                <div class="container">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-chart-line"></i>
                            My Completed Exams
                        </h2>
                        <p class="section-subtitle">View your exam results and performance</p>
                    </div>
                    <div class="exams-grid">
                        <?php foreach ($attendedExams as $exam): ?>
                            <div class="exam-card completed">
                                <div class="exam-card-header">
                                    <h3 class="exam-title"><?php echo htmlspecialchars($exam['name']); ?></h3>
                                    <span class="badge badge-success">
                                        <?php echo number_format($exam['score'], 2); ?>%
                                    </span>
                                </div>
                                <div class="exam-card-body">
                                    <div class="exam-meta">
                                        <div class="exam-meta-item">
                                            <i class="fas fa-building"></i>
                                            <span><?php echo htmlspecialchars($exam['dept_name']); ?></span>
                                        </div>
                                        <div class="exam-meta-item">
                                            <i class="fas fa-user-tie"></i>
                                            <span><?php echo htmlspecialchars($exam['first_name'] . ' ' . $exam['last_name']); ?></span>
                                        </div>
                                        <div class="exam-meta-item">
                                            <i class="fas fa-trophy"></i>
                                            <span><?php echo $exam['score'] >= 75 ? 'Pass' : 'Review Required'; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="exam-card-footer">
                                    <a href="exams/result.php?id=<?php echo $exam['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                        View Result
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- Features Section -->
        <section class="features-section">
            <div class="container">
                <div class="section-header text-center">
                    <h2 class="section-title">Why Choose ExamPro?</h2>
                    <p class="section-subtitle">Comprehensive features for a seamless examination experience</p>
                </div>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="feature-title">Secure & Reliable</h3>
                        <p class="feature-description">
                            Academic integrity is our priority. Cloud-based platform with 99.9% uptime and advanced security measures.
                        </p>
                        <ul class="feature-list">
                            <li><i class="fas fa-check"></i>Encrypted data transmission</li>
                            <li><i class="fas fa-check"></i>GDPR Compliant</li>
                            <li><i class="fas fa-check"></i>Regular security audits</li>
                        </ul>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <h3 class="feature-title">Easy to Use</h3>
                        <p class="feature-description">
                            User-friendly interface designed for students, examiners, and administrators with high adoption rates.
                        </p>
                        <ul class="feature-list">
                            <li><i class="fas fa-check"></i>Intuitive dashboard</li>
                            <li><i class="fas fa-check"></i>Quick registration</li>
                            <li><i class="fas fa-check"></i>Instant results</li>
                        </ul>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3 class="feature-title">24/7 Support</h3>
                        <p class="feature-description">
                            World-class support team ready to assist you. Get help when you need it most.
                        </p>
                        <ul class="feature-list">
                            <li><i class="fas fa-check"></i>Live chat support</li>
                            <li><i class="fas fa-check"></i>Comprehensive documentation</li>
                            <li><i class="fas fa-check"></i>Quick response time</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Links Section -->
        <section class="quick-links-section">
            <div class="container">
                <div class="quick-links-grid">
                    <a href="pages/aboutUs.php" class="quick-link-card">
                        <i class="fas fa-info-circle"></i>
                        <h3>About Us</h3>
                        <p>Learn more about ExamPro and our mission</p>
                    </a>
                    <a href="pages/contactUs.php" class="quick-link-card">
                        <i class="fas fa-envelope"></i>
                        <h3>Contact Us</h3>
                        <p>Get in touch with our support team</p>
                    </a>
                    <a href="pages/terms.php" class="quick-link-card">
                        <i class="fas fa-file-contract"></i>
                        <h3>Terms & Conditions</h3>
                        <p>Read our terms of service</p>
                    </a>
                    <a href="pages/privacy.php" class="quick-link-card">
                        <i class="fas fa-shield-alt"></i>
                        <h3>Privacy Policy</h3>
                        <p>Understand how we protect your data</p>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <?php include("includes/footer.php"); ?>

    <script src="scripts/index.js"></script>
</body>

</html>
