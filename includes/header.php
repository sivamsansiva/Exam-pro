<?php
// Determine profile link and user display name based on user role
$profileLink = "auth/login.php"; // Default link
$userName = "Guest";
$userRole = "";
$userRoleDisplay = "";

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    // Extract first part of email for display
    $userName = explode('@', $email)[0];

    if (isset($_SESSION['role'])) {
        $userRole = $_SESSION['role']; // lowercase for comparisons
        $userRoleDisplay = ucfirst($_SESSION['role']); // capitalized for display
        $profileLink = "../users/profile.php";
    }
}

// Get current page for active nav highlighting
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<style>
    /* ============================================
   HEADER STYLES
   ============================================ */
    .main-header {
        background: var(--color-white);
        border-bottom: 1px solid var(--color-border);
        box-shadow: var(--shadow-sm);
        position: sticky;
        top: 0;
        z-index: var(--z-sticky);
        transition: box-shadow var(--transition-base);
    }

    .main-header:hover {
        box-shadow: var(--shadow-md);
    }

    .header-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 var(--spacing-lg);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: var(--spacing-xl);
        height: 72px;
    }

    /* Logo Styles */
    .header-logo {
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
        font-size: var(--font-size-xl);
        font-weight: var(--font-weight-bold);
        color: var(--color-primary-600);
        text-decoration: none;
        transition: all var(--transition-base);
        padding: var(--spacing-sm) var(--spacing-md);
        border-radius: var(--radius-lg);
    }

    .header-logo:hover {
        background: var(--color-primary-50);
        color: var(--color-primary-700);
        transform: translateY(-2px);
    }

    .header-logo i {
        font-size: 1.75rem;
        margin-right: 0;
    }

    /* Navigation Styles */
    .header-nav {
        flex: 1;
        display: flex;
        justify-content: center;
    }

    .nav-menu {
        display: flex;
        align-items: center;
        gap: var(--spacing-xs);
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .nav-item {
        position: relative;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: var(--spacing-xs);
        padding: var(--spacing-sm) var(--spacing-md);
        color: var(--text-secondary);
        text-decoration: none;
        font-weight: var(--font-weight-medium);
        font-size: var(--font-size-sm);
        border-radius: var(--radius-md);
        transition: all var(--transition-base);
        white-space: nowrap;
    }

    .nav-link i {
        font-size: 1rem;
        margin-right: 0;
    }

    .nav-link:hover {
        background: var(--color-primary-50);
        color: var(--color-primary-600);
    }

    .nav-link.active {
        background: var(--color-primary-100);
        color: var(--color-primary-700);
        font-weight: var(--font-weight-semibold);
    }

    /* Dropdown Menu */
    .dropdown-menu {
        position: absolute;
        top: calc(100% + var(--spacing-sm));
        left: 0;
        min-width: 220px;
        background: var(--color-white);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        list-style: none;
        padding: var(--spacing-sm);
        margin: 0;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all var(--transition-base);
        z-index: var(--z-dropdown);
    }

    .nav-item:hover .dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-link {
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
        padding: var(--spacing-sm) var(--spacing-md);
        color: var(--text-secondary);
        text-decoration: none;
        font-size: var(--font-size-sm);
        border-radius: var(--radius-md);
        transition: all var(--transition-fast);
    }

    .dropdown-link i {
        width: 20px;
        color: var(--color-primary-500);
        margin-right: 0;
    }

    .dropdown-link:hover {
        background: var(--color-primary-50);
        color: var(--color-primary-700);
        transform: translateX(4px);
    }

    /* User Profile Section */
    .header-user {
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
    }

    .user-profile {
        position: relative;
    }

    .user-button {
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
        padding: var(--spacing-xs) var(--spacing-md);
        background: var(--color-secondary-50);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-full);
        cursor: pointer;
        transition: all var(--transition-base);
    }

    .user-button:hover {
        background: var(--color-primary-50);
        border-color: var(--color-primary-200);
        box-shadow: var(--shadow-sm);
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-full);
        background: linear-gradient(135deg, var(--color-primary-500), var(--color-accent-500));
        color: var(--text-inverse);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: var(--font-weight-bold);
        font-size: var(--font-size-lg);
        box-shadow: var(--shadow-sm);
    }

    .user-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .user-name {
        font-weight: var(--font-weight-semibold);
        font-size: var(--font-size-sm);
        color: var(--text-primary);
    }

    .user-role {
        font-size: var(--font-size-xs);
        color: var(--text-tertiary);
    }

    .user-button i.fa-chevron-down {
        font-size: 0.75rem;
        color: var(--text-tertiary);
        margin-left: var(--spacing-xs);
        transition: transform var(--transition-base);
    }

    .user-profile:hover .user-button i.fa-chevron-down {
        transform: rotate(180deg);
    }

    /* User Dropdown */
    .user-dropdown {
        position: absolute;
        top: calc(100% + var(--spacing-sm));
        right: 0;
        min-width: 240px;
        background: var(--color-white);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-xl);
        padding: var(--spacing-sm);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all var(--transition-base);
        z-index: var(--z-dropdown);
    }

    .user-profile:hover .user-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .user-dropdown-link {
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
        padding: var(--spacing-sm) var(--spacing-md);
        color: var(--text-secondary);
        text-decoration: none;
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-medium);
        border-radius: var(--radius-md);
        transition: all var(--transition-fast);
    }

    .user-dropdown-link i {
        width: 20px;
        color: var(--color-primary-500);
        margin-right: 0;
    }

    .user-dropdown-link:hover {
        background: var(--color-primary-50);
        color: var(--color-primary-700);
        transform: translateX(4px);
    }

    .user-dropdown-divider {
        height: 1px;
        background: var(--color-divider);
        margin: var(--spacing-xs) 0;
    }

    /* Mobile Menu Toggle */
    .mobile-menu-toggle {
        display: none;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--text-secondary);
        cursor: pointer;
        padding: var(--spacing-sm);
        border-radius: var(--radius-md);
        transition: all var(--transition-base);
    }

    .mobile-menu-toggle:hover {
        background: var(--color-primary-50);
        color: var(--color-primary-600);
    }

    /* ============================================
   RESPONSIVE DESIGN
   ============================================ */
    @media (max-width: 1024px) {
        .header-container {
            padding: 0 var(--spacing-md);
        }

        .nav-menu {
            gap: 0;
        }

        .nav-link {
            font-size: 0.8125rem;
            padding: var(--spacing-xs) var(--spacing-sm);
        }
    }

    @media (max-width: 768px) {
        .header-nav {
            display: none;
        }

        .mobile-menu-toggle {
            display: block;
        }

        .user-info {
            display: none;
        }

        .user-button {
            padding: var(--spacing-xs);
        }

        .user-button i.fa-chevron-down {
            display: none;
        }
    }

    @media (max-width: 640px) {
        .header-container {
            height: 64px;
            gap: var(--spacing-md);
        }

        .header-logo {
            font-size: var(--font-size-lg);
        }

        .header-logo span {
            display: none;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            font-size: var(--font-size-base);
        }
    }
</style>

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
                        <?php if ($userRoleDisplay): ?>
                            <span class="user-role"><?php echo htmlspecialchars($userRoleDisplay); ?></span>
                        <?php endif; ?>
                    </div>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="user-dropdown">
                    <?php if ($userRole === 'staff'): ?>
                        <a href="/Exam-pro/dashboards/user_dashboard.php" class="user-dropdown-link">
                            <i class="fas fa-tachometer-alt"></i>My Dashboard
                        </a>
                    <?php elseif ($userRole === 'admin'): ?>
                        <a href="/Exam-pro/dashboards/admin_dashboard.php" class="user-dropdown-link">
                            <i class="fas fa-tachometer-alt"></i>Admin Dashboard
                        </a>
                    <?php elseif ($userRole === 'manager'): ?>
                        <a href="/Exam-pro/dashboards/manager_dashboard.php" class="user-dropdown-link">
                            <i class="fas fa-tachometer-alt"></i>Manager Dashboard
                        </a>
                    <?php elseif ($userRole === 'examiner'): ?>
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
                    <a href="/Exam-pro/auth/logout.php" class="user-dropdown-link">
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
