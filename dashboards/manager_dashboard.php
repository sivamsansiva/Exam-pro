<?php
    include('../config/config.php');

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Manager'){
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

        $sql2 = "UPDATE staff SET F_Name='$Fname', L_Name='$Lname', Gender='$gender', Email='$Email', DOB='$DOB' WHERE Role='Manager'";
        $result = mysqli_query($conn, $sql2);

        // Get Manager ID
        $managerQuery = "SELECT S_ID FROM staff WHERE Role='Manager'";
        $managerResult = mysqli_query($conn, $managerQuery);
        $managerRow = mysqli_fetch_assoc($managerResult);
        $Sid = $managerRow['S_ID'];

        $sql3 = "UPDATE staff_phone_no SET S_phone_no='$phoneNo' WHERE S_ID='$Sid'";
        $result3 = mysqli_query($conn, $sql3);

        if ($result && $result3) {
            $updateSuccess = true;
        }
    }

    // Fetch Manager Details
    $sql1 = "SELECT * FROM staff WHERE Role='Manager'";
    $result = mysqli_query($conn, $sql1);
    $row = $result->fetch_assoc();
    $Sid = $row['S_ID'];
    $Fname = $row['F_Name'];
    $Lname = $row['L_Name'];
    $DOB = $row['DOB'];
    $Email = $row['Email'];
    $gender = $row['Gender'];
    $Age = $row['Age'];

    // Fetch Manager Phone
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
    <title>Manager Dashboard - ExamPro</title>
    <link rel="stylesheet" href="../styles/theme.css">
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include ("../includes/header.php"); ?>

    <div class="container mt-xl">
        <div class="mb-xl">
            <h1>Manager Dashboard</h1>
            <p>Manage exams, candidates, and staff.</p>
        </div>

        <?php if ($updateSuccess): ?>
            <div class="alert alert-success mb-lg">
                <i class="fas fa-check-circle"></i> Profile updated successfully!
            </div>
        <?php endif; ?>

        <div class="grid-2">
            <!-- Manager Profile Section -->
            <div class="card mb-xl">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-user-tie"></i> Manager Profile</h2>
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

            <!-- Quick Stats / Navigation -->
            <div class="card mb-xl">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-chart-pie"></i> Overview</h2>
                </div>
                <div style="display: grid; gap: 1rem;">
                    <a href="#manage-exams" class="btn btn-outline" style="justify-content: flex-start;">
                        <i class="fas fa-file-alt"></i> Manage Exams
                    </a>
                    <a href="#candidates" class="btn btn-outline" style="justify-content: flex-start;">
                        <i class="fas fa-users"></i> Manage Candidates
                    </a>
                    <a href="#staffs" class="btn btn-outline" style="justify-content: flex-start;">
                        <i class="fas fa-chalkboard-teacher"></i> Manage Staff
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
                            <th>Uploaded By</th>
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
                                      <td>' . htmlspecialchars($row['S_ID']) . '</td>
                                      <td>
                                          <div style="display: flex; gap: 0.5rem;">
                                              <a href="../exams/updateExam.php?updateid=' . $row['E_ID'] . '" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                              <a href="../exams/deleteExam.php?deleteid=' . $row['E_ID'] . '" class="btn btn-sm btn-secondary" onclick="return confirm(\'Are you sure you want to delete this exam?\')"><i class="fas fa-trash"></i></a>
                                          </div>
                                      </td>
                                  </tr>';
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>No exams found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Candidates Section -->
        <div class="card mb-xl" id="candidates">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-users"></i> Exam Candidates</h2>
            </div>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Dept ID</th>
                            <th>DOB</th>
                            <th>NIC</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Operations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT C_ID, F_Name, L_Name, D_ID, DOB, NIC, Email, Gender FROM exam_candidate";
                        $result = mysqli_query($conn, $sql);

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<tr>
                                    <td>' . htmlspecialchars($row['C_ID']) . '</td>
                                    <td>' . htmlspecialchars($row['F_Name'] . ' ' . $row['L_Name']) . '</td>
                                    <td>' . htmlspecialchars($row['D_ID']) . '</td>
                                    <td>' . htmlspecialchars($row['DOB']) . '</td>
                                    <td>' . htmlspecialchars($row['NIC']) . '</td>
                                    <td>' . htmlspecialchars($row['Email']) . '</td>
                                    <td>' . htmlspecialchars($row['Gender']) . '</td>
                                    <td>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <a href="../users/updateCandidate.php?updateid=' . $row['C_ID'] . '" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                            <a href="../users/deleteCandidate.php?deleteid=' . $row['C_ID'] . '" class="btn btn-sm btn-secondary" onclick="return confirm(\'Are you sure you want to delete this candidate?\')"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>';
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center'>No candidates found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Staff Section -->
        <div class="card mb-xl" id="staffs">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-chalkboard-teacher"></i> Staff</h2>
            </div>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Dept ID</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Operations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT S_ID, F_Name, L_Name, D_ID, Email, Role FROM staff";
                        $result = mysqli_query($conn, $sql);

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<tr>
                                    <td>' . htmlspecialchars($row['S_ID']) . '</td>
                                    <td>' . htmlspecialchars($row['F_Name'] . ' ' . $row['L_Name']) . '</td>
                                    <td>' . htmlspecialchars($row['D_ID']) . '</td>
                                    <td>' . htmlspecialchars($row['Email']) . '</td>
                                    <td><span class="badge badge-primary">' . htmlspecialchars($row['Role']) . '</span></td>
                                    <td>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <a href="../users/updateStaff.php?updateid=' . $row['S_ID'] . '" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                            <a href="../users/deleteStaff.php?deleteid=' . $row['S_ID'] . '" class="btn btn-sm btn-secondary" onclick="return confirm(\'Are you sure you want to delete this staff member?\')"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>';
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>No staff found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include ("../includes/footer.php"); ?>
</body>
</html>
