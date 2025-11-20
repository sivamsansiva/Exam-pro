<?php
// Determine profile link and user display name based on user role
$profileLink = "auth/login.php"; // Default link
$userName = "Guest";
$userRole = "";

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    // Extract first part of email for display
    $userName = explode('@', $email)[0];

    if (isset($_SESSION['role'])) {
        $userRole = $_SESSION['role'];
        $profileLink = "../users/profile.php";
    }
}

// Get current page for active nav highlighting
$currentPage = basename($_SERVER['PHP_SELF']);
?>

    <header class="main-header">
        <div class="header-container">
            <!-- Logo -->
            <a href="/Exam-pro/index.php" class="header-logo">
                <i class="fas fa-graduation-cap"></i>
                <span>ExamPro</span>
            </a>

            <!-- Navigation -->
            <nav class="header-nav" id="mainNav">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="/Exam-pro/index.php" class="nav-link <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">
                            <i class="fas fa-home"></i>
                            <span>Home</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <span>Exams</span>
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="/Exam-pro/exams/registerExam.php" class="dropdown-link">
                                <i class="fas fa-user-plus"></i>Register Exam
                            </a></li>
                            <li><a href="/Exam-pro/exams/attemptExam.php" class="dropdown-link">
                                <i class="fas fa-pen-to-square"></i>Attempt Exam
                            </a></li>
                            <li><a href="/Exam-pro/exams/result.php" class="dropdown-link">
                                <i class="fas fa-chart-line"></i>View Results
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <span>Support</span>
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="/Exam-pro/pages/contactUs.php" class="dropdown-link">
                                <i class="fas fa-envelope"></i>Contact Us
                            </a></li>
                            <li><a href="/Exam-pro/pages/complain.php" class="dropdown-link">
                                <i class="fas fa-exclamation-circle"></i>Submit Complaint
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="/Exam-pro/pages/aboutUs.php" class="nav-link <?php echo ($currentPage == 'aboutUs.php') ? 'active' : ''; ?>">
                            <i class="fas fa-info-circle"></i>
                            <span>About Us</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- User Profile -->
            <div class="header-user">
                <div class="user-profile">
                    <div class="user-button">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($userName, 0, 1)); ?>
                        </div>
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                            <?php if ($userRole): ?>
                                <span class="user-role"><?php echo htmlspecialchars($userRole); ?></span>
                            <?php endif; ?>
                        </div>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="user-dropdown">
                        <?php if ($userRole === 'Staff'): ?>
                            <a href="/Exam-pro/dashboards/user_dashboard.php" class="user-dropdown-link">
                                <i class="fas fa-tachometer-alt"></i>My Dashboard
                            </a>
                        <?php elseif ($userRole === 'Admin'): ?>
                            <a href="/Exam-pro/dashboards/admin_dashboard.php" class="user-dropdown-link">
                                <i class="fas fa-tachometer-alt"></i>Admin Dashboard
                            </a>
                        <?php elseif ($userRole === 'Manager'): ?>
                            <a href="/Exam-pro/dashboards/manager_dashboard.php" class="user-dropdown-link">
                                <i class="fas fa-tachometer-alt"></i>Manager Dashboard
                            </a>
                        <?php elseif ($userRole === 'Examiner'): ?>
                            <a href="/Exam-pro/dashboards/examiner_dashboard.php" class="user-dropdown-link">
                                <i class="fas fa-tachometer-alt"></i>Examiner Dashboard
                            </a>
                        <?php endif; ?>
                        <a href="/Exam-pro/users/profile.php" class="user-dropdown-link">
                            <i class="fas fa-user"></i>My Profile
                        </a>
                        <a href="/Exam-pro/exams/result.php" class="user-dropdown-link">
                            <i class="fas fa-chart-bar"></i>My Results
                        </a>
                        <div class="user-dropdown-divider"></div>
                        <a href="/Exam-pro/auth/logout.php" class="user-dropdown-link" style="color: var(--error);">
                            <i class="fas fa-sign-out-alt"></i>Logout
                        </a>
                    </div>
                </div>

                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" aria-label="Toggle menu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

