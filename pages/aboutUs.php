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
    <link rel="stylesheet" href="../styles/style.css">
    <style>
        body {
    background: linear-gradient(90deg,#C63C51 0%,#8C3061 50%, #4F1787 100%);
}

.AboutLinks {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100px;
    gap: 25px;
}
.AboutLinks>li a:hover {
    display: flex;
    list-style: none;
    color: rgb(10, 10, 10);
    text-decoration:line-through;
}

.AboutLinks a {
    color: rgb(9, 10, 9);
    text-decoration:none;
    font-size: 1rem;
}

img{
    border: solid;
    width: 100%;
}

.image{
    text-align: center;
}

h1{
    margin-bottom: 30px;
    font-size: 32px;
    text-align: center;
    color: #fdfdfd;
    padding-bottom: 10px;
    text-decoration: underline;
}

h3{
    padding: 15px 15px 0px 15px;
}

h5{
    margin: 15px;
    padding: 15px;
}

.box{
    border-color: black;
    border-width: 2px;
    border-style:double;
    display: inline-block;
    width: 350px;
    height:250px;
}

.para{
    padding: 20px;
}

.box:hover{
    background-color: rgb(222, 233, 239);

}

.subcontant{
    text-align: center;
}

.secure p:first-child {
    font-weight: bold;
}

.secure{
    font-size: 1.2rem;
    padding: 25px;
}

.content{
    padding: 40px;
    background-color: rgb(124, 206, 244);
}

.Maincontent{
    width: 60%;
    margin: 0 auto;
    padding: 40px;
}

@media screen and (max-width: 768px) {
    .Maincontent{
        width: 90%;
    }

    .content{
        width:90%;
    }

    h1 {
        font-size: 2rem;
        width: 90%;
    }

    h3 {
        font-size: 1.5rem;
    }

    p {
        font-size: 1rem;
    }
}
    </style>
</head>
<body>

    <!-- Header -->
    <?php
        include ("../includes/header.php");
    ?>
    <!-- About Us Page Content -->
    <div class="Maincontent">
        <div class="About">
        <ul class="AboutLinks">
            <li><a href="aboutUs.php">About us</a></li>
            <li><a href="privacy.php">Privacy & Policy</a></li>
            <li><a href="terms.php">Terms & Conditions</a></li>
        </ul>

        <h1>About Us</h1>
        <div class="content">

            <div class="image">
            <img src="../assets/images/about.png" alt="image" width="750px">
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
        include ("../includes/footer.php");
    ?>
</body>
</html>
