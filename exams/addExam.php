<?php
   include("../config/config.php");

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['email']) || $_SESSION['role'] == 'Manager' || $_SESSION['role'] == 'Employee'){
        header("Location: login.php");
        exit();
    }

    // Database content

   if($_SERVER["REQUEST_METHOD"] == "POST"){
        $exam_id = $_POST["exam_id"];
        $ename = $_POST["ename"];
        $qpassword = $_POST["qpassword"];
        $Duration = $_POST["duration"];
        $sid = $_POST["sid"];


   global $conn;
   $message = "";

   $sql = "INSERT INTO exam (E_ID,E_Name,Q_password,Duration,S_ID)
           VALUES ('$exam_id','$ename','$qpassword','$Duration','$sid')";

    if($conn->query($sql) === TRUE){
        // echo "New Exam added Sucessfully";
        echo '<script>alert("New Exam added Sucessfully");</script>';

        if ($_SESSION['Role'] == 'Manager') {
            echo '<script>window.location.href = "manager.php";</script>';
        }
        elseif ($_SESSION['Role'] == 'Admin') {
            echo '<script>window.location.href = "admin.php";</script>';
        }
        elseif  ($_SESSION['Role'] == 'Examiner') {
            echo '<script>window.location.href = "examiner.php";</script>';
        }
    }
    else{
        echo "Error".$sql ."<br>" . $conn->error;
    }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Exam</title>
    <!-- <link rel="stylesheet" href="../styles/examStyle.css"> -->
    <link rel="stylesheet" href="../styles/addExamAd.css">
    <style>
        /* General Styling */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: linear-gradient(90deg, #ffffff 0%, #EB8317 35%, #10375C 100%);
}

.admin-container {
  display: flex;
  height: 100vh;
}

/* Sidebar Styling */
/* .sidebar {
  width: 250px;
  height:100%;
  background-color: #34495e;
  color: #ecf0f1;
  padding: 20px;
  display: flex;
  flex-direction: column;
}

.sidebar h2 {
  text-align: center;
  margin-bottom: 30px;
}

.sidebar ul {
  list-style-type: none;
}

.sidebar ul li {
  margin-bottom: 20px;
}

.sidebar ul li a {
  text-decoration: none;
  color: #ecf0f1;
  font-size: 18px;
  display: block;
  padding: 10px;
  border-radius: 5px;
  transition: background-color 0.3s;
} */

.sidebar ul li a:hover {
  background-color: #2c3e50;
}

/* Main Content Styling */
.main-content {
  flex: 1;
  padding: 30px;

}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-left: 20px;
  margin-right: 20px;
  margin-top: 10px;
  margin-bottom: 10px;
  width:100%;

}

.section-header h2 {
  color: #34495e;

}

.profile-section,
#exam-list,
#feedback-list,
#complaints-list,
#staff-list,
#canditate-list
{
  background-color: #fff;
  padding-top: 5px;
  padding-bottom: 20px;
  border-radius: 20px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  margin:50px auto;

}

.profile-section:hover,
#exam-list:hover,
#feedback-list:hover,
#complaints-list:hover,
#staff-list:hover,
#canditate-list:hover
{
   transition: 0.3s;
  transform: scale(1.05);
  box-shadow: 0 10px 15px rgba(0,0,0,.2);
}



.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}
#admin{
display: flex;
flex-wrap:wrap;
width:80%;
padding: 20 20 20 20;

}

input[type="text"],
input[type="email"],
input[type="password"],
input[type="Date"],
input[type="tel"]

{
flex:80%;
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
  margin: 20px auto;
}

.form-actions {
  display: flex;
  justify-content: flex-start;
  gap: 10px;
  margin:20 px auto;
}

button.btn {
  padding: 10px 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

button.edit-btn {
  background-color: #3498db;
  color: #fff;
}

button.save-btn {
  background-color: #2ecc71;
  color: #fff;
}

button.add-btn {
  background-color: #9b59b6;
  color: #fff;
}

button.delete-btn {
  background-color: #e74c3c;
  color: #fff;
}



button{
  color:white;
}

button:hover {
  opacity: 0.8;
}

ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
}

ul li {
  margin: 10px 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

ul li button {
  margin-left: 15px;
}



.table-style{
  margin: 20px auto;
  border-collapse: collapse;
  font-family: Agenda-Light, sans-serif;
  font-weight: 100;
  background: #333;
  color: #fff;
  text-rendering: optimizeLegibility;
  border-radius: 5px;
  width:90%;


}


.table-style thead tr th {
  font-weight: 600;
}

/* Styles for table header and table data */
.table-style thead th, .table-style tbody td {
  padding: 1rem;
  font-size: 16px;
  vertical-align: middle;
  text-align: center;
}


.table-style tbody td {
  padding: .8rem;
  font-size: 16px;
  color: #444;
  background: #eee;
  transition: 0.3s;

}

/* Styles for alternating rows in the table body - w3school */
.table-style tbody tr:not(:last-child) {
  border-top: 1px solid #ddd;
  border-bottom: 1px solid #ddd;
}

/* responsive table - w3school*/
@media screen and (max-width: 800px) {
  .table-style caption { background-image: none; }
  .table-style thead { display: none; }
  .table-style tbody td {
    display: block;
    padding: .6rem;
  }
  .table-style tbody tr td:first-child {
    background: #666;
    color: #fff;
  }
  .table-style tbody td:before {
    content: attr(data-th);
    font-weight: bold;
    display: inline-block;
    width: 6rem;
  }
}

section-header{
  border:2px solid grey;
  background-color: #f0f0f0;
}
/* Styles for the update button */
.update-btn,
.reply-btn,
#submitbtn{
  width: 80px;
  height:30px;
  background-color: #0078D7;
  font-size: 14px;
  font-weight: bold;
  border: 0;
  border-radius: 25px;
  cursor: pointer;
  transition: 0.3s;
  transform: scale(1.05);
}

/* Hover styles for the update button */
.update-btn:hover,
.reply-btn:hover,
#submitbtn:hover{
  font-size: 16px;
  background-color: #005a9e;
}

/* Styles for the delete button */
.delete-btn{
  width: 80px;
  height:30px;
  background-color: rgb(224, 0, 0);
  font-size: 14px;
  font-weight: bold;
  border: 0;
  border-radius: 25px;
  cursor: pointer;
  transition: 0.3s;
}

/* Hover styles for the delete button */
.delete-btn:hover{
  font-size: 16px;
  background-color: rgb(168, 1, 1);
}


.delete-btn a, .update-btn a{
  color: #fff;
  text-decoration: none;
}

#horizontal{
  display: flex;
  /* width: 100%; */
  padding-top: 20px;
  padding-bottom: 40px;

}

h3{
  margin-left: 50px;
  margin-bottom: 20px;


}

a{
  text-decoration: none;
  color:white;
}

#canditate-list{
  background-color: #fff;
  padding-top: 5px;
  padding-bottom: 20px;
  border-radius: 20px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  margin-top: 10px;
}
/* .form-grou{
  width: 100px;
} */
/* #password {
  width: 100px;
} */
.profile .profile-section  .form-group {
background-color:red;
width: 100%;
padding: 10px;
border: 1px solid #ccc;
border-radius: 5px;
padding-left:50px;
display :flex;
/* margin : 40px auto; */
}

#submitbtn{
width: 200px;
height:50px;
border-radius: 25px;
background-color: #0078D7;
color:white;
align-content:right;
border:none;
font-size: 3 rem;
}

#profile-section{
/*
display: flex;
justify-content: center; */
width:80vw ;
margin : 20px 100px 100px 20px;
padding-left: 50px;
padding-right:50px;
/* flex: 80%; */
}




.modal {
display: none;
position: fixed;
z-index: 1;
left: 0;
top: 0;
width: 100%;
height: 100%;
overflow: auto;
background-color: rgb(0, 0, 0);
background-color: rgba(0, 0, 0, 0.4);
}

.modal-content {
background-color: #fefefe;
margin: 15% auto;
padding: 20px;
border: 1px solid #888;
width: 80%;
max-width: 600px;
}

/* .close {
color: #aaa;

font-size: 28px;
font-weight: bold;
} */

.close:hover,
.close:focus {
color: black;
text-decoration: none;
cursor: pointer;
}

.close{
width:50px;
border-radius: 20px;
background-color: red;
color:#aaa;
float: right;
}
    </style>
</head>
<body>

    <!-- Header -->
    <?php
        include ("../includes/header.php");
    ?>

    <!-- Add Exam Content -->
    <div class="add-exam">
        <h1> Add Exam </h1>

        <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">

            <label for="ename">Exam ID:</label><br>
            <input type="text" id="eid" name="exam_id" required placeholder="Exam ID"><br>

            <label for="ename">Exam Name:</label><br>
            <input type="text" id="ename" name="ename" required placeholder="Exam Name"><br>

            <label for="qpassword">Quiz Password:</label><br>
            <input type="text" id="ename" name="qpassword" required
            placeholder="Quiz Password"><br>

            <label for="duration">Exam Duration:</label><br>
            <input type="text" name="duration" id="duration" pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}" placeholder="00:00:00" required><br>

            <label for="">Staff ID:</label><br>
            <?php

                // Select exams from database

                $sql= "SELECT S_ID from staff";
                $result = $conn->query($sql);
                if($result->num_rows > 0){

            ?>
                <select name="sid" id="sid" required>
                <option value="" disabled selected>Select Staff ID</option>

            <?php
                while ($row = $result->fetch_assoc()) {
                    $sid = $row['S_ID'];
                    echo "<option>$sid</option>";
                }
            ?>
            </select><br>

            <?php
                }
                else{
                echo "No Staff ID Found.";
                }
            ?>

            <input type="submit" name="submit" value="Add Exam" >
        </form>
    </div>

    <!-- Footer -->
    <?php
        include ("../includes/footer.php");
    ?>

</body>
</html>
<?php
   include("../config/config.php");

   if (session_status() == PHP_SESSION_NONE) {
    session_start();
    }

    if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Admin'){
        header("Location: login.php");
        exit();
    }
    // Database content

   if($_SERVER["REQUEST_METHOD"] == "POST"){
        $exam_id = $_POST["exam_id"];
        $ename = $_POST["ename"];
        $qpassword = $_POST["qpassword"];
        $Duration = $_POST["duration"];
        $sid = $_POST["sid"];
        global $conn;
        $message = "";

        $sql = "INSERT INTO exam (E_ID,E_Name,Q_password,Duration,S_ID)
                VALUES ('$exam_id','$ename','$qpassword','$Duration','$sid')";
        if($conn->query($sql) === TRUE){
            // echo "New Exam added Sucessfully";
            echo '<script>alert("New Exam added Sucessfully");</script>';
            if ($_SESSION['Role'] == 'Examiner') {
                echo '<script>window.location.href = "examiner.php";</script>';
            }
            elseif ($_SESSION['Role'] == 'Manager') {
                echo '<script>window.location.href = "manager.php";</script>';
            }
            elseif ($_SESSION['Role'] == 'Admin') {
                echo '<script>window.location.href = "admin.php";</script>';
            }
        }
        else{
            echo "Connection Error".$sql ."<br>".$conn->error;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/addExamAd.css">
    <title> Add Exam </title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

.add-exam {
    width: 500px;
    margin: 100px auto;
    background-color: #c9c6c6;
    box-shadow: 0 10px 20px rgba(12, 61, 223, 0.2);
    padding: 20px;
    border-radius: 5px;
    transition: 0.3s;

}

.add-exam h1 {
    font-size: 32px;
    text-align: center;
    margin-bottom: 20px;
    color: rgb(12, 12, 12);
}

.add-exam form {
    display: flex;
    flex-direction: column;
  }

  .add-exam label {
    font-size: 18px;
    margin-bottom: 10px;
    color: rgb(26, 24, 24);
  }

  .add-exam input[type="text"],
  .add-exam input[type="date"],
  .add-exam input[type="file"],
  .add-exam textarea,
  .add-exam select {
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    font-family: 'Poppins', sans-serif;
  }

  .add-exam input[type="submit"] {
    padding: 10px 20px;
    border: none;
    background-color: #333;
    color: #fff;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
  }

  .add-exam input[type="submit"]:hover {
    background-color: #666;
  }

  select {
    background-position: right center;
    background-size: 20px;
    padding-right: 30px;
  }
    </style>
</head>
<body>
    <!-- Add Exam content -->
    <div class="add-exam">
        <h2> Add Exam </h2>

        <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">

            <label for="ename">Exam ID:</label><br>
            <input type="text" id="eid" name="exam_id" required placeholder="Exam ID"><br>

            <label for="ename">Exam Name:</label><br>
            <input type="text" id="ename" name="ename" required placeholder="Exam Name"><br>

            <label for="qpassword">Quiz Password:</label><br>
            <input type="text" id="ename" name="qpassword" required
            placeholder="Quiz Password"><br>

            <label for="duration">Exam Duration:</label><br>
            <input type="text" name="duration" id="duration" pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}" placeholder="00:00:00" required><br>

            <label for="">Staff ID:</label><br>
            <?php

                // Select exams from database

                $sql= "SELECT S_ID from staff";
                $result = $conn->query($sql);
                if($result->num_rows > 0){

            ?>
                <select name="sid" id="sid" required>
                <option value="" disabled selected>Select Staff ID</option>

            <?php
                while ($row = $result->fetch_assoc()) {
                    $sid = $row['S_ID'];
                    echo "<option>$sid</option>";
                }
            ?>
            </select><br>

            <?php
                }
                else{
                echo "No Staff ID Found.";
                }

            ?>

            <input type="submit" name="submit" value="Add Exam" >
        </form>
    </div>

</body>
</html>
