<?php
// session_start();
include('php/config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy & Policy</title>
    <style>
        body {
            background: linear-gradient(90deg,#C63C51 0%,#8C3061 50%, #4F1787 100%);
}

        .container {
            width: 60%;
            margin: 0 auto;
            padding: 40px;
        }

        h1 {
            text-align: center;
            color: #000000;
            font-size: 32px;
            letter-spacing: 1px;
            padding-bottom: 10px;
            margin-bottom:30px;
            text-decoration: underline;
        }
        
        p {
            color: #333;
            font-size: 18px;
            margin-bottom: 20px;
            line-height: 1.8;
        }

        #submain {
            margin-bottom: 30px;
            font-size: 18px;
        }

        .main {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            border-radius: 8px;
        }

        .container div {
            margin-bottom: 10px;
            font-size: 18px;
            color:black;
        }

        .container p {
            margin-bottom: 10px;
        }

        .container h3 {
            color: #2c3e50;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .container p {
            font-size: 1rem;
            color: #555;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }

        .container p#submain {
            border-left: none;
            padding: 0;
            margin: 0;
            font-family: Arial, sans-serif;
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

        @media screen and (max-width: 768px) {
            .container {
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

    <!-- Privacy & Policy Page Content -->

    <div class="container">

    <div class="About">
        <ul class="AboutLinks">
          <li><a href="about.php">About us</a></li>
          <li><a href="privacy.php">Privacy & Policy</a></li>
          <li><a href="terms.php">Terms & Conditions</a></li>
        </ul>

        <h1> Privacy & Policy </h1>
            
            <div class="main">
                <p id="submain">
                    This Privacy Policy explains how Xam pro we collects, uses, and protects the personal information of employees using our Online Examination System. 
                </p>
                <p id="submain">

                    <h3> Information We Collect</h3> 

                    We collect personal data when you register for and use our Online Examination System. This includes: <br><br>

                    - Personal Identification Information: Name, Employee ID, Department, Job Title, Email Address. <br>
                    - Exam Information: Exam scores, answers, performance analytics, and history. <br>
                    - Usage Data: Device information, IP address, browser type, and interaction logs with the system. <br>
                </p>
                <p id="submain">

                    <h3> How We Use Your Information</h3>

                    We use the information collected for the following purposes: <br><br>

                    - To facilitate and manage the online examination process.<br>
                    - To monitor and evaluate employee performance.<br>
                    - To provide feedback and generate reports.<br>
                    - To improve our system's functionality and user experience.<br>
                </p>
                <p id="submain">

                    <h3> Data Sharing and Disclosure</h3>

                    We will not share your personal information with third parties except: <br><br>

                    - With authorized personnel within our company for HR and training purposes. <br>
                    - When required by law or to comply with legal. <br>
                </p>
                <p id="submain">

                    <h3> Data Security</h3>

                    We take the security of your data seriously and implement the following measures:<br><br>

                    - Encryption of sensitive information. <br>
                    - Secure access protocols (password protection, two-factor authentication).<br>
                    - Regular system audits and updates to ensure the safety of stored data.<br><br>

                    However, no method of data transmission over the internet is 100% secure, and we cannot guarantee absolute security.<br>
                </p>
                <p id="submain">

                    <h3> Employee Rights</h3>

                    As an employee, you have the following rights concerning your data:<br><br>

                    - Access: You can request a copy of your personal data at any time.<br>
                    - Correction: You can ask us to correct or update your information.<br>
                    - Deletion: You may request the deletion of your data, subject to HR and company policies.<br>
                    - Restriction: You can request the restriction of processing your data under certain conditions.<br>
                </p>
                <p id="submain">

                    <h3> Cookies</h3>

                    Our system uses cookies to improve user experience by remembering your login details and system preferences. You can manage or block cookies through your browser settings, but doing so may impact the functionality of the examination system.
                </p>
                <p id="submain">

                    <h3> Data Retention</h3>

                    We retain your personal data for as long as necessary to fulfill the purposes outlined in this Privacy Policy, including compliance with legal obligations or company policies.
                </p>
                <p id="submain">

                    <h3> Changes to This Privacy Policy</h3>

                    We reserve the right to update this Privacy Policy from time to time. Any changes will be posted on this page, and you will be notified via email or system notification. Continued use of the Online Examination System after changes are made constitutes acceptance of the new policy.
                </p>
                <p id="submain">

                    <h3> Contact Us</h3>

                    If you have any questions or concerns about this Privacy Policy or how we handle your personal information, please contact us at: <br><br>
                    
                    Email: xampro@gmail.com  <br>
                    Address: Jaffna, Sri Lanka.
                                
                </p>
            </div>


        </div>
    </div>

    <!-- Footer -->
    <?php
        include ("php/footer.php");
    ?>
    
</body>
</html>