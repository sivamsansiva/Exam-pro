<?php
// session_start();
// include('../config/config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - ExamPro</title>
    <link rel="stylesheet" href="../styles/core.css">
    <link rel="stylesheet" href="../styles/components.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .page-header {
            background: linear-gradient(135deg, var(--color-secondary-700) 0%, var(--color-secondary-900) 100%);
            color: white;
            padding: var(--spacing-3xl) var(--spacing-xl);
            text-align: center;
            border-radius: var(--radius-xl);
            margin-bottom: var(--spacing-xl);
        }

        .page-header h1 {
            margin: 0;
            font-size: var(--font-size-4xl);
            font-weight: var(--font-weight-bold);
        }

        .content-section {
            margin-bottom: var(--spacing-xl);
        }

        .content-section ul {
            padding-left: var(--spacing-xl);
        }

        .content-section li {
            margin: var(--spacing-sm) 0;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <?php
    include("../includes/header.php");
    ?>

    <!-- Privacy & Policy Page Content -->
    <div class="container mt-4 mb-4">
        <div class="text-center mb-4">
            <h1 class="mb-2">Privacy & Policy</h1>
        </div>

        <div class="card">
            <div class="card-body">
                <p class="mb-4">
                    This Privacy Policy explains how ExamPro collects, uses, and protects the personal information of employees using our Online Examination System.
                </p>

                <div class="mb-4">
                    <h3 class="card-title">Information We Collect</h3>
                    <p>We collect personal data when you register for and use our Online Examination System. This includes:</p>
                    <ul>
                        <li><strong>Personal Identification Information:</strong> Name, Employee ID, Department, Job Title, Email Address.</li>
                        <li><strong>Exam Information:</strong> Exam scores, answers, performance analytics, and history.</li>
                        <li><strong>Usage Data:</strong> Device information, IP address, browser type, and interaction logs with the system.</li>
                    </ul>
                </div>

                <div class="mb-4">
                    <h3 class="card-title">How We Use Your Information</h3>
                    <p>We use the information collected for the following purposes:</p>
                    <ul>
                        <li>To facilitate and manage the online examination process.</li>
                        <li>To monitor and evaluate employee performance.</li>
                        <li>To provide feedback and generate reports.</li>
                        <li>To improve our system's functionality and user experience.</li>
                    </ul>
                </div>

                <div class="mb-4">
                    <h3 class="card-title">Data Sharing and Disclosure</h3>
                    <p>We will not share your personal information with third parties except:</p>
                    <ul>
                        <li>With authorized personnel within our company for HR and training purposes.</li>
                        <li>When required by law or to comply with legal obligations.</li>
                    </ul>
                </div>

                <div class="mb-4">
                    <h3 class="card-title">Data Security</h3>
                    <p>We take the security of your data seriously and implement the following measures:</p>
                    <ul>
                        <li>Encryption of sensitive information.</li>
                        <li>Secure access protocols (password protection, two-factor authentication).</li>
                        <li>Regular system audits and updates to ensure the safety of stored data.</li>
                    </ul>
                    <p class="mt-2 text-secondary"><small>However, no method of data transmission over the internet is 100% secure, and we cannot guarantee absolute security.</small></p>
                </div>

                <div class="mb-4">
                    <h3 class="card-title">Employee Rights</h3>
                    <p>As an employee, you have the following rights concerning your data:</p>
                    <ul>
                        <li><strong>Access:</strong> You can request a copy of your personal data at any time.</li>
                        <li><strong>Correction:</strong> You can ask us to correct or update your information.</li>
                        <li><strong>Deletion:</strong> You may request the deletion of your data, subject to HR and company policies.</li>
                        <li><strong>Restriction:</strong> You can request the restriction of processing your data under certain conditions.</li>
                    </ul>
                </div>

                <div class="mb-4">
                    <h3 class="card-title">Cookies</h3>
                    <p>Our system uses cookies to improve user experience by remembering your login details and system preferences. You can manage or block cookies through your browser settings, but doing so may impact the functionality of the examination system.</p>
                </div>

                <div class="mb-4">
                    <h3 class="card-title">Data Retention</h3>
                    <p>We retain your personal data for as long as necessary to fulfill the purposes outlined in this Privacy Policy, including compliance with legal obligations or company policies.</p>
                </div>

                <div class="mb-4">
                    <h3 class="card-title">Changes to This Privacy Policy</h3>
                    <p>We reserve the right to update this Privacy Policy from time to time. Any changes will be posted on this page, and you will be notified via email or system notification. Continued use of the Online Examination System after changes are made constitutes acceptance of the new policy.</p>
                </div>

                <div class="alert alert-info">
                    <h4 class="alert-heading mb-2">Contact Us</h4>
                    <p>If you have any questions or concerns about this Privacy Policy or how we handle your personal information, please contact us at:</p>
                    <p class="mb-0"><strong>Email:</strong> xampro@gmail.com</p>
                    <p class="mb-0"><strong>Address:</strong> Jaffna, Sri Lanka.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php
    include("../includes/footer.php");
    ?>

</body>

</html>
