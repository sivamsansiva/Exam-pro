<?php
// User Dashboard
include("../config/config.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$email = $_SESSION['email'];

// Fetch user information
$query = "SELECT id, first_name, last_name, email, department_id, dob, nic, gender FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

// Fetch user phone number
$sql = "SELECT phone FROM user_phone WHERE user_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result1 = $stmt->get_result();
$user_data1 = $result1->fetch_assoc();

$updateSuccess = false;
$updateError = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = mysqli_real_escape_string($conn, $_POST["first_name"]);
    $lastName = mysqli_real_escape_string($conn, $_POST["last_name"]);
    $departmentId = (int)$_POST["department_id"];
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $dob = mysqli_real_escape_string($conn, $_POST["dob"]);
    $nic = mysqli_real_escape_string($conn, $_POST["nic"]);
    $phoneNo = mysqli_real_escape_string($conn, $_POST["phone"]);

    // Update user information
    $updateQuery = "UPDATE users SET
        first_name=?,
        last_name=?,
        email=?,
        dob=?,
        nic=?,
        department_id=?,
        updated_at=NOW()
        WHERE id=?";

    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssiii", $firstName, $lastName, $email, $dob, $nic, $departmentId, $userId);

    if ($stmt->execute()) {
        // Update phone number
        $checkPhone = "SELECT * FROM user_phone WHERE user_id=?";
        $stmt = $conn->prepare($checkPhone);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $checkResult = $stmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            $updatePhoneQuery = "UPDATE user_phone SET phone=? WHERE user_id=?";
            $stmt = $conn->prepare($updatePhoneQuery);
            $stmt->bind_param("si", $phoneNo, $userId);
            $stmt->execute();
        } else {
            $insertPhoneQuery = "INSERT INTO user_phone (user_id, phone) VALUES (?, ?)";
            $stmt = $conn->prepare($insertPhoneQuery);
            $stmt->bind_param("is", $userId, $phoneNo);
            $stmt->execute();
        }

        $updateSuccess = true;
        // Refresh data
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_data = $result->fetch_assoc();
        
        $stmt = $conn->prepare("SELECT phone FROM user_phone WHERE user_id = ? LIMIT 1");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result1 = $stmt->get_result();
        $user_data1 = $result1->fetch_assoc();
    }
    else {
        $updateError = "Error updating profile: " . $conn->error;
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
            <h1>Hello, <span id="greetingName"><?php echo htmlspecialchars($user_data['first_name'] . ' ' . $user_data['last_name']); ?></span>!</h1>
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
                        <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user_data['first_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($user_data['last_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">User ID</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user_data['id']); ?>" readonly style="background-color: #f0f0f0;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Department</label>
                        <?php
                            $deptSql = "SELECT id, name FROM department ORDER BY name";
                            $deptResult = $conn->query($deptSql);
                            if($deptResult && $deptResult->num_rows > 0){
                        ?>
                        <select name="department_id" class="form-control" required>
                            <option value="">Select Department</option>
                            <?php
                                while ($dept = $deptResult->fetch_assoc()) {
                                    $selected = ($user_data['department_id'] == $dept['id']) ? 'selected' : '';
                                    echo "<option value=\"" . htmlspecialchars($dept['id']) . "\" $selected>" . htmlspecialchars($dept['name']) . "</option>";
                                }
                            ?>
                        </select>
                        <?php
                            } else {
                                echo "<input type='text' class='form-control' value='No departments' readonly>";
                            }
                        ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" value="<?php echo htmlspecialchars($user_data['dob']); ?>" required>
                        <span class="text-error" id="dobError"></span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">NIC</label>
                        <input type="text" name="nic" class="form-control" value="<?php echo htmlspecialchars($user_data['nic']); ?>" required>
                        <span class="text-error" id="nicError"></span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                        <span class="text-error" id="emailError"></span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <div style="display: flex; gap: 1.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="radio" name="gender" value="male" <?php if ($user_data['gender'] == "male") echo "checked"; ?> disabled> Male
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="radio" name="gender" value="female" <?php if ($user_data['gender'] == "female") echo "checked"; ?> disabled> Female
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="radio" name="gender" value="other" <?php if ($user_data['gender'] == "other") echo "checked"; ?> disabled> Other
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mobile Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" pattern="[0-9]{10}" placeholder="07XXXXXXXX" value="<?php echo htmlspecialchars($user_data1['phone'] ?? ''); ?>" required>
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
                                <th>Exam Code</th>
                                <th>Exam Name</th>
                                <th>Score</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT e.code, e.name, a.score, a.ended_at
                                    FROM exam_attempt a
                                    JOIN exam e ON a.exam_id = e.id
                                    WHERE a.user_id = ? AND a.completed = 1
                                    ORDER BY a.ended_at DESC";

                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $userId);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $scoreClass = $row['score'] >= 75 ? 'success' : 'warning';
                                    echo '<tr>
                                          <td>' . htmlspecialchars($row['code']) . '</td>
                                          <td>' . htmlspecialchars($row['name']) . '</td>
                                          <td><span class="badge badge-' . $scoreClass . '">' . number_format($row['score'], 2) . '%</span></td>
                                          <td>' . htmlspecialchars(date('Y-m-d H:i', strtotime($row['ended_at']))) . '</td>
                                      </tr>';
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>No exam results found</td></tr>";
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
            let dob = document.forms["profileForm"]["dob"].value;
            let nic = document.forms["profileForm"]["nic"].value;
            let email = document.forms["profileForm"]["email"].value;

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

            if (dobDate > today) {
                dobError.textContent = "Date of Birth cannot be in the future.";
                isValid = false;
            }

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
            var firstName = document.querySelector('input[name="first_name"]').value;
            var lastName = document.querySelector('input[name="last_name"]').value;
            var greetingName = document.getElementById("greetingName");

            greetingName.textContent = firstName + " " + lastName;
        }

        // Attach event listeners
        document.querySelector('input[name="first_name"]').addEventListener("input", updateGreeting);
        document.querySelector('input[name="last_name"]').addEventListener("input", updateGreeting);
    </script>
</body>
</html>
