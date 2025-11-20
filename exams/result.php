<?php
// session_start();
include('../config/config.php');

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] != 'Staff'){
  header("Location: ../auth/login.php");
  exit();
}

$userId = $_SESSION['user_id'] ?? null;
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
            <label for="examId" class="form-label">Exam Code or Name</label>
            <input type="text" id="examId" name="exam_search" class="form-control" placeholder="Enter exam code or name" required>
          </div>

          <button type="submit" name="submit" class="btn btn-primary" style="width: 100%;">View Result</button>
        </form>
      </div>
    </div>
  </div>

  <?php
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $examSearch = $_POST['exam_search'] ?? '';

    // Use prepared statement to get exam attempts
    $stmt = $conn->prepare("
      SELECT
        e.id, e.code, e.name, e.max_score,
        ea.attempt_no, ea.score, ea.started_at, ea.ended_at, ea.completed
      FROM exam_attempt ea
      INNER JOIN exam e ON ea.exam_id = e.id
      WHERE ea.user_id = ?
        AND (e.code LIKE ? OR e.name LIKE ?)
        AND ea.completed = 1
      ORDER BY ea.started_at DESC
    ");

    $searchPattern = "%{$examSearch}%";
    $stmt->bind_param("iss", $userId, $searchPattern, $searchPattern);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $attempts = [];
      while ($row = $result->fetch_assoc()) {
        $attempts[] = $row;
      }

      // Pass result data to JavaScript using PHP
      echo "<script>
              var resultData = " . json_encode($attempts) . ";
            </script>";
    } else {
      echo "<script>
              var resultData = { error: 'No completed exam attempts found for the provided search.' };
            </script>";
    }
    $stmt->close();
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
        popupTitle.innerHTML = '<i class="fas fa-check-circle" style="color: var(--secondary);"></i> Exam Results';

        var resultsHTML = '';
        resultData.forEach(function(attempt, index) {
          var percentage = (attempt.score / attempt.max_score * 100).toFixed(2);
          var passed = percentage >= 60 ? 'Passed' : 'Failed';
          var statusColor = percentage >= 60 ? 'var(--secondary)' : 'var(--error)';

          resultsHTML += '<div style="border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1rem; text-align: left;">';
          resultsHTML += '<strong>Exam:</strong> ' + attempt.name + ' (' + attempt.code + ')<br>';
          resultsHTML += '<strong>Attempt:</strong> #' + attempt.attempt_no + '<br>';
          resultsHTML += '<strong>Score:</strong> ' + attempt.score + '/' + attempt.max_score + ' (' + percentage + '%)<br>';
          resultsHTML += '<strong>Status:</strong> <span style="color: ' + statusColor + '; font-weight: bold;">' + passed + '</span><br>';
          resultsHTML += '<strong>Started:</strong> ' + new Date(attempt.started_at).toLocaleString() + '<br>';
          resultsHTML += '<strong>Completed:</strong> ' + new Date(attempt.ended_at).toLocaleString();
          resultsHTML += '</div>';
        });

        popupMessage.innerHTML = resultsHTML;
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
    });
  </script>
<?php
    include ("../includes/footer.php");
?>
</body>
</html>
