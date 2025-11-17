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
        switch ($userRole) {
            case 'admin':
                $profileLink = "users/admin.php";
                break;
            case 'examiner':
                $profileLink = "users/examiner.php";
                break;
            case 'manager':
                $profileLink = "users/manager.php";
                break;
            case 'employee':
            default:
                $profileLink = "users/candidate.php";
                break;
        }
    }
}

// Get current page for active nav highlighting
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamPro - Online Examination System</title>
    <link rel="stylesheet" href="styles/theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Modern Header Styles */
        .main-header {
            background-color: var(--primary);
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 var(--spacing-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }

        .header-logo {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            text-decoration: none;
        }

        .header-logo i {
            font-size: 1.75rem;
            color: var(--white);
        }

        .header-logo span {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--white);
            letter-spacing: -0.5px;
        }

        .header-nav {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            flex: 1;
            justify-content: center;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: var(--spacing-xs);
            margin: 0;
            padding: 0;
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.625rem 1rem;
            color: var(--white);
            text-decoration: none;
            border-radius: var(--radius-md);
            font-weight: 500;
            font-size: 0.9375rem;
            transition: all var(--transition-fast);
            white-space: nowrap;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .nav-link i {
            font-size: 0.875rem;
        }

        /* Dropdown Styles */
        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background-color: var(--white);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            padding: var(--spacing-sm);
            min-width: 200px;
            display: none;
            margin-top: 0.5rem;
            list-style: none;
        }

        .nav-item:hover .dropdown-menu {
            display: block;
        }

        .dropdown-link {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            padding: 0.625rem 1rem;
            color: var(--text-primary);
            text-decoration: none;
            border-radius: var(--radius-sm);
            transition: all var(--transition-fast);
            font-size: 0.9375rem;
        }

        .dropdown-link:hover {
            background-color: var(--primary-light);
            color: var(--primary);
        }

        .dropdown-link i {
            font-size: 0.875rem;
            width: 1.25rem;
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
            padding: 0.5rem 1rem;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-lg);
            color: var(--white);
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .user-button:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--white);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.9375rem;
            line-height: 1.2;
        }

        .user-role {
            font-size: 0.75rem;
            opacity: 0.9;
            text-transform: capitalize;
        }

        .user-dropdown {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            background-color: var(--white);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            padding: var(--spacing-sm);
            min-width: 220px;
            display: none;
        }

        .user-profile:hover .user-dropdown {
            display: block;
        }

        .user-dropdown-link {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            padding: 0.75rem 1rem;
            color: var(--text-primary);
            text-decoration: none;
            border-radius: var(--radius-sm);
            transition: all var(--transition-fast);
            font-size: 0.9375rem;
        }

        .user-dropdown-link:hover {
            background-color: var(--primary-light);
            color: var(--primary);
        }

        .user-dropdown-link i {
            width: 1.25rem;
            font-size: 0.875rem;
        }

        .user-dropdown-divider {
            height: 1px;
            background-color: var(--border-light);
            margin: var(--spacing-sm) 0;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--white);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
        }

        /* Mobile Responsive */
        @media (max-width: 968px) {
            .header-nav {
                position: fixed;
                top: 70px;
                left: 0;
                right: 0;
                background-color: var(--white);
                box-shadow: var(--shadow-lg);
                padding: var(--spacing-lg);
                display: none;
                flex-direction: column;
                align-items: stretch;
            }

            .header-nav.active {
                display: flex;
            }

            .nav-menu {
                flex-direction: column;
                width: 100%;
            }

            .nav-link {
                color: var(--text-primary);
                justify-content: space-between;
            }

            .nav-link:hover,
            .nav-link.active {
                background-color: var(--primary-light);
                color: var(--primary);
            }

            .dropdown-menu {
                position: static;
                box-shadow: none;
                background-color: var(--bg-primary);
                margin-top: 0.5rem;
                margin-left: var(--spacing-lg);
            }

            .mobile-menu-toggle {
                display: block;
            }

            .user-info {
                display: none;
            }
        }

        @media (max-width: 640px) {
            .header-container {
                padding: 0 var(--spacing-md);
            }

            .header-logo span {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <!-- Logo -->
            <a href="index.php" class="header-logo">
                <i class="fas fa-graduation-cap"></i>
                <span>ExamPro</span>
            </a>

            <!-- Navigation -->
            <nav class="header-nav" id="mainNav">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">
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
                            <li><a href="exams/registerExam.php" class="dropdown-link">
                                <i class="fas fa-user-plus"></i>Register Exam
                            </a></li>
                            <li><a href="exams/attemptExam.php" class="dropdown-link">
                                <i class="fas fa-pen-to-square"></i>Attempt Exam
                            </a></li>
                            <li><a href="exams/result.php" class="dropdown-link">
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
                            <li><a href="contactUs.php" class="dropdown-link">
                                <i class="fas fa-envelope"></i>Contact Us
                            </a></li>
                            <li><a href="complain.php" class="dropdown-link">
                                <i class="fas fa-exclamation-circle"></i>Submit Complaint
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="aboutUs.php" class="nav-link <?php echo ($currentPage == 'aboutUs.php') ? 'active' : ''; ?>">
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
                        <a href="<?php echo $profileLink; ?>" class="user-dropdown-link">
                            <i class="fas fa-user"></i>My Profile
                        </a>
                        <a href="exams/result.php" class="user-dropdown-link">
                            <i class="fas fa-chart-bar"></i>My Results
                        </a>
                        <div class="user-dropdown-divider"></div>
                        <a href="auth/logout.php" class="user-dropdown-link" style="color: var(--error);">
                            <i class="fas fa-sign-out-alt"></i>Logout
                        </a>
                    </div>
                </div>

                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" onclick="toggleMobileMenu()" aria-label="Toggle menu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <script>
        function toggleMobileMenu() {
            const nav = document.getElementById('mainNav');
            nav.classList.toggle('active');
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const nav = document.getElementById('mainNav');
            const toggle = document.querySelector('.mobile-menu-toggle');

            if (!nav.contains(event.target) && !toggle.contains(event.target)) {
                nav.classList.remove('active');
            }
        });
    </script>
