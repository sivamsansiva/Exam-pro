<style>
    /* ============================================
   FOOTER STYLES
   ============================================ */
    .main-footer {
        background: linear-gradient(135deg, var(--color-secondary-900) 0%, var(--color-secondary-800) 100%);
        color: var(--color-secondary-100);
        margin-top: var(--spacing-4xl);
        border-top: 4px solid var(--color-primary-500);
    }

    .footer-content {
        max-width: 1400px;
        margin: 0 auto;
        padding: var(--spacing-3xl) var(--spacing-lg) var(--spacing-xl);
    }

    .footer-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: var(--spacing-3xl);
        margin-bottom: var(--spacing-3xl);
    }

    .footer-section h3 {
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-bold);
        color: var(--color-white);
        margin-bottom: var(--spacing-lg);
        position: relative;
        padding-bottom: var(--spacing-sm);
    }

    .footer-section h3::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 40px;
        height: 3px;
        background: linear-gradient(90deg, var(--color-primary-500), var(--color-accent-500));
        border-radius: var(--radius-full);
    }

    .footer-about p {
        font-size: var(--font-size-sm);
        line-height: var(--line-height-relaxed);
        color: var(--color-secondary-300);
        margin-bottom: var(--spacing-lg);
    }

    /* Social Links */
    .social-links {
        display: flex;
        gap: var(--spacing-sm);
    }

    .social-links a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: var(--radius-lg);
        color: var(--color-white);
        text-decoration: none;
        font-size: var(--font-size-lg);
        transition: all var(--transition-base);
    }

    .social-links a i {
        margin-right: 0;
    }

    .social-links a:hover {
        background: var(--color-primary-500);
        border-color: var(--color-primary-400);
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3);
    }

    /* Footer Links */
    .footer-links,
    .footer-contact-info {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li,
    .footer-contact-info li {
        margin-bottom: var(--spacing-sm);
    }

    .footer-links a {
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
        color: var(--color-secondary-300);
        text-decoration: none;
        font-size: var(--font-size-sm);
        transition: all var(--transition-fast);
        padding: var(--spacing-xs) 0;
    }

    .footer-links a i {
        width: 20px;
        color: var(--color-primary-400);
        font-size: 0.875rem;
        margin-right: 0;
    }

    .footer-links a:hover {
        color: var(--color-white);
        transform: translateX(4px);
    }

    .footer-links a:hover i {
        color: var(--color-accent-400);
    }

    /* Contact Info */
    .footer-contact-info li {
        display: flex;
        align-items: flex-start;
        gap: var(--spacing-sm);
        font-size: var(--font-size-sm);
        color: var(--color-secondary-300);
        padding: var(--spacing-xs) 0;
    }

    .footer-contact-info li i {
        width: 20px;
        color: var(--color-primary-400);
        margin-top: 2px;
        flex-shrink: 0;
        margin-right: 0;
    }

    /* Footer Bottom */
    .footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: var(--spacing-xl);
    }

    .footer-bottom-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: var(--spacing-lg);
    }

    .footer-copyright {
        display: flex;
        align-items: center;
        gap: var(--spacing-xs);
        font-size: var(--font-size-sm);
        color: var(--color-secondary-400);
    }

    .footer-copyright i {
        margin-right: 0;
    }

    .footer-legal-links {
        display: flex;
        gap: var(--spacing-lg);
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-legal-links a {
        color: var(--color-secondary-400);
        text-decoration: none;
        font-size: var(--font-size-sm);
        transition: color var(--transition-fast);
    }

    .footer-legal-links a:hover {
        color: var(--color-primary-400);
    }

    /* ============================================
   RESPONSIVE DESIGN
   ============================================ */
    @media (max-width: 768px) {
        .footer-content {
            padding: var(--spacing-2xl) var(--spacing-md) var(--spacing-lg);
        }

        .footer-grid {
            grid-template-columns: 1fr;
            gap: var(--spacing-2xl);
            margin-bottom: var(--spacing-2xl);
        }

        .footer-bottom-content {
            flex-direction: column;
            text-align: center;
        }

        .footer-legal-links {
            flex-direction: column;
            gap: var(--spacing-sm);
        }
    }

    @media (max-width: 640px) {
        .footer-section h3 {
            font-size: var(--font-size-base);
        }

        .social-links a {
            width: 40px;
            height: 40px;
        }
    }
</style>

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
<script src="/Exam-pro/scripts/script.js"></script>
