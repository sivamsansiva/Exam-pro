<?php
// session_start();
include('../config/config.php');

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
  <link rel="stylesheet" href="../styles/result.css">
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
    body{
    background: linear-gradient(90deg, #ffffff 0%, #EB8317 35%, #10375C 100%);
}
.Result {
    /* background-image: url('pngtree-vector-abstract-background-technology-concept-future-electric-blue-vector-png-image_23646548.jpg'); */
    font-family: Arial, sans-serif;
    /* background-color: #fff; */
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}


.container {
    background-color: #98c0da;
    padding: 80px;
    border-radius: 8px;
    width: 500px;
    text-align: center;
    box-shadow: 0 5px 10px rgba(0,0,0,.2);
    transition: 0.3s;

}
.container:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 15px rgba(0,0,0,.2);
}

h1 {
    margin-bottom: 20px;
}

label {
    display: block;
    margin: 10px 0 5px;
}

input {
    width: 80%;
    padding: 8px;
    margin-bottom: 15px;
    border: 3px solid #938383;
    border-radius: 4px;
}

button {
    padding: 10px 15px;
    background-color: #007BFF;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button:hover {
    background-color: #0056b3;
}

#result {
    margin-top: 20px;
    font-size: 18px;
    font-weight: bold;
    color: #fff;
}
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

  #loader {
    display: none;
    font-size: 20px;
    color: #333;
    text-align: center;
    padding: 20px;
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 9999;
  }
  </style>
</head>

<body>
<?php
    include ("../includes/header.php");
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

    document.getElementById('resultForm').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent the default form submission

    // Show loader
    document.getElementById('loader').style.display = 'block';

    // Simulate a delay to mimic processing time (like querying a database)
    setTimeout(function () {
      // Hide the loader after delay
      document.getElementById('loader').style.display = 'none';

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

    }, 2000); // Simulate a 2-second delay

    // Close button functionality
    document.getElementById('closePopup').addEventListener('click', function () {
      document.getElementById('popup').style.display = 'none'; // Hide the popup
      document.getElementById('resultForm').reset(); // Reset the form
    });
  });
  </script>
<?php
    include ("../includes/footer.php");
?>
</body>

</html>
