<?php
include("../config/config.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Staff') {
    header("Location: ../auth/login.php");
    exit();
}
// Database content

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_id'];
    $messageContent = $_POST["message_content"];
    $messageType = 'feedback';
    $status = 'open';

    global $conn;

    $stmt = $conn->prepare("INSERT INTO message (user_id, type, content, status, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("isss", $userId, $messageType, $messageContent, $status);

    if ($stmt->execute()) {
        $stmt->close();
        echo '<script>alert("Feedback submitted successfully!");</script>';
        echo '<script>window.location.href = "../index.php";</script>';
    } else {
        $stmt->close();
        echo "Error: Unable to submit feedback.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Feedback</title>
</head>

<body>
    <?php
    include("../includes/header.php");
    ?>
    <!-- Add Feedback content -->
    <div class="container mt-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title text-center">Add Feedback</h2>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label for="message_content" class="form-label">Your Feedback:</label>
                        <textarea id="message_content" name="message_content" rows="8" class="form-control" placeholder="Enter your feedback or suggestions here..." required></textarea>
                    </div>

                    <div class="text-center">
                        <input type="submit" name="submit" value="Submit Feedback" class="btn btn-primary btn-lg">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    include("../includes/footer.php");
    ?>
</body>

</html>
