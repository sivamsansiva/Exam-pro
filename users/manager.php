<?php
// session_start();
include('php/config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Manager'){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Profile Page</title>
    <link rel="stylesheet" href="style/admin.css">
    <link rel="stylesheet" href="style/add_exam_AD.css">
</head>
<body>
    <?php
        include ("php/header.php");
    ?>
    <div class="admin-container">
        <main class="main-content">
            <!--================ Manager Profile Section ===============-->
            <section id="profile">
                <div class="section-header">
                    <h2>Manager Profile</h2>
                </div>
                <div class="profile-section" id="profile-section">
                    
                 <?php
                 $session['Role']="Manager";
                    $sql1 = "SELECT * FROM staff WHERE Role='Manager'";
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
             <h3 id="heading">Manager details</h3>
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
                            <th>Uploaded by</th>
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
                                    $uploader = $row['S_ID'];

                                    // Output the table rows
                                    echo '<tr>
                                          <td>' . $eid. '</td>                            
                                          <td>' . $examname . '</td>
                                          <td>' . $password . '</td>
                                          <td>' . $duration . '</td>
                                          <td>' . $uploader . '</td>
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
            <br>
            <br>
        
                    <!--===========Staffs Section =============-->
        
                    <section id="candidates">
                <div class="section-header">
                    <h2>Exam Candidates</h2>
                </div>
            <section id="section-header">
                <div id="canditate-list">
                    <h3>Exam Candidate Details</h3>
                    <table class="table-style" id="displayEC">
                            <thead>
                            <tr>
                            <th>Candidate ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Department ID</th>
                            <th>Date of Birth</th>
                            <th>NIC</th>
                            <th>Email</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th width="18%">Operations</th>
                            </tr>
                        </thead>
                    <tbody>
                        <?php
                            // Query to get exam details to display
                            $sql = "SELECT C_ID,F_Name,L_Name,D_ID,DOB,NIC,Email,Age,Gender FROM exam_candidate"; // Correct column name: 'duration'

                            // Execute the query
                            $result = mysqli_query($conn, $sql);

                            if($result && $result->num_rows > 0 ){

                                while($row = $result->fetch_assoc()){
                                    
                                    $cid = $row['C_ID'];
                                    $FName = $row['F_Name'];
                                    $LName = $row['L_Name'];
                                    $DID = $row['D_ID'];
                                    $DOB = $row['DOB'];
                                    $NIC = $row['NIC'];
                                    $Email = $row['Email'];
                                    $Gender = $row['Age'];
                                    $Age = $row['Gender'];



                                    echo '<tr>
                                        <td>'.$cid.'</td>
                                        <td>'.$FName.'</td>
                                        <td>'.$LName.'</td>
                                        <td>'.$DID.'</td>
                                        <td>'.$DOB.'</td>
                                        <td>'.$NIC.'</td>
                                        <td>'.$Email.'</td>
                                        <td>'.$Age.'</td>
                                        <td>'.$Gender.'</td>
                                        
                                        <td>
                                        <center>
                                        <button class = "update-btn"><a href="update_EC.php?updateid='.$cid.'">Update</a></button>
                                        <button class = "delete-btn" onclick="ConfirmDelete()"><a href="delete_EC.php?deleteid='.$cid.'">Delete</a></button>
                                        </center>
                                        </td>
                                        </tr>';
                                }} else {
                                    echo "<tr><td colspan='6'>No results found</td></tr>";
                                }
                    
                        ?>
                    </tbody> 
                    </table>    
                </div>  
                </section>
             
            </section>
        
        <br>
        <br>
               

            <!--================Staffs Section =======================-->
        <div>
            <section id="Staffs">
                <div class="section-header">
                    <h2>Staff</h2>
                </div>
                <div id="staff-list">
                    <h3>staff Details</h3>
                    <table class="table-style">
                            <thead>
                            <tr>
                            <th>Staff ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Department ID</th>
                            <th>Date of Birth</th>
                            <th>NIC</th>
                            <th>EMail</th>
                            <th>Gender</th>
                            <th>Age</th>
                            <th>Role</th>
                            <th width="18%">Operations</th>
                            </tr>
                        </thead>
                    <tbody>
                        <?php
                            // Query to get exam details to display
                            $sql = "SELECT S_ID,F_Name,L_Name,D_ID,DOB,NIC,Email,Gender,Age,Role FROM staff"; 
                            // Execute the query
                            $result = mysqli_query($conn, $sql);

                            if($result && $result->num_rows > 0 ){

                                while($row = $result->fetch_assoc()){
                                    
                                    $Sid = $row['S_ID'];
                                    $FName = $row['F_Name'];
                                    $LName = $row['L_Name'];
                                    $DID = $row['D_ID'];
                                    $DOB = $row['DOB'];
                                    $NIC = $row['NIC'];
                                    $Email = $row['Email'];
                                    $Gender = $row['Gender'];
                                    $Age = $row['Age'];
                                    $Role = $row['Role'];



                                    echo '<tr>
                                        <td>'.$Sid.'</td>
                                        <td>'.$FName.'</td>
                                        <td>'.$LName.'</td>
                                        <td>'.$DID.'</td>
                                        <td>'.$DOB.'</td>
                                        <td>'.$NIC.'</td>
                                        <td>'.$Email.'</td>
                                        <td>'.$Gender.'</td>
                                        <td>'.$Age.'</td>
                                        <td>'.$Role.'</td>
                                        <td>
                                        <center>
                                        <button class = "update-btn"><a href="update_staff.php?updateid='.$Sid.'">Update</a></button>
                                        <button class = "delete-btn" onclick="ConfirmDelete()"><a href="delete_STF.php?deleteid='.$Sid.'">Delete</a></button>
                                        </center>
                                        </td>
                                        </tr>';
                                }} else {
                                    echo "<tr><td colspan='6'>No results found</td></tr>";
                                }
                    
                        ?>
                    </tbody> 
                    </table>    
                </div>  
                    
            
            </section>
        </div>             
    <script src="script/admin.js"></script>


    <?php
        include ("php/footer.php")
    ?>
</body>
</html>
