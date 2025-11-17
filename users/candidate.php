<?php
// Assuming session is started and the user is logged in
include("php/config.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Employee'){
    header("Location: login.php");
    exit();
}

$_SESSION['C_ID']='C001';
$user_id = $_SESSION['C_ID'];
$query = "SELECT C_ID,F_Name, L_Name, D_ID, DOB, NIC, Email, Age, Gender FROM exam_candidate WHERE C_ID = '$user_id'";
$result = mysqli_query($conn, $query);
$user_data = mysqli_fetch_assoc($result);

// Fetch candidate phone number
$sql = "SELECT * FROM exam_candidate_phone_no WHERE C_ID= '$user_id'";
$result1 = mysqli_query($conn, $sql);
$user_data1 = mysqli_fetch_assoc($result1);

$updateSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Fname = $_POST["F_Name"];
    $Lname = $_POST["L_Name"];
    $Department_ID = $_POST["D_ID"];
    $Email = $_POST["Email"];
    $DOB = $_POST["DOB"];
    $NIC = $_POST["NIC"];
    $Phone_no = $_POST["phone"];

    // Update candidate information
    $updateQuery = "UPDATE exam_candidate SET
        F_Name='$Fname',
        L_Name='$Lname',
        Email='$Email',
        DOB='$DOB',
        NIC='$NIC'
        WHERE C_ID='$user_id'";

    // Execute update query
    if (mysqli_query($conn, $updateQuery)) {
        // Update phone number separately (assuming you have a separate table for phone numbers)
        $updatePhoneQuery = "UPDATE exam_candidate_phone_no SET Phone_no='$Phone_no' WHERE C_ID='$user_id'";
        mysqli_query($conn, $updatePhoneQuery);

        // Optional: Display a success message or redirect the user
        $updateSuccess = true;
    } 
    else {
        echo "Error updating profile: " . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Profile</title>
        <link rel="stylesheet" href="style/candidate.css">
    </head>

    <body>
        <?php
            include ("php/header.php");
        ?>
    <!-------------------- Greeting -------------------->
        <h2 id="nameGreeting">Hello, <span id="greetingName"><?php echo htmlspecialchars($user_data['F_Name'] . ' ' . $user_data['L_Name']); ?></span>!</h2>

        <div class="user-profile">
        <form id="profileForm" action="candidate.php" method="POST" onsubmit="return validateForm();">
            <div class="form-group">
                <label>First Name:</label>
                <input type="text" name="F_Name" value="<?php echo htmlspecialchars($user_data['F_Name']); ?>" required>
            </div>

            <div class="form-group">
                <label>Last Name:</label>
                <input type="text" name="L_Name" value="<?php echo htmlspecialchars($user_data['L_Name']); ?>" required>
            </div>

            <div class="form-group">
                <label>Employee ID:</label>
                <input type="text" name="C_ID" value="<?php echo htmlspecialchars($user_data['C_ID']); ?>" readonly>
            </div>

            <div class="form-group">
                <label>Department:</label>
                <input type="text" name="D_ID" value="<?php echo htmlspecialchars($user_data['D_ID']); ?>" required>
            </div>

            <div class="form-group">
                <label>Date of Birth:</label>
                <input type="date" name="DOB" value="<?php echo htmlspecialchars($user_data['DOB']); ?>" required>
                <span class="error" id="dobError"></span>
            </div>

            <div class="form-group">
                <label>NIC:</label>
                <input type="text" name="NIC" value="<?php echo htmlspecialchars($user_data['NIC']); ?>" required>
                <span class="error" id="nicError"></span>
            </div>

            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="Email" value="<?php echo htmlspecialchars($user_data['Email']); ?>" required>
                <span class="error" id="emailError"></span>
            </div>

            <div class="form-group">
                <label>Gender:</label>
                <input type="text" name="Gender" value="<?php echo htmlspecialchars($user_data['Gender']); ?>" readonly>
            </div>

            <div class="form-group">
            <label>Mobile Number: </label>
            <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" placeholder="07XXXXXXXX" value="<?php echo htmlspecialchars($user_data1['Phone_no']); ?>" required><br>
            </div>

            <button type="submit" class="submit-btn">Update Profile</button>

                <div id="examDetails">
                    <h2> Exam Details </h2>
                    <table class="examTable">
                        <thead>
                        <tr>
                            <th>Exam ID</th>
                            <th>Exam Name</th>
                            <th>Results</th>
                        </tr>
                        </thead>
                        <tbody>

                          <?php
                            // Query to get exam details to display
                            $sql = "SELECT exam.E_ID, exam.E_Name, attends.Result
                                    FROM exam
                                    JOIN attends ON exam.E_ID = attends.E_ID
                                    WHERE attends.C_ID = '" . $user_data['C_ID'] . "'
                                    ";

                            // Execute the query
                            $result = mysqli_query($conn, $sql);
                            
                            // Check if the query returns any rows
                            if ($result && mysqli_num_rows($result) > 0) {
                                // Loop through the results
                                while ($row = mysqli_fetch_assoc($result)) { // Using mysqli_fetch_assoc() for procedural style
                                    $eid = $row['E_ID'];
                                    $examname=$row['E_Name'];
                                    $results = $row['Result'];

                                    // Output the table rows
                                    echo '<tr>
                                          <td>' . htmlspecialchars($eid). '</td>                            
                                          <td>' . htmlspecialchars($examname) . '</td>
                                          <td>' . htmlspecialchars($results) . '</td>
                                      </tr>';
                                }
                            } else {
                                echo "<tr><td colspan='3'>No results found</td></tr>";
                            }
                          ?>
                        </tbody> 
                    </table>  
                </div>
            </div>
        <!------------- Show success alert if the profile was updated ------------->
        <script>
            <?php if ($updateSuccess) : ?>
            alert("Profile updated successfully!");
            <?php endif; ?>
        </script>

                <!------------ Include JavaScript file here --------------->
        <script src="script/candidate.js"></script> 
        <?php
            include ("php/footer.php");
        ?>
    </body>
</html>