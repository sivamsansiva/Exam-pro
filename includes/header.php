<?php
// session_start(); // Start the session to access session variables

// Determine profile link based on user role
$profileLink = "profile.php"; // Default link (in case no role matches)
if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'Admin':
            $profileLink = "admin.php"; // Link for admin profile
            break;
        case 'Examiner':
            $profileLink = "examiner.php"; // Link for examiner profile
            break;
        case 'Manager':
            $profileLink = "manager.php"; // Link for manager profile
            break;
        default:
            $profileLink = "candidate.php"; // Default user profile
    }
} else {
    $profileLink = "login.php"; // If not logged in, redirect to login
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header Design</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        /* dark mode */
        .dark-mode {
            background-color: black;
            color: white;
        } 
        /* Navbar Styles */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: hsl(220, 24%, 12%);
            padding: 10px 20px;
            height: 75px;
        }

        .navbar .logo a {
            color: #fff;
            text-decoration: none;
            font-size: 24px;
        }

        .navbar .link {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        .navbar .link li {
            position: relative;
        }

        .navbar .link li a {
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            display: block;
        }

        .navbar .link li:hover .dropdown {
            display: block;
        }

        .navbar .dropdown {
            display: none;
            position: absolute;
            background-color: #000000;
            top: 100%;
            left: 0;
            min-width: 200px;
            z-index: 1;
            list-style: none;
            padding: 0;
            margin: 0;
            border: #f1f1f1;
            border-radius: 10px;
        }

        .navbar .dropdown li {
            width: 100%;
        }

        .navbar .dropdown li a {
            padding: 10px;
            color: #fff;
            text-decoration: none;
            border-radius: 10px;
            display: block;
            text-align: center;
        }

        .navbar .dropdown li a:hover {
            background-color: #fff;
            color: orange;
        }

        .navbar .user {
            color: #fff;
            text-decoration: none;
            margin-right: 20px;
        }

        .navbar .btn {
            display: none;
        }

        @media (max-width: 800px) {
            .navbar .link {
                flex-direction: row;
                display: none;
                width: 100%;
            }
            .navbar .link li {
                width: 100%;
            }
            .navbar .link li a {
                padding: 5px;
                text-align: center;
                align-items: baseline;
            }
            .navbar .btn {
                display: block;
                color: #fff;
                font-size: 24px;
                cursor: pointer;
            }
            .navbar .link.active {
                display: flex;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="navbar">
            <div class="logo">
                <a href="index.php">XamPro</a>
            </div>
            <ul class="link">
                <li><a href="index.php">Home</a></li>
                <li class="dropdown-container"><a href="">Exam</a>
                    <ul class="dropdown">
                        <li class="Dlink"><a href="registerExam.php">Register Exam</a></li>
                        <li class="Dlink"><a href="attemptExam.php">Attempt Exam</a></li>
                        <li class="Dlink"><a href="result.php">View Result</a></li>
                    </ul>
                </li>
                <li class="dropdown-container"><a href="">Support</a>
                    <ul class="dropdown">
                        <li class="Dlink"><a href="contact.php">Contact</a></li>
                        <li class="Dlink"><a href="complain.php">Complain</a></li>
                    </ul>
                </li>
                <li><a href="about.php">About Us</a></li>
            </ul>
            <a href="<?php echo $profileLink; ?>" class="user"> Hello  <?php echo isset($_SESSION['email']) ? $_SESSION['email'] : 'Guest'; ?></a>
            <div class="btn" onclick="toggleMenu()">
                <i class="fa fa-bars"></i>
            </div>
        </div>
    </div>
    <script>
        function toggleMenu() {
        const links = document.querySelector('.navbar .link');
        links.classList.toggle('active');
    }
    </script>
</body>
</html>
