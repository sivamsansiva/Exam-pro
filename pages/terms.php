<?php
// include('php/config.php');

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
    <title>Terms & Conditions</title>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"> 

    <style>
        /* terms.css */
        
        body {
            background: linear-gradient(90deg,#C63C51 0%,#8C3061 50%, #4F1787 100%);
}

        .Main-Content{
            width: 60%;
            margin: 0 auto;
            padding: 40px;
            text-align: center;
        }

        .AboutLinks{
            margin-bottom: 20px;
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

        h1{
            margin-bottom: 30px;
            font-size: 32px;
            text-align: center;
            color: #fff;
            padding-bottom: 10px;
            text-decoration: underline;
           
        }

        .content-box{
            background-color: rgb(195, 203, 206);
            border: 2px solid black;
            padding: 20px;

        }
        .para{
            font-size: 18px;
            line-height: 1.6;
            color: #444;
            text-align: left;
            margin-bottom: 40px;
        }

        h4{
            margin-bottom: 30px;
            font-size: 26px;
            text-align: left;
            color: #333;
            padding-bottom: 10px;
        }

        @media screen and (max-width: 768px) {
            .Main-Content {
                width: 90%;
            }

            h1 {
                font-size: 2rem;
                width: 90%;
            }

            h3 {
                font-size: 1.5rem;
            }

            p {
                font-size: 0.9rem;
            }
        }
    </style>

</head>

<body>

    <!-- Header -->
    <?php
        include ("php/header.php");
    ?>

    <!-- Terms & Conditions Page Content -->

    <section class="Main-Content">
        <div class="About">
            <ul class="AboutLinks">
              <li><a href="about.php">About us</a></li>
              <li><a href="privacy.php">Privacy & Policy</a></li>
              <li><a href="terms.php">Terms & Conditions</a></li>
            </ul>

        <h1> Terms & Conditions </h1>

        <div class="content-box">
        <p class="para"> By accessing and participating in this online examination, you agree to comply with the following terms and conditions. These rules are in place to ensure the integrity, fairness, and smooth operation of the examination process. Please read carefully before proceeding. </p>

        <h4> Eligibility </h4>
        <p class="para"> Only authorized employees are permitted to participate in this online examination. Any attempt to access the examination platform by individuals who are not registered or approved by the company is strictly prohibited. The organization reserves the right to verify the identity of any participant at any point during or after the examination. Unauthorized participation may lead to disciplinary action.</p>

        <h4> Confidentiality </h4>
        <p class="para"> All examination materials, including but not limited to the exam questions, answers, instructions, and any related content, are strictly confidential. You are not allowed to share, copy, reproduce, distribute, or disseminate any part of the exam materials, whether during or after the examination. Violation of confidentiality may lead to legal consequences, disqualification, or further action by the company, depending on the severity of the breach. </p>

        <h4>  Independent Participation </h4>
        <p class="para"> The online examination is designed to assess your individual knowledge and skills. Therefore, you must complete the exam independently without assistance from others or the use of unauthorized materials such as textbooks, notes, electronic devices, or other external resources unless explicitly stated otherwise. Any form of cheating, including but not limited to plagiarism, collaboration with other candidates, or the use of unauthorized tools, is strictly forbidden. The company may employ various monitoring tools or methods, such as video proctoring or system tracking, to ensure compliance. </p>

        <h4> Use of Technology </h4>
        <p class="para"> As this examination is conducted online, you are responsible for ensuring that your device (computer, tablet, or smartphone) and internet connection are functional and stable throughout the exam duration. This includes maintaining adequate battery power, securing a stable internet connection, and ensuring the device's software and hardware are compatible with the examination platform. The company will not be held responsible for any technical difficulties on your end. However, if you encounter technical issues that prevent you from completing the exam, you must report them immediately to the support team. It is advised to perform a system check before starting the exam to avoid disruptions. </p>

        <h4> Time Limits and Exam Completion </h4>
        <p class="para">  Each exam will have a clearly defined time limit, which will be communicated to you before you begin. You are required to complete all sections of the exam within the allotted time. Once the time has expired, the exam will be automatically submitted, and no further changes will be allowed. Exceeding the time limit may result in disqualification or a penalty to your final score. Make sure you manage your time wisely during the exam. In cases where an extension is warranted due to technical difficulties, it will be granted only at the sole</p>

    </div>
    </section>

    <!-- Footer -->
    <?php
        include ("php/footer.php");
    ?>
</body>
</html>