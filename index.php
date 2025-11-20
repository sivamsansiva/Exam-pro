<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('config/config.php');

if (!isset($_SESSION['email'])){
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

// Fetch department exams (exams from user's department)
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

// Fetch general exams (all exams)
$generalExamQuery = "SELECT e.id, e.code, e.name, e.description, e.duration_minutes, e.scheduled_at,
                            u.first_name, u.last_name, u.email as examiner_email, d.name as dept_name
                     FROM exam e
                     JOIN users u ON e.created_by = u.id
                     JOIN department d ON e.department_id = d.id
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
    <link rel="stylesheet" href="styles/theme.css">
    <link rel="stylesheet" href="styles/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Staff'): ?>
                                <a href="dashboards/user_dashboard.php" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-tachometer-alt"></i>
                                    My Dashboard
                                </a>
                            <?php elseif (isset($_SESSION['role'])): ?>
                                <a href="dashboards/<?php echo strtolower($_SESSION['role']); ?>_dashboard.php" class="btn btn-secondary btn-lg">
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
                        <div class="stat-icon" style="background-color: var(--primary-light); color: var(--primary);">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number"><?php echo count($departmentExams); ?></h3>
                            <p class="stat-label">Department Exams</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: #D1FAE5; color: var(--secondary);">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number"><?php echo count($generalExams); ?></h3>
                            <p class="stat-label">Available Exams</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: #FEF3C7; color: var(--warning);">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number"><?php echo count($registeredExams); ?></h3>
                            <p class="stat-label">Registered Exams</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: #FEE2E2; color: var(--error);">
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
                                    <i class="fas fa-hashtag"></i>
                                    <span>Code: <?php echo htmlspecialchars($exam['code']); ?></span>
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
                        All Available Exams
                    </h2>
                    <p class="section-subtitle">Browse all exams across departments</p>
                </div>
                <div class="exams-grid">
                    <?php if (count($generalExams) > 0): ?>
                        <?php foreach ($generalExams as $exam): ?>
                        <div class="exam-card">
                            <div class="exam-card-header">
                                <h3 class="exam-title"><?php echo htmlspecialchars($exam['name']); ?></h3>
                                <span class="badge badge-success"><?php echo htmlspecialchars($exam['dept_name']); ?></span>
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
                                        <i class="fas fa-hashtag"></i>
                                        <span>Code: <?php echo htmlspecialchars($exam['code']); ?></span>
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
                        <div class="feature-icon" style="background-color: var(--primary-light); color: var(--primary);">
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
                        <div class="feature-icon" style="background-color: #D1FAE5; color: var(--secondary);">
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
                        <div class="feature-icon" style="background-color: #FEF3C7; color: var(--warning);">
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
