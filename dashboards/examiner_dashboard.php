<?php
    include('../config/config.php');

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Examiner'){
        header("Location: ../auth/login.php");
        exit();
    }

    // Handle Profile Update
    $updateSuccess = false;
    if (isset($_POST['update_profile'])) {
        $Fname = mysqli_real_escape_string($conn, $_POST["fname"]);
        $Lname = mysqli_real_escape_string($conn, $_POST["lname"]);
        $Email = mysqli_real_escape_string($conn, $_POST["email"]);
        $DOB = mysqli_real_escape_string($conn, $_POST["dob"]);
        $gender = mysqli_real_escape_string($conn, $_POST["gender"]);
        $phoneNo = mysqli_real_escape_string($conn, $_POST['phone']);

        $sql2 = "UPDATE staff SET F_Name='$Fname', L_Name='$Lname', Gender='$gender', Email='$Email', DOB='$DOB' WHERE Role='Examiner'";
        $result = mysqli_query($conn, $sql2);

        // Get Examiner ID to update phone
        $examinerQuery = "SELECT S_ID FROM staff WHERE Role='Examiner'";
        $examinerResult = mysqli_query($conn, $examinerQuery);
        $examinerRow = mysqli_fetch_assoc($examinerResult);
        $Sid = $examinerRow['S_ID'];

        $sql3 = "UPDATE staff_phone_no SET S_phone_no='$phoneNo' WHERE S_ID='$Sid'";
        $result3 = mysqli_query($conn, $sql3);

        if ($result && $result3) {
            $updateSuccess = true;
        }
    }

    // Handle Add Exam
    $addExamSuccess = false;
    if (isset($_POST['add_exam'])) {
        $exam_id = $_POST["exam_id"];
        $ename = $_POST["ename"];
        $qpassword = $_POST["qpassword"];
        $Duration = $_POST["duration"];
        $sid = $_POST["sid"];

        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO exam (E_ID, E_Name, Q_password, Duration, S_ID) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $exam_id, $ename, $qpassword, $Duration, $sid);

        if ($stmt->execute()) {
            $addExamSuccess = true;
        } else {
            $addExamError = $stmt->error;
        }
        $stmt->close();
    }

    // Fetch Examiner Details
    $sql1 = "SELECT * FROM staff WHERE Role='Examiner'";
    $result = mysqli_query($conn, $sql1);
    $row = $result->fetch_assoc();
    $Sid = $row['S_ID'];
    $Fname = $row['F_Name'];
    $Lname = $row['L_Name'];
    $DOB = $row['DOB'];
    $Email = $row['Email'];
    $gender = $row['Gender'];
    $Age = $row['Age'];

    // Fetch Examiner Phone
    $sql1 = "SELECT * FROM staff_phone_no WHERE S_ID='$Sid'";
    $result = mysqli_query($conn, $sql1);
    $phoneRow = $result->fetch_assoc();
    $phoneNo = $phoneRow['S_phone_no'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examiner Dashboard - ExamPro</title>
    <link rel="stylesheet" href="../styles/theme.css">
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include ("../includes/header.php"); ?>

    <div class="container mt-xl">
        <div class="mb-xl">
            <h1>Examiner Dashboard</h1>
            <p>Manage exams and your profile.</p>
        </div>

        <?php if ($updateSuccess): ?>
            <div class="alert alert-success mb-lg">
                <i class="fas fa-check-circle"></i> Profile updated successfully!
            </div>
        <?php endif; ?>

        <?php if (isset($addExamSuccess) && $addExamSuccess): ?>
            <div class="alert alert-success mb-lg">
                <i class="fas fa-check-circle"></i> New Exam added successfully!
            </div>
        <?php endif; ?>

        <?php if (isset($addExamError)): ?>
            <div class="alert alert-error mb-lg">
                <i class="fas fa-exclamation-circle"></i> Error adding exam: <?php echo htmlspecialchars($addExamError); ?>
            </div>
        <?php endif; ?>

        <div class="grid-2">
            <!-- Examiner Profile Section -->
            <div class="card mb-xl">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-user-tie"></i> Examiner Profile</h2>
                </div>
                <form method="post" id="profile-form">
                    <div class="form-group">
                        <label class="form-label">First Name</label>
                        <input type="text" name="fname" class="form-control" value="<?php echo htmlspecialchars($Fname); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="lname" class="form-control" value="<?php echo htmlspecialchars($Lname); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <div style="display: flex; gap: 1.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="radio" name="gender" value="Male" <?php if ($gender == "Male") echo "checked"; ?>> Male
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="radio" name="gender" value="Female" <?php if ($gender == "Female") echo "checked"; ?>> Female
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mobile Number</label>
                        <input type="tel" name="phone" class="form-control" pattern="[0-9]{10}" value="<?php echo htmlspecialchars($phoneNo); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($Email); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" value="<?php echo htmlspecialchars($DOB); ?>" required>
                    </div>

                    <button type="submit" name="update_profile" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-xl">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-bolt"></i> Quick Actions</h2>
                </div>
                <div style="display: grid; gap: 1rem;">
                    <button id="openPopupBtn" class="btn btn-secondary" style="justify-content: flex-start;">
                        <i class="fas fa-plus-circle"></i> Add New Exam
                    </button>
                    <a href="#manage-exams" class="btn btn-outline" style="justify-content: flex-start;">
                        <i class="fas fa-list"></i> Manage Exams
                    </a>
                </div>
            </div>
        </div>

        <!-- Manage Exams Section -->
        <div class="card mb-xl" id="manage-exams">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-file-alt"></i> Manage Exams</h2>
            </div>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Exam ID</th>
                            <th>Exam Name</th>
                            <th>Password</th>
                            <th>Duration</th>
                            <th>Operations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT E_ID, E_Name, Q_password, Duration, S_ID FROM exam";
                        $result = mysqli_query($conn, $sql);

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<tr>
                                      <td>' . htmlspecialchars($row['E_ID']) . '</td>
                                      <td>' . htmlspecialchars($row['E_Name']) . '</td>
                                      <td>' . htmlspecialchars($row['Q_password']) . '</td>
                                      <td>' . htmlspecialchars($row['Duration']) . '</td>
                                      <td>
                                          <div style="display: flex; gap: 0.5rem;">
                                              <a href="../exams/updateExam.php?updateid=' . $row['E_ID'] . '" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                              <a href="../exams/deleteExam.php?deleteid=' . $row['E_ID'] . '" class="btn btn-sm btn-secondary" onclick="return confirm(\'Are you sure you want to delete this exam?\')"><i class="fas fa-trash"></i></a>
                                          </div>
                                      </td>
                                  </tr>';
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>No exams found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Exam Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Add New Exam</h2>
                <button class="modal-close">&times;</button>
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="exam_id" class="form-label">Exam ID</label>
                    <input type="text" id="exam_id" name="exam_id" class="form-control" required placeholder="E001">
                </div>

                <div class="form-group">
                    <label for="ename" class="form-label">Exam Name</label>
                    <input type="text" id="ename" name="ename" class="form-control" required placeholder="Mathematics">
                </div>

                <div class="form-group">
                    <label for="qpassword" class="form-label">Quiz Password</label>
                    <input type="text" id="qpassword" name="qpassword" class="form-control" required placeholder="Secret123">
                </div>

                <div class="form-group">
                    <label for="duration" class="form-label">Exam Duration</label>
                    <input type="text" name="duration" id="duration" class="form-control" pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}" placeholder="00:00:00" required>
                    <small class="text-muted">Format: HH:MM:SS</small>
                </div>

                <div class="form-group">
                    <label for="sid" class="form-label">Staff ID</label>
                    <?php
                    $sql = "SELECT S_ID FROM staff";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                    ?>
                        <select name="sid" id="sid" class="form-control" required>
                            <option value="" disabled selected>Select Staff ID</option>
                            <?php
                            while ($row = $result->fetch_assoc()) {
                                $sid = $row['S_ID'];
                                echo "<option>$sid</option>";
                            }
                            ?>
                        </select>
                    <?php
                    } else {
                        echo "<p class='text-error'>No Staff ID Found.</p>";
                    }
                    ?>
                </div>

                <button type="submit" name="add_exam" class="btn btn-primary" style="width: 100%;">Add Exam</button>
            </form>
        </div>
    </div>

    <?php include ("../includes/footer.php"); ?>

    <script>
        // Modal Logic
        const modal = document.getElementById("myModal");
        const btn = document.getElementById("openPopupBtn");
        const closeBtn = document.getElementsByClassName("modal-close")[0];

        if (btn) {
            btn.onclick = function () {
                modal.classList.add("show");
            }
        }

        if (closeBtn) {
            closeBtn.onclick = function () {
                modal.classList.remove("show");
            }
        }

        window.onclick = function (event) {
            if (event.target == modal) {
                modal.classList.remove("show");
            }
        }
    </script>
</body>
</html>
