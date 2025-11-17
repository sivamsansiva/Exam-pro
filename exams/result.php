<?php
// session_start();
include('php/config.php');

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] == 'Manager' || $_SESSION['role'] == 'Examiner' || $_SESSION['role'] == 'Admin'){
  header("Location: login.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Exam Results</title>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="style/result.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
  <style>
    .popup {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
    }

    .popup-content {
      background-color: white;
      margin: 20% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 75%;
      text-align: center;
    }
  </style>
</head>

<body>
<?php
    include ("php/header.php");
?>
  <div class="Result">
    <div class="container">
      <h1>Exam Results</h1>

      <!-- Form -->
      <form id="resultForm" action="" method="post">
        <label for="name">Employee Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="id">Employee ID:</label>
        <input type="text" id="id" name="C_ID" required>

        <label for="examId">Exam ID:</label>
        <input type="text" id="examId" name="E_ID" required>

        <button type="submit" name="submit">View the Result</button>
      </form>

      <?php
      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $E_ID = $_POST['E_ID'];
        $C_ID = $_POST['C_ID'];

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
                    eid: '" . $E_ID . "',
                    cid: '" . $C_ID . "',
                    result: '" . $Result . "'
                  };
                </script>";
        } else {
          echo "<script>
                  var resultData = { error: 'No result found.' };
                </script>";
        }

        mysqli_close($conn);
      }
      ?>

      <!-- Popup for displaying the result -->
      <div id="popup" class="popup">
        <div class="popup-content">
          <h2 id="popupTitle"></h2>
          <p id="popupMessage"></p>
          <button id="closePopup">Close</button>
        </div>
      </div>

    </div>
  </div>

  <script>
    // Check if resultData is defined (meaning the form has been submitted and PHP has passed the result)
    if (typeof resultData !== 'undefined') {
      // Show the result in a popup
      if (!resultData.error) {
        document.getElementById('popupTitle').innerText = 'Exam Result';
        document.getElementById('popupMessage').innerHTML =
          'Exam ID: ' + resultData.eid + '<br>' +
          'Employee ID: ' + resultData.cid + '<br>' +
          'Result: ' + resultData.result;
      } else {
        // If no result is found
        document.getElementById('popupTitle').innerText = 'Error';
        document.getElementById('popupMessage').innerText = resultData.error;
      }

      // Show the popup
      document.getElementById('popup').style.display = 'block';
    }

    // Close button functionality
    document.getElementById('closePopup').addEventListener('click', function () {
      document.getElementById('popup').style.display = 'none'; // Hide the popup
      document.getElementById('resultForm').reset(); // Reset the form
    });
  </script>
<?php
    include ("php/footer.php");
?>
</body>

</html>