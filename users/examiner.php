<?php
    include('php/config.php');
  
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Examiner'){
        header("Location: login.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examiner Profile Page</title>
    <link rel="stylesheet" href="style/admin.css">
    <link rel="stylesheet" href="style/add_exam_AD.css">
</head>
<body>
    <?php
        include ("php/header.php");
    ?>
    <div class="admin-container">
        <main class="main-content">
            <!--================ Examiner Profile Section ===============-->
            <section id="profile">
                <div class="section-header">
                    <h2>Examiner Profile</h2>
                </div>
                <div class="profile-section" id="profile-section">
                    
                 <?php
                 $session['Role']="Examiner";
                    $sql1 = "SELECT * FROM staff WHERE Role='Examiner'";
                        $result = mysqli_query($conn, $sql1);
                        $row = $result->fetch_assoc();
                        $Sid = $row['S_ID'];
                        $Fname = $row['F_Name'];
                        $Lname = $row['L_Name'];
                        $DOB = $row['DOB'];
                        $Email = $row['Email'];
                        $gender = $row['Gender'];
                        $Age=$row['Age'];

                        // Fetch candidate phone number
                        $sql1 = "SELECT * FROM staff_phone_no WHERE S_ID='$Sid'";
                        $result = mysqli_query($conn, $sql1);
                        $row = $result->fetch_assoc();

                        $phoneNo = $row['S_phone_no'];

                        // Check if form is submitted
                        if (isset($_POST['submit'])) {
                            $Fname = isset($_POST["fname"]) ? $_POST["fname"] : "";  // Check if 'fname' exists in $_POST
                            $Lname = isset($_POST["lname"]) ? $_POST["lname"] : "";  // Check if 'lname' exists in $_POST
                            $Email = isset($_POST["email"]) ? $_POST["email"] : "";  // Check if 'email' exists in $_POST
                            $DOB = isset($_POST["dob"]) ? $_POST["dob"] : "";        // Check if 'dob' exists in $_POST
                            $gender = isset($_POST["gender"]) ? $_POST["gender"] : "";// Check if 'gender' exists in $_POST
                            $phoneNo = isset($_POST['phone']) ? $_POST['phone'] : "";
                            // Update the candidate information
                            $sql2 = "UPDATE staff SET F_Name='$Fname', L_Name='$Lname', Gender='$gender', Email='$Email', DOB='$DOB' WHERE Role='Admin'";
                       
                            $result = mysqli_query($conn, $sql2);

                        $sql2="UPDATE  staff_phone_no SET S_phone_no='$phoneNo' WHERE S_ID='$Sid'";

                        $result = mysqli_query($conn, $sql2);
                        }
                        ?>
             <form method="post" id="profile-form">
             <h3 id="heading">Examiner details</h3>
            <label>First Name: </label><br>
            <input type="text" name="fname" placeholder="Enter First Name" value="<?php echo $Fname; ?>" required><br>

            <label>Last Name: </label><br>
            <input type="text" name="lname" placeholder="Enter Last Name" value="<?php echo $Lname; ?>" required><br>

            <label>Gender: </label>
            <label>
                <input type="radio" id="Male" name="gender" value="Male" <?php if ($gender == "Male") echo "checked"; ?>> Male
                <input type="radio" id="Female" name="gender" value="Female" <?php if ($gender == "Female") echo "checked"; ?>> Female<br>
            </label><br>

            <label>Mobile Number: </label><br>
            <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" placeholder="07XXXXXXXX" value="<?php echo $phoneNo; ?>" required><br>

            <label>Email: </label><br>
            <input type="email" id="email" name="email" placeholder="Enter Email" value="<?php echo $Email; ?>" required><br>

            <label>Date of Birth: </label><br>
            <input type="date" name="dob" value="<?php echo $DOB; ?>" required><br>
            
                   
            <center>
            <input type="submit" name="submit" id="submitbtn" value="Update"><br> 
            </center>
            </form>
                </div>
            
            </section>
    <div>
            <!-- ================Manage Exams Section====================-->
            <section id="manage-exams">
                <div class="section-header">
                    <h2>Manage Exams</h2>
                    <button class="btn add-btn" id="openPopupBtn">Add New 
                        Exam</button>

<!-- The Modal -->
<div id="myModal" class="modal">
    
    <div class="modal-content">
        <button class="close">close</button>
        <!-- PHP Form Start -->
        <?php
        include("config.php");

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $exam_id = $_POST["exam_id"];
            $ename = $_POST["ename"];
            $qpassword = $_POST["qpassword"];
            $Duration = $_POST["duration"];
            $sid = $_POST["sid"];

            global $conn;
            $message = "";

            // Use prepared statements to prevent SQL injection
            $stmt = $conn->prepare("INSERT INTO exam (E_ID, E_Name, Q_password, Duration, S_ID) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $exam_id, $ename, $qpassword, $Duration, $sid);

            if ($stmt->execute()) {
                echo '<script>alert("New Exam added Successfully");</script>';
                if ($_SESSION['Role'] == 'Examiner') {
                    echo '<script>window.location.href = "examiner.php";</script>';
                } elseif ($_SESSION['Role'] == 'Manager') {
                    echo '<script>window.location.href = "manager.php";</script>';
                } elseif ($_SESSION['Role'] == 'Admin') {
                    echo '<script>window.location.href = "admin.php";</script>';
                }
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        }
        ?>

        <!--================ Add Exam Form ==================== -->
        <div class="add-exam">
            <h2 id="heading"> Add Exam </h2>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                
                <label for="exam_id">Exam ID:</label><br>
                <input type="text" id="exam_id" name="exam_id" required placeholder="Exam ID"><br>

                <label for="ename">Exam Name:</label><br>
                <input type="text" id="ename" name="ename" required placeholder="Exam Name"><br>

                <label for="qpassword">Quiz Password:</label><br>
                <input type="text" id="qpassword" name="qpassword" required placeholder="Quiz Password"><br>

                <label for="duration">Exam Duration:</label><br>
                <input type="text" name="duration" id="duration" pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}" placeholder="00:00:00" required><br>

                <label for="sid">Staff ID:</label><br>
                <?php
                // Select staff IDs from database
                $sql = "SELECT S_ID FROM staff";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
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
                } else {
                    echo "No Staff ID Found.";
                }
                ?>
                <input type="submit" name="submit" value="Add Exam">
            </form>
        </div>
        <!--========= PHP Form End========= -->
    </div>
</div>
                </div>
                <div id="exam-list">
                    <h3>Current Exams</h3>
                    <table class="table-style">
                        <thead>
                        <tr>
                            <th>Exam ID</th>
                            <th>Exam Name</th>
                            <th>Password</th>
                            <th>Duration</th> 
                            <th width= "18%">Operations</th>
                        </tr>
                        </thead>
                        <tbody>

                          <?php
                            // Query to get exam details to display
                            $sql = "SELECT E_ID, E_Name ,Q_password, Duration, S_ID FROM exam"; // Correct column name: 'duration'

                            // Execute the query
                            $result = mysqli_query($conn, $sql);
                            
                            // Check if the query returns any rows
                            if ($result && mysqli_num_rows($result) > 0) {
                                // Loop through the results
                                while ($row = mysqli_fetch_assoc($result)) { // Using mysqli_fetch_assoc() for procedural style
                                    $eid = $row['E_ID'];
                                    $examname=$row['E_Name'];
                                    $password = $row['Q_password'];
                                    $duration = $row['Duration'];

                                    // Output the table rows
                                    echo '<tr>
                                          <td>' . $eid. '</td>                            
                                          <td>' . $examname . '</td>
                                          <td>' . $password . '</td>
                                          <td>' . $duration . '</td>
                                          <td>
                                            <center>
                                              <button class="update-btn">
                                                <a href="updateExam.php?updateid='.$eid.'">Update</a>
                                              <button class = "delete-btn" onclick="ConfirmDelete_Exam()"><a href="deleteExam.php?deleteid='.$eid.'">delete</a></button>

                                            </center>
                                          </td>
                                      </tr>';
                                }
                            } else {
                                echo "<tr><td colspan='6'>No results found</td></tr>";
                            }
                          ?>
                        </tbody> 
                    </table>  
                </div>
            </section>
                        
    <script src="script/admin.js"></script>
    <script src="script/examiner.js"></script>


    <?php
        include ("php/footer.php")
    ?>
</body>
</html>
