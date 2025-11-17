<!-- wait until about page finish -->
<?php
    require ('php/config.php');
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
      }
      
      if (!isset($_SESSION['email'])){
        header("Location: login.php");
        exit();
      }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link rel="stylesheet" href="style/about.css">

</head>
<body>

    <!-- Header -->
    <?php
        include ("php/header.php");
    ?>
    <!-- About Us Page Content -->
    <div class="Maincontent">
        <div class="About">
        <ul class="AboutLinks">
            <li><a href="about.php">About us</a></li>
            <li><a href="privacy.php">Privacy & Policy</a></li>
            <li><a href="terms.php">Terms & Conditions</a></li>
        </ul>

        <h1>About Us</h1>
        <div class="content">

            <div class="image">
            <img src="img/about.png" alt="image" width="750px">
            </div> <br>
            
            <div class="aboutcontant">
                <h3>
                    Welcome to Xampro, the leading provider of innovative online examination solutions tailored for employee
                    assessments. Our mission is to revolutionize the way organizations evaluate and enhance their workforce's
                    skills and knowledge.
                </h3>
                <h3>
                    At Xampro, we understand the importance of efficient, secure, and scalable examination processes. Our
                    state-of-the-art online examination system is designed to meet the diverse needs of businesses across various
                    industries. Whether you are looking to conduct aptitude tests, technical assessments, or compliance exams, our
                    platform offers a seamless and user-friendly experience.
                </h3>
            </div> <br>

            <hr>
            <br>
            <div class="subcontant">
            <div class="box">
                <p class="para">Our Vision
                We envision a future where employee assessments are not just a formality but a strategic tool for growth and
                development. By leveraging cutting-edge technology, we aim to provide organizations with the insights they
                need to make informed decisions and foster a culture of continuous improvement.</p>
            </div>
            <div class="box">
                <p class="para">Our Mission
                Our mission is to empower organizations with reliable and efficient online examination solutions that
                enhance productivity, ensure compliance, and drive employee success. We are committed to delivering
                exceptional service and support to help our clients achieve their assessment goals.</p>
            </div>
            </div>
            <br>
            <hr>
            <br>
            <div class="secure">
            <p >Secure and Reliable:</p>

            <p> Our platform ensures the highest level of security and integrity for all examinations,
                protecting both the organization and the employees.
                User-Friendly Interface: Designed with the end-user in mind, our system is easy to navigate, ensuring a smooth
                experience for both administrators and examinees.
                Customizable Solutions: We offer tailored solutions to meet the specific needs of your organization, from
                question types to reporting formats.
                Real-Time Analytics: Gain valuable insights with our comprehensive reporting and analytics tools, helping you
                track performance and identify areas for improvement.
                24/7 Support: Our dedicated support team is always available to assist you with any queries or issues,
                ensuring a hassle-free experience.</p>
            </div>
        </div>
        </div>
    </div>

    <!-- Footer -->
    <?php
        include ("php/footer.php");
    ?>
</body>
</html>