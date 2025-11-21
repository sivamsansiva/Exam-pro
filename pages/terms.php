<?php
// include('../config/config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Conditions - ExamPro</title>
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
    </style>
</head>

<body>

    <!-- Header -->
    <?php
    include("../includes/header.php");
    ?>

    <!-- Terms & Conditions Page Content -->
    <div class="container mt-4 mb-4">
        <div class="text-center mb-4">
            <h1 class="mb-2">Terms & Conditions</h1>
        </div>

        <div class="card">
            <div class="card-body">
                <p class="mb-4">
                    By accessing and participating in this online examination, you agree to comply with the following terms and conditions. These rules are in place to ensure the integrity, fairness, and smooth operation of the examination process. Please read carefully before proceeding.
                </p>

                <div class="mb-4">
                    <h4 class="card-title">Eligibility</h4>
                    <p>Only authorized employees are permitted to participate in this online examination. Any attempt to access the examination platform by individuals who are not registered or approved by the company is strictly prohibited. The organization reserves the right to verify the identity of any participant at any point during or after the examination. Unauthorized participation may lead to disciplinary action.</p>
                </div>

                <div class="mb-4">
                    <h4 class="card-title">Confidentiality</h4>
                    <p>All examination materials, including but not limited to the exam questions, answers, instructions, and any related content, are strictly confidential. You are not allowed to share, copy, reproduce, distribute, or disseminate any part of the exam materials, whether during or after the examination. Violation of confidentiality may lead to legal consequences, disqualification, or further action by the company, depending on the severity of the breach.</p>
                </div>

                <div class="mb-4">
                    <h4 class="card-title">Independent Participation</h4>
                    <p>The online examination is designed to assess your individual knowledge and skills. Therefore, you must complete the exam independently without assistance from others or the use of unauthorized materials such as textbooks, notes, electronic devices, or other external resources unless explicitly stated otherwise. Any form of cheating, including but not limited to plagiarism, collaboration with other candidates, or the use of unauthorized tools, is strictly forbidden. The company may employ various monitoring tools or methods, such as video proctoring or system tracking, to ensure compliance.</p>
                </div>

                <div class="mb-4">
                    <h4 class="card-title">Use of Technology</h4>
                    <p>As this examination is conducted online, you are responsible for ensuring that your device (computer, tablet, or smartphone) and internet connection are functional and stable throughout the exam duration. This includes maintaining adequate battery power, securing a stable internet connection, and ensuring the device's software and hardware are compatible with the examination platform. The company will not be held responsible for any technical difficulties on your end. However, if you encounter technical issues that prevent you from completing the exam, you must report them immediately to the support team. It is advised to perform a system check before starting the exam to avoid disruptions.</p>
                </div>

                <div class="mb-4">
                    <h4 class="card-title">Time Limits and Exam Completion</h4>
                    <p>Each exam will have a clearly defined time limit, which will be communicated to you before you begin. You are required to complete all sections of the exam within the allotted time. Once the time has expired, the exam will be automatically submitted, and no further changes will be allowed. Exceeding the time limit may result in disqualification or a penalty to your final score. Make sure you manage your time wisely during the exam. In cases where an extension is warranted due to technical difficulties, it will be granted only at the sole discretion of the administrator.</p>
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
