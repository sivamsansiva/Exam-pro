<?php
// User Dashboard - Enhanced for User Module
include("../config/config.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit();
}

$email = $_SESSION['email'];
$query = "SELECT C_ID,F_Name, L_Name, D_ID, DOB, NIC, Email, Age, Gender FROM exam_candidate WHERE Email = '$email'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
    $user_id = $user_data['C_ID'];
    $_SESSION['C_ID'] = $user_id;
} else {
    echo "User not found in candidate database.";
    exit();
}

// Fetch candidate phone number
$sql = "SELECT * FROM exam_candidate_phone_no WHERE C_ID= '$user_id'";
$result1 = mysqli_query($conn, $sql);
$user_data1 = mysqli_fetch_assoc($result1);

$updateSuccess = false;
$updateError = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Fname = mysqli_real_escape_string($conn, $_POST["F_Name"]);
    $Lname = mysqli_real_escape_string($conn, $_POST["L_Name"]);
    $Department_ID = mysqli_real_escape_string($conn, $_POST["D_ID"]);
    $Email = mysqli_real_escape_string($conn, $_POST["Email"]);
    $DOB = mysqli_real_escape_string($conn, $_POST["DOB"]);
    $NIC = mysqli_real_escape_string($conn, $_POST["NIC"]);
    $Phone_no = mysqli_real_escape_string($conn, $_POST["phone"]);

    // Update candidate information
    $updateQuery = "UPDATE exam_candidate SET
        F_Name='$Fname',
        L_Name='$Lname',
        Email='$Email',
        DOB='$DOB',
        NIC='$NIC',
        D_ID='$Department_ID'
        WHERE C_ID='$user_id'";

    // Execute update query
    if (mysqli_query($conn, $updateQuery)) {
        // Update phone number separately
        $checkPhone = "SELECT * FROM exam_candidate_phone_no WHERE C_ID='$user_id'";
        $checkResult = mysqli_query($conn, $checkPhone);
        if (mysqli_num_rows($checkResult) > 0) {
            $updatePhoneQuery = "UPDATE exam_candidate_phone_no SET Phone_no='$Phone_no' WHERE C_ID='$user_id'";
            mysqli_query($conn, $updatePhoneQuery);
        } else {
            $insertPhoneQuery = "INSERT INTO exam_candidate_phone_no (C_ID, Phone_no) VALUES ('$user_id', '$Phone_no')";
            mysqli_query($conn, $insertPhoneQuery);
        }

        $updateSuccess = true;
        // Refresh data
        $result = mysqli_query($conn, $query);
        $user_data = mysqli_fetch_assoc($result);
        $result1 = mysqli_query($conn, $sql);
        $user_data1 = mysqli_fetch_assoc($result1);
    }
    else {
        $updateError = "Error updating profile: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Profile</title>
        <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="../styles/theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>

<body>
    <?php include ("../includes/header.php"); ?>

    <div class="container mt-xl">
        <div class="mb-xl text-center">
            <h1>Hello, <span id="greetingName"><?php echo htmlspecialchars($user_data['F_Name'] . ' ' . $user_data['L_Name']); ?></span>!</h1>
            <p>Welcome to your dashboard.</p>
        </div>

        <?php if ($updateSuccess): ?>
            <div class="alert alert-success mb-lg">
                <i class="fas fa-check-circle"></i> Profile updated successfully!
            </div>
        <?php endif; ?>

        <?php if ($updateError): ?>
            <div class="alert alert-error mb-lg">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($updateError); ?>
            </div>
        <?php endif; ?>

        <div class="grid-2">
            <!-- Profile Section -->
            <div class="card mb-xl">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-user"></i> My Profile</h2>
                </div>
                <form id="profileForm" action="user_dashboard.php" method="POST" onsubmit="return validateForm();">
                    <div class="form-group">
                        <label class="form-label">First Name</label>
                        <input type="text" name="F_Name" class="form-control" value="<?php echo htmlspecialchars($user_data['F_Name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="L_Name" class="form-control" value="<?php echo htmlspecialchars($user_data['L_Name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Candidate ID</label>
                        <input type="text" name="C_ID" class="form-control" value="<?php echo htmlspecialchars($user_data['C_ID']); ?>" readonly style="background-color: #f0f0f0;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Department ID</label>
                        <input type="text" name="D_ID" class="form-control" value="<?php echo htmlspecialchars($user_data['D_ID']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="DOB" class="form-control" value="<?php echo htmlspecialchars($user_data['DOB']); ?>" required>
                        <span class="text-error" id="dobError"></span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">NIC</label>
                        <input type="text" name="NIC" class="form-control" value="<?php echo htmlspecialchars($user_data['NIC']); ?>" required>
                        <span class="text-error" id="nicError"></span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="Email" class="form-control" value="<?php echo htmlspecialchars($user_data['Email']); ?>" required>
                        <span class="text-error" id="emailError"></span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <input type="text" name="Gender" class="form-control" value="<?php echo htmlspecialchars($user_data['Gender']); ?>" readonly style="background-color: #f0f0f0;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mobile Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" pattern="[0-9]{10}" placeholder="07XXXXXXXX" value="<?php echo htmlspecialchars($user_data1['Phone_no'] ?? ''); ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
            </div>

            <!-- Exam Results Section -->
            <div class="card mb-xl">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-graduation-cap"></i> Exam Results</h2>
                </div>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Exam ID</th>
                                <th>Exam Name</th>
                                <th>Result</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT exam.E_ID, exam.E_Name, attends.Result
                                    FROM exam
                                    JOIN attends ON exam.E_ID = attends.E_ID
                                    WHERE attends.C_ID = '" . $user_data['C_ID'] . "'";

                            $result = mysqli_query($conn, $sql);

                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '<tr>
                                          <td>' . htmlspecialchars($row['E_ID']) . '</td>
                                          <td>' . htmlspecialchars($row['E_Name']) . '</td>
                                          <td><span class="badge badge-primary">' . htmlspecialchars($row['Result']) . '</span></td>
                                      </tr>';
                                }
                            } else {
                                echo "<tr><td colspan='3' class='text-center'>No exam results found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-lg">
                    <a href="../exams/registerExam.php" class="btn btn-outline" style="width: 100%; justify-content: center;">
                        <i class="fas fa-plus-circle"></i> Register for New Exam
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include ("../includes/footer.php"); ?>

    <script>
        function validateForm() {
            let dob = document.forms["profileForm"]["DOB"].value;
            let nic = document.forms["profileForm"]["NIC"].value;
            let email = document.forms["profileForm"]["Email"].value;

            let dobError = document.getElementById("dobError");
            let nicError = document.getElementById("nicError");
            let emailError = document.getElementById("emailError");

            let isValid = true;

            // Clear previous error messages
            dobError.textContent = "";
            nicError.textContent = "";
            emailError.textContent = "";

            // Validate Date of Birth
            const dobDate = new Date(dob);
            const today = new Date();
            const maxDOB = new Date();
            maxDOB.setFullYear(today.getFullYear() - 18); // Changed to 18 for general usage

            if (dobDate > today) {
                dobError.textContent = "Date of Birth cannot be in the future.";
                isValid = false;
            }
            // Removed strict 21 age limit, kept future check.

            // Validate NIC
            let nicPattern = /^[0-9]{9}[Vv]$|^[0-9]{12}$/;
            if (!nicPattern.test(nic)) {
                nicError.textContent = "NIC must be 9 digits followed by 'V' or 12 digits.";
                isValid = false;
            }

            // Validate Email format
            let emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            if (!emailPattern.test(email)) {
                emailError.textContent = "Invalid email format.";
                isValid = false;
            }

            return isValid;
        }

        function updateGreeting() {
            var firstName = document.querySelector('input[name="F_Name"]').value;
            var lastName = document.querySelector('input[name="L_Name"]').value;
            var greetingName = document.getElementById("greetingName");

            greetingName.textContent = firstName + " " + lastName;
        }

        // Attach event listeners
        document.querySelector('input[name="F_Name"]').addEventListener("input", updateGreeting);
        document.querySelector('input[name="L_Name"]').addEventListener("input", updateGreeting);
    </script>
</body>
</html>
