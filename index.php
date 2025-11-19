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
$userRole = $_SESSION['role'] ?? 'employee';
$userDepartment = $_SESSION['department'] ?? null;

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
    $deptExamQuery = "SELECT e.*, s.F_Name, s.L_Name, s.Email as examiner_email, d.D_Name
                      FROM exam e
                      JOIN staff s ON e.S_ID = s.S_ID
                      JOIN department d ON s.D_ID = d.D_ID
                      WHERE s.D_ID = ?
                      ORDER BY e.E_ID DESC";
    $stmt = $conn->prepare($deptExamQuery);
    $stmt->bind_param("s", $userDepartment);
    $stmt->execute();
    $departmentExams = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Fetch general exams (all exams or exams from other departments - simplified version)
$generalExamQuery = "SELECT e.*, s.F_Name, s.L_Name, s.Email as examiner_email, d.D_Name
                     FROM exam e
                     JOIN staff s ON e.S_ID = s.S_ID
                     JOIN department d ON s.D_ID = d.D_ID
                     ORDER BY e.E_ID DESC
                     LIMIT 6";
$generalExams = $conn->query($generalExamQuery)->fetch_all(MYSQLI_ASSOC);

// Note: Since the database schema doesn't have a registration table,
// we'll simulate registered and attended exams based on the attends table
// Fetch registered exams (for now, we'll show all available exams as "available to register")
$registeredExams = [];

// Fetch attended exams (exams the user has completed)
$attendedExams = [];
// First, check if user exists in exam_candidate table
$candidateCheck = "SELECT C_ID FROM exam_candidate WHERE Email = ?";
$stmt = $conn->prepare($candidateCheck);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$candidateResult = $stmt->get_result();

if ($candidateResult->num_rows > 0) {
    $candidate = $candidateResult->fetch_assoc();
    $candidateId = $candidate['C_ID'];

    $attendedQuery = "SELECT e.*, s.F_Name, s.L_Name, d.D_Name, a.Result
                      FROM attends a
                      JOIN exam e ON a.E_ID = e.E_ID
                      JOIN staff s ON e.S_ID = s.S_ID
                      JOIN department d ON s.D_ID = d.D_ID
                      WHERE a.C_ID = ?
                      ORDER BY a.Result DESC";
    $stmt = $conn->prepare($attendedQuery);
    $stmt->bind_param("s", $candidateId);
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
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Employee'): ?>
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
                            <h3 class="exam-title"><?php echo htmlspecialchars($exam['E_Name']); ?></h3>
                            <span class="badge badge-info"><?php echo htmlspecialchars($exam['D_Name']); ?></span>
                        </div>
                        <div class="exam-card-body">
                            <div class="exam-meta">
                                <div class="exam-meta-item">
                                    <i class="fas fa-user-tie"></i>
                                    <span><?php echo htmlspecialchars($exam['F_Name'] . ' ' . $exam['L_Name']); ?></span>
                                </div>
                                <div class="exam-meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span><?php echo htmlspecialchars($exam['Duration']); ?></span>
                                </div>
                                <div class="exam-meta-item">
                                    <i class="fas fa-hashtag"></i>
                                    <span>ID: <?php echo htmlspecialchars($exam['E_ID']); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="exam-card-footer">
                            <a href="exams/registerExam.php?id=<?php echo $exam['E_ID']; ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-user-plus"></i>
                                Register
                            </a>
                            <a href="exams/registerExam.php?id=<?php echo $exam['E_ID']; ?>" class="btn btn-sm btn-outline">
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
                                <h3 class="exam-title"><?php echo htmlspecialchars($exam['E_Name']); ?></h3>
                                <span class="badge badge-success"><?php echo htmlspecialchars($exam['D_Name']); ?></span>
                            </div>
                            <div class="exam-card-body">
                                <div class="exam-meta">
                                    <div class="exam-meta-item">
                                        <i class="fas fa-user-tie"></i>
                                        <span><?php echo htmlspecialchars($exam['F_Name'] . ' ' . $exam['L_Name']); ?></span>
                                    </div>
                                    <div class="exam-meta-item">
                                        <i class="fas fa-clock"></i>
                                        <span><?php echo htmlspecialchars($exam['Duration']); ?></span>
                                    </div>
                                    <div class="exam-meta-item">
                                        <i class="fas fa-hashtag"></i>
                                        <span>ID: <?php echo htmlspecialchars($exam['E_ID']); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="exam-card-footer">
                                <a href="exams/registerExam.php?id=<?php echo $exam['E_ID']; ?>" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-user-plus"></i>
                                    Register
                                </a>
                                <a href="exams/registerExam.php?id=<?php echo $exam['E_ID']; ?>" class="btn btn-sm btn-outline">
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
                            <h3 class="exam-title"><?php echo htmlspecialchars($exam['E_Name']); ?></h3>
                            <span class="badge badge-success">
                                <?php echo number_format($exam['Result'], 2); ?>%
                            </span>
                        </div>
                        <div class="exam-card-body">
                            <div class="exam-meta">
                                <div class="exam-meta-item">
                                    <i class="fas fa-building"></i>
                                    <span><?php echo htmlspecialchars($exam['D_Name']); ?></span>
                                </div>
                                <div class="exam-meta-item">
                                    <i class="fas fa-user-tie"></i>
                                    <span><?php echo htmlspecialchars($exam['F_Name'] . ' ' . $exam['L_Name']); ?></span>
                                </div>
                                <div class="exam-meta-item">
                                    <i class="fas fa-trophy"></i>
                                    <span><?php echo $exam['Result'] >= 75 ? 'Pass' : 'Review Required'; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="exam-card-footer">
                            <a href="exams/result.php?id=<?php echo $exam['E_ID']; ?>" class="btn btn-sm btn-primary">
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
