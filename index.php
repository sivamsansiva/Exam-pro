<?php
// session_start();
include('php/config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email'])){
    header("Location: auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles/index.css">
</head>
<body>
    <?php
        include ("includes/header.php");
    ?>
    <div class="topCont">
        <div class="nav-bar">
            <div class="search">
                <input type="search" id="searchBar" placeholder="Search Exams...">
                <button type="button"><i class="fas fa-search"></i></button>
            </div>
            <div class="mode">
                <!-- <input type="button" id="log" onClick="logOut()" value="Log Out"> -->
                <a href="logout.php"><button type="button">logOut<i class="fa fa-sign-out-alt"></i></button></a>
            </div>
        </div>
        <div class="intro">
            <div class="topIntro">
                <h1>The reliable exam <br> platform.</h1><br>
                <p>Secure. Easy to use. Dedicated to your success.</p><br>
                <!-- <input type="button" value="Register Exam" onClick="openPage()" id="register"> -->
                <a href="registerExam.php"><button type="button">Register Here</button></a>
            </div>
            <div class="botIntro">
                <a href="Exams.html">
                    <img src="img/exampro.webp" alt="exampro">
                </a>
            </div>
        </div>
    </div>
    <div class="botCont">
        <div class="aboutCont">
            <h1>Contact when you need Us</h1>
            <div class="container">
                <div class="card about-us">
                    <h2>About Us</h2>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                    <a href="about.php">Visit About Us <i class="fas fa-external-link-alt"></i></a>
                </div>
                <div class="card contact-us">
                    <h2>Contact Us</h2>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                    <a href="contact.php">Visit Contact Us <i class="fas fa-external-link-alt"></i></a>
                </div>
                <div class="card term">
                    <h2>Terms & Conditions</h2>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                    <a href="terms.php">View Terms & Conditions <i class="fas fa-external-link-alt"></i></a>
                </div>
                <div class="card policy">
                    <h2>Privacy & Policies</h2>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                    <a href="privacy.php">View Privacy & Policies <i class="fas fa-external-link-alt"></i></a>
                </div>
            </div>
        </div>
        <div class="featCont">
            <h1>Features of the ExamPro</h1>
            <div class="features">
                <div class="feature secure">
                    <h2>ExamPro</h2>
                    <h2>Secure</h2>
                    <p>Academic integrity can never be compromised.</p>
                    <ul>
                        <li><i class="fas fa-check"></i>Lockdown application</li>
                        <li><i class="fas fa-check"></i>Closed sourced code (highest security)</li>
                        <li><i class="fas fa-check"></i>Third-party security audits</li>
                        <li><i class="fas fa-check"></i>Third-party penetration tests</li>
                        <li><i class="fas fa-check"></i>Online proctoring</li>
                        <li><i class="fas fa-check"></i>EU and US servers</li>
                        <li><i class="fas fa-check"></i>GDPR Compliant</li>
                    </ul>
                </div>
                <div class="feature reliable">
                    <h2>ExamPro</h2>
                    <h2>Reliable</h2>
                    <p>Our customers can't afford lost exams or lost answers.</p>
                    <ul>
                        <li><i class="fas fa-check"></i>Cloud-based (99.9% uptime)</li>
                        <li><i class="fas fa-check"></i>Offline exam compatibility</li>
                        <li><i class="fas fa-check"></i>Autosave every 10 seconds</li>
                        <li><i class="fas fa-check"></i>Automatic updates</li>
                        <li><i class="fas fa-check"></i>World-class support</li>
                        <li><i class="fas fa-check"></i>Proprietary technology</li>
                        <li><i class="fas fa-check"></i>Zero maintenance</li>
                    </ul>
                </div>
                <div class="feature easy">
                    <h2>ExamPro</h2>
                    <h2>Easy to use</h2>
                    <p>Easy-implemented online exam system that supports the whole examination lifecycle.</p>
                    <ul>
                        <li><i class="fas fa-check"></i>LMS integration through LTI</li>
                        <li><i class="fas fa-check"></i>User friendly interface</li>
                        <li><i class="fas fa-check"></i>High adoption</li>
                        <li><i class="fas fa-check"></i>Modern accessibility tools</li>
                        <li><i class="fas fa-check"></i>Anonymous grading</li>
                        <li><i class="fas fa-check"></i>External tools</li>
                        <li><i class="fas fa-check"></i>QTI import</li>
                        <li><i class="fas fa-check"></i>Collaborative grading</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php
        include ("php/footer.php");
    ?>
    <script src="mainScript.js"></script>
</body>
</html>
