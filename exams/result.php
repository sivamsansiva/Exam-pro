<?php
// session_start();
include('../config/config.php');

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] == 'Manager' || $_SESSION['role'] == 'Examiner' || $_SESSION['role'] == 'Admin'){
  header("Location: ../auth/login.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Exam Results</title>
  <link rel="stylesheet" href="../styles/theme.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .popup {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(4px);
      align-items: center;
      justify-content: center;
    }

    .popup.show {
      display: flex;
    }

    .popup-content {
      background-color: var(--white);
      padding: var(--spacing-xl);
      border-radius: var(--radius-lg);
      width: 90%;
      max-width: 500px;
      text-align: center;
      box-shadow: var(--shadow-xl);
      position: relative;
      animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
      from { transform: translateY(-20px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
  </style>
</head>

<body>
<?php
    include ("../includes/header.php");
?>
  <div class="container mt-xl mb-xl">
    <div class="card" style="max-width: 600px; margin: 0 auto;">
      <div class="card-header">
        <h1 class="card-title"><i class="fas fa-chart-line"></i> Exam Results</h1>
        <p class="card-subtitle">View your examination results</p>
      </div>
      <div class="card-body">
        <!-- Form -->
        <form id="resultForm" action="" method="post">
          <div class="form-group">
            <label for="name" class="form-label">Employee Name</label>
            <input type="text" id="name" name="name" class="form-control" required>
          </div>

          <div class="form-group">
            <label for="id" class="form-label">Employee ID</label>
            <input type="text" id="id" name="C_ID" class="form-control" required>
          </div>

          <div class="form-group">
            <label for="examId" class="form-label">Exam ID</label>
            <input type="text" id="examId" name="E_ID" class="form-control" required>
          </div>

          <button type="submit" name="submit" class="btn btn-primary" style="width: 100%;">View Result</button>
        </form>
      </div>
    </div>
  </div>

  <?php
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $E_ID = mysqli_real_escape_string($conn, $_POST['E_ID']);
    $C_ID = mysqli_real_escape_string($conn, $_POST['C_ID']);

    // Query the database to get the result
    $sql = "SELECT E_ID, C_ID, Result FROM attends WHERE E_ID = '$E_ID' AND C_ID = '$C_ID'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
      $E_ID = $row["E_ID"];
      $C_ID = $row["C_ID"];
      $Result = $row["Result"];

      // Pass result data to JavaScript using PHP
      echo "<script>
              var resultData = {
                eid: '" . htmlspecialchars($E_ID) . "',
                cid: '" . htmlspecialchars($C_ID) . "',
                result: '" . htmlspecialchars($Result) . "'
              };
            </script>";
    } else {
      echo "<script>
              var resultData = { error: 'No result found for the provided details.' };
            </script>";
    }
  }
  ?>

  <!-- Popup for displaying the result -->
  <div id="popup" class="popup">
    <div class="popup-content">
      <h2 id="popupTitle" style="margin-bottom: 1rem;"></h2>
      <div id="popupMessage" style="margin-bottom: 1.5rem; line-height: 1.6;"></div>
      <button id="closePopup" class="btn btn-secondary">Close</button>
    </div>
  </div>

  <script>
    // Check if resultData is defined (meaning the form has been submitted and PHP has passed the result)
    if (typeof resultData !== 'undefined') {
      var popup = document.getElementById('popup');
      var popupTitle = document.getElementById('popupTitle');
      var popupMessage = document.getElementById('popupMessage');

      // Show the result in a popup
      if (!resultData.error) {
        popupTitle.innerHTML = '<i class="fas fa-check-circle" style="color: var(--secondary);"></i> Exam Result';
        popupMessage.innerHTML =
          '<strong>Exam ID:</strong> ' + resultData.eid + '<br>' +
          '<strong>Employee ID:</strong> ' + resultData.cid + '<br>' +
          '<strong>Result:</strong> <span style="font-size: 1.2em; font-weight: bold; color: var(--primary);">' + resultData.result + '</span>';
      } else {
        // If no result is found
        popupTitle.innerHTML = '<i class="fas fa-exclamation-circle" style="color: var(--error);"></i> Error';
        popupMessage.innerText = resultData.error;
      }

      // Show the popup
      popup.classList.add('show');
    }

    // Close button functionality
    document.getElementById('closePopup').addEventListener('click', function () {
      document.getElementById('popup').classList.remove('show');
      // Optional: Reset form or redirect
      // document.getElementById('resultForm').reset();
    });
  </script>
<?php
    include ("../includes/footer.php");
?>
</body>
</html>
