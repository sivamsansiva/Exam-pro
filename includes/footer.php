<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Footer Design</title>
    <link rel="stylesheet" href="styles/theme.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Modern Footer Styles */
        .main-footer {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #0d3a75 100%);
            color: var(--white);
            margin-top: auto;
        }

        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: var(--spacing-2xl) var(--spacing-lg);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-xl);
            margin-bottom: var(--spacing-xl);
        }

        .footer-section h3 {
            color: var(--white);
            font-size: var(--font-size-lg);
            font-weight: 600;
            margin-bottom: var(--spacing-lg);
            position: relative;
            padding-bottom: var(--spacing-sm);
        }

        .footer-section h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background-color: var(--secondary);
            border-radius: 2px;
        }

        .footer-about p {
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.7;
            margin-bottom: var(--spacing-md);
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: var(--spacing-sm);
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            transition: all var(--transition-fast);
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-sm);
        }

        .footer-links a:hover {
            color: var(--white);
            padding-left: var(--spacing-sm);
        }

        .footer-links a i {
            font-size: 0.875rem;
            width: 1.25rem;
        }

        .footer-contact-info {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-contact-info li {
            display: flex;
            align-items: flex-start;
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-md);
            color: rgba(255, 255, 255, 0.85);
        }

        .footer-contact-info i {
            font-size: 1.125rem;
            color: var(--secondary);
            width: 1.25rem;
            margin-top: 0.125rem;
        }

        .social-links {
            display: flex;
            gap: var(--spacing-md);
            margin-top: var(--spacing-lg);
        }

        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            color: var(--white);
            text-decoration: none;
            transition: all var(--transition-base);
            font-size: 1.125rem;
        }

        .social-links a:hover {
            background-color: var(--secondary);
            border-color: var(--secondary);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(52, 168, 83, 0.3);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: var(--spacing-lg);
            margin-top: var(--spacing-lg);
            text-align: center;
        }

        .footer-bottom-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: var(--spacing-md);
            color: rgba(255, 255, 255, 0.75);
            font-size: var(--font-size-sm);
        }

        .footer-copyright {
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        .footer-legal-links {
            display: flex;
            gap: var(--spacing-lg);
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-legal-links a {
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            transition: color var(--transition-fast);
        }

        .footer-legal-links a:hover {
            color: var(--white);
        }

        @media (max-width: 768px) {
            .footer-content {
                padding: var(--spacing-xl) var(--spacing-md);
            }

            .footer-grid {
                grid-template-columns: 1fr;
                gap: var(--spacing-2xl);
            }

            .footer-bottom-content {
                flex-direction: column;
                text-align: center;
            }

            .footer-legal-links {
                flex-direction: column;
                gap: var(--spacing-sm);
            }

            .social-links {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-grid">
                <!-- About Section -->
                <div class="footer-section footer-about">
                    <h3>ExamPro</h3>
                    <p>
                        The reliable and secure online examination platform dedicated to your success.
                        We provide comprehensive exam management solutions for educational institutions and organizations.
                    </p>
                    <div class="social-links">
                        <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links Section -->
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="/Exam-pro/index.php">
                            <i class="fas fa-home"></i>Home
                        </a></li>
                        <li><a href="/Exam-pro/exams/registerExam.php">
                            <i class="fas fa-user-plus"></i>Register for Exam
                        </a></li>
                        <li><a href="/Exam-pro/exams/attemptExam.php">
                            <i class="fas fa-pen-to-square"></i>Attempt Exam
                        </a></li>
                        <li><a href="/Exam-pro/exams/result.php">
                            <i class="fas fa-chart-line"></i>View Results
                        </a></li>
                        <li><a href="/Exam-pro/pages/aboutUs.php">
                            <i class="fas fa-info-circle"></i>About Us
                        </a></li>
                    </ul>
                </div>

                <!-- Support Section -->
                <div class="footer-section">
                    <h3>Support</h3>
                    <ul class="footer-links">
                        <li><a href="/Exam-pro/pages/contactUs.php">
                            <i class="fas fa-envelope"></i>Contact Us
                        </a></li>
                        <li><a href="/Exam-pro/pages/complain.php">
                            <i class="fas fa-exclamation-circle"></i>Submit Complaint
                        </a></li>
                        <li><a href="/Exam-pro/pages/privacy.php">
                            <i class="fas fa-shield-alt"></i>Privacy Policy
                        </a></li>
                        <li><a href="/Exam-pro/pages/terms.php">
                            <i class="fas fa-file-contract"></i>Terms & Conditions
                        </a></li>
                    </ul>
                </div>

                <!-- Contact Information Section -->
                <div class="footer-section">
                    <h3>Contact Info</h3>
                    <ul class="footer-contact-info">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>123 Education Street, Academic City, Country</span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <span>+1 (555) 123-4567</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span>support@exampro.com</span>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <span>Mon - Fri: 9:00 AM - 6:00 PM</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <div class="footer-copyright">
                        <i class="far fa-copyright"></i>
                        <span><?php echo date('Y'); ?> ExamPro. All rights reserved.</span>
                    </div>
                    <ul class="footer-legal-links">
                        <li><a href="/Exam-pro/pages/privacy.php">Privacy Policy</a></li>
                        <li><a href="/Exam-pro/pages/terms.php">Terms of Service</a></li>
                        <li><a href="/Exam-pro/pages/contactUs.php">Contact</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
