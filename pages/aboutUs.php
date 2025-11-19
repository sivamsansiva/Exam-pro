<!-- wait until about page finish -->
<?php
    require ('../config/config.php');
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
      }

      if (!isset($_SESSION['email'])){
        header("Location: ../auth/login.php");
        exit();
      }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link rel="stylesheet" href="../styles/theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Header -->
    <?php
        include ("../includes/header.php");
    ?>

    <!-- About Us Page Content -->
    <div class="container mt-4 mb-4">
        <div class="text-center mb-4">
            <h1 class="mb-2">About Us</h1>
            <div class="d-flex justify-content-center gap-2">
                <a href="aboutUs.php" class="btn btn-primary btn-sm">About Us</a>
                <a href="privacy.php" class="btn btn-outline btn-sm">Privacy & Policy</a>
                <a href="terms.php" class="btn btn-outline btn-sm">Terms & Conditions</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="text-center mb-4">
                    <img src="../assets/images/about.png" alt="About Us" style="max-width: 100%; height: auto; border-radius: var(--radius-lg);">
                </div>

                <div class="mb-4">
                    <h3 class="text-center mb-3">Welcome to ExamPro</h3>
                    <p class="text-center" style="max-width: 800px; margin: 0 auto;">
                        The leading provider of innovative online examination solutions tailored for employee assessments.
                        Our mission is to revolutionize the way organizations evaluate and enhance their workforce's skills and knowledge.
                    </p>
                    <p class="text-center mt-2" style="max-width: 800px; margin: 0 auto;">
                        At ExamPro, we understand the importance of efficient, secure, and scalable examination processes.
                        Our state-of-the-art online examination system is designed to meet the diverse needs of businesses across various industries.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="card" style="background-color: var(--bg-primary); border: none;">
                        <div class="card-body text-center">
                            <h4 class="card-title">Our Vision</h4>
                            <p class="card-text">
                                We envision a future where employee assessments are not just a formality but a strategic tool for growth and development.
                                By leveraging cutting-edge technology, we aim to provide organizations with the insights they need.
                            </p>
                        </div>
                    </div>
                    <div class="card" style="background-color: var(--bg-primary); border: none;">
                        <div class="card-body text-center">
                            <h4 class="card-title">Our Mission</h4>
                            <p class="card-text">
                                Our mission is to empower organizations with reliable and efficient online examination solutions that enhance productivity,
                                ensure compliance, and drive employee success.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <h4 class="alert-heading mb-2">Why Choose Us?</h4>
                    <ul style="list-style-position: inside; margin-left: 1rem;">
                        <li><strong>Secure and Reliable:</strong> Highest level of security and integrity.</li>
                        <li><strong>User-Friendly Interface:</strong> Easy to navigate for everyone.</li>
                        <li><strong>Customizable Solutions:</strong> Tailored to your specific needs.</li>
                        <li><strong>Real-Time Analytics:</strong> Comprehensive reporting tools.</li>
                        <li><strong>24/7 Support:</strong> Dedicated support team always available.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php
        include ("../includes/footer.php");
    ?>
</body>
</html>
