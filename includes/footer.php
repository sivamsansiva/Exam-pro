<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Footer Design</title>
	<link rel="stylesheet" href="style/home.css">
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}
		.mainFooter {
			width:100%;
			background-color: hsl(220, 24%, 12%);
			
		}
		.mainFooter .mainFootCont {
			display: flex;
			flex-wrap: wrap;
		}
		.mainFooter .mainFootEle {
			width: 25%;
			padding: 15px;
		}
		.mainFooter .mainFootEle h3 {
			font-size: 18px;
			color: #ffffff;
			text-transform: capitalize;
			margin-bottom: 35px;
			font-weight: 500;
			position: relative;
		}
		.mainFooter .mainFootEle h3::before{
			content: '';
			position: absolute;
			left:0;
			bottom: -10px;
			background-color: #e91e63;
			height: 2px;
			box-sizing: border-box;
			width: 50px;
		}
		.mainFooter .mainFootEle ul {
			list-style: none;
		}
		.mainFooter .mainFootEle li:not(:last-child){
			margin-bottom: 10px;
		}
		.mainFooter .mainFootEle ul li a{
			font-size: 16px;
			text-transform: capitalize;
			color: #ffffff;
			text-decoration: none;
			font-weight: 300;
			color: #bbbbbb;
			display: block;
			transition: all 0.3s ease;
		}
		.mainFooter .mainFootEle ul li a:hover{
			color: #ffffff;
			padding-left: 8px;
		}
		.mainFooter .mainFootEle .social-links a{
			display: inline-block;
			height: 40px;
			width: 40px;
			background-color: rgba(255,255,255,0.2);
			margin:0 10px 10px 0;
			text-align: center;
			line-height: 40px;
			border-radius: 50%;
			color: #ffffff;
			transition: all 0.5s ease;
		}
		.mainFooter .mainFootEle .social-links a:hover{
			color: #24262b;
			background-color: #ffffff;
		}
		@media(max-width: 800px){
			.mainFooter .mainFootEle {
				width: 50%;
				margin-bottom: 30px;
			}
		}
		@media(max-width: 500px){
			.mainFooter .mainFootEle {
				width: 100%;
			}
		}
    </style>
</head>
<body>
	<div class="mainFooter">
        <div class="mainFootCont">
            <div class="mainFootEle">
                <h3>Xam_Pro</h3>
                <div class="logo">

                </div>
            </div>
            <div class="mainFootEle">
                <h3>follow us</h3>
                <div class="social-links">
                    <a href="https://x.com/i/flow/login?input_flow_data=%7B%22requested_variant%22%3A%22eyJteCI6IjIifQ%3D%3D%22%7D"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.linkedin.com/#:~:text=500%20million+%20members%20|%20Manage%20your%20professional%20identity."><i class="fab fa-linkedin-in"></i></a>
                    <a href="https://web.facebook.com/?_rdc=1&_rdr#:~:text=Log%20into%20Facebook%20to%20start%20sharing%20and%20connecting%20with%20your"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="mainFootEle">
                <h3>Company</h3>
                <ul>
                    <li><a href="www.netexam.lk">Exams</a></li>
                    <li><a href="result.php">Results</a></li>
                    <li><a href="contact.html">Contact</a></li>
                </ul>
            </div>
            <div class="mainFootEle">
                <h3>Services</h3>
                <ul>
                    <li><a href="about.php">about us</a></li>
                    <li><a href="privacy.php">Privacy & Policy</a></li>
                    <li><a href="terms.php">Terms & Conditions</a></li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
