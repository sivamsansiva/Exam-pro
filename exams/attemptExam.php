<?php
    require ("../config/config.php");

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Employee'){
        header("Location: login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attempt Exam</title>
    <link rel="stylesheet" href="../styles/examStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===================================
   MODERN EXAM REGISTRATION STYLES
   Glassmorphism Design with Theme Variables
   =================================== */

:root {
  --surface-glass: rgba(255, 255, 255, 0.95);
  --surface-glass-hover: rgba(255, 255, 255, 0.98);
  --surface-muted: rgba(255, 255, 255, 0.7);
  --text-primary: #0f172a;
  --text-secondary: #475569;
  --primary-500: #2563eb;
  --primary-600: #1d4ed8;
  --primary-700: #1e40af;
  --error-500: #dc2626;
  --border-color: rgba(15, 23, 42, 0.1);
  --focus-ring: 0 0 0 4px rgba(37, 99, 235, 0.15);
  --card-radius: 20px;
  --transition-base: 200ms ease;
}

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  min-height: 100vh;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  color: var(--text-primary);
  background: linear-gradient(
      135deg,
      rgba(15, 23, 42, 0.6),
      rgba(37, 99, 235, 0.35)
    ),
    url("../assets/images/exam_bg.jpg") center / cover no-repeat;
  position: relative;
  overflow-x: hidden;
  display: flex;
  flex-direction: column;
}

body::before {
  content: "";
  position: fixed;
  inset: 0;
  background: linear-gradient(
    160deg,
    rgba(15, 23, 42, 0.55) 0%,
    rgba(30, 64, 175, 0.35) 100%
  );
  backdrop-filter: blur(3px);
  z-index: -2;
}

body::after {
  content: "";
  position: fixed;
  inset: 0;
  background: linear-gradient(
    45deg,
    rgba(37, 99, 235, 0.1) 0%,
    rgba(52, 168, 83, 0.1) 100%
  );
  z-index: -1;
}

/* Main Container */
.registration-container {
  width: 95%;
  max-width: 1400px;
  margin: 2rem auto;
  background: var(--surface-glass);
  border-radius: var(--card-radius);
  padding: 2rem;
  box-shadow: 0 32px 70px rgba(15, 23, 42, 0.35);
  backdrop-filter: blur(16px);
  transition: transform var(--transition-base),
    box-shadow var(--transition-base);
  display: flex;
  gap: 2rem;
  flex: 1;
}

.registration-container:hover {
  transform: translateY(-4px);
  box-shadow: 0 42px 90px rgba(15, 23, 42, 0.38);
}

/* Exam Details Section */
.exam-details {
  flex: 1;
  padding: 2rem;
  border-right: 1px solid var(--border-color);
  background: var(--surface-muted);
  border-radius: var(--card-radius);
}

.exam-details h2 {
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 1.5rem;
  letter-spacing: -0.02em;
}

.exam-info {
  margin-bottom: 1.25rem;
  padding: 1rem;
  background: var(--surface-glass);
  border-radius: 12px;
  border: 1px solid var(--border-color);
  transition: all var(--transition-base);
}

.exam-info:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(15, 23, 42, 0.15);
}

.exam-info label {
  font-weight: 600;
  color: var(--primary-600);
  font-size: 0.95rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 0.5rem;
  display: block;
}

.exam-info span {
  color: var(--text-primary);
  font-size: 1.05rem;
  font-weight: 500;
}

/* Registration Form Section */
.registration-form {
  flex: 1;
  padding: 2rem;
}

.registration-form h1 {
  font-size: 2.15rem;
  font-weight: 700;
  letter-spacing: -0.02em;
  margin-bottom: 1.5rem;
  color: var(--text-primary);
  text-align: center;
}

.registration-form h1::after {
  content: "";
  display: block;
  width: 60px;
  height: 4px;
  background: linear-gradient(90deg, var(--primary-500), var(--primary-700));
  margin: 1rem auto 0;
  border-radius: 2px;
}

/* Form Elements */
form {
  display: grid;
  gap: 1.5rem;
}

.form-field {
  display: grid;
  gap: 0.5rem;
}

.form-field label {
  font-weight: 600;
  color: var(--text-primary);
  font-size: 1rem;
}

.form-field input,
.form-field select {
  width: 100%;
  border: 1px solid var(--border-color);
  border-radius: 12px;
  padding: 0.875rem 1rem;
  font-size: 1rem;
  background: #ffffff;
  color: var(--text-primary);
  transition: border-color var(--transition-base),
    box-shadow var(--transition-base), background-color var(--transition-base);
}

.form-field select {
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'%3E%3Cpath fill='none' stroke='%232563eb' stroke-width='2' d='m6 9l6 6l6-6'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 16px center;
  background-size: 18px;
}

.form-field input:focus,
.form-field select:focus {
  outline: none;
  border-color: var(--primary-500);
  box-shadow: var(--focus-ring);
  background: var(--surface-glass);
}

/* Submit Button */
input[type="submit"] {
  border: none;
  border-radius: 12px;
  background: linear-gradient(
    135deg,
    var(--primary-500) 0%,
    var(--primary-600) 45%,
    var(--primary-700) 100%
  );
  color: #ffffff;
  padding: 1rem 2rem;
  font-size: 1.05rem;
  font-weight: 600;
  cursor: pointer;
  transition: transform var(--transition-base),
    box-shadow var(--transition-base), filter var(--transition-base);
  margin-top: 1rem;
}

input[type="submit"]:hover {
  transform: translateY(-2px);
  box-shadow: 0 18px 32px rgba(37, 99, 235, 0.28);
}

input[type="submit"]:active {
  transform: translateY(0);
  filter: brightness(0.96);
}

/* Message Styles */
.message {
  padding: 1rem 1.25rem;
  margin: 1.5rem 0;
  border-radius: 14px;
  font-weight: 500;
  border-left: 4px solid;
}

.message.success {
  background: rgba(52, 168, 83, 0.08);
  border-color: var(--secondary, #34a853);
  color: #065f46;
}

.message.error {
  background: rgba(220, 38, 38, 0.08);
  border-color: var(--error-500);
  color: #991b1b;
}

.message.warning {
  background: rgba(251, 188, 5, 0.08);
  border-color: var(--warning, #fbbc05);
  color: #92400e;
}

/* Responsive Design */
@media (max-width: 968px) {
  .registration-container {
    flex-direction: column;
    padding: 1.5rem;
    gap: 1.5rem;
  }

  .exam-details {
    border-right: none;
    border-bottom: 1px solid var(--border-color);
    padding: 1.5rem;
  }

  .registration-form {
    padding: 1.5rem;
  }

  .registration-form h1 {
    font-size: 1.85rem;
  }
}

@media (max-width: 640px) {
  body {
    padding: 1rem;
  }

  .registration-container {
    margin: 1rem auto;
    padding: 1rem;
  }

  .exam-details,
  .registration-form {
    padding: 1rem;
  }

  .exam-details h2 {
    font-size: 1.5rem;
  }

  .registration-form h1 {
    font-size: 1.5rem;
  }
}

/* Legacy Support for Old Classes */
.attemptExam,
.registration,
.addExam,
.container {
  width: 95%;
  max-width: 600px;
  margin: 2rem auto;
  background: var(--surface-glass);
  border-radius: var(--card-radius);
  padding: 2rem;
  box-shadow: 0 32px 70px rgba(15, 23, 42, 0.35);
  backdrop-filter: blur(16px);
  transition: transform var(--transition-base),
    box-shadow var(--transition-base);
}

.attemptExam:hover,
.registration:hover,
.addExam:hover,
.container:hover {
  transform: translateY(-4px);
  box-shadow: 0 42px 90px rgba(15, 23, 42, 0.38);
}

h1 {
  text-align: center;
  margin-bottom: 1.5rem;
  color: var(--text-primary);
  font-size: 2.15rem;
  font-weight: 700;
  letter-spacing: -0.02em;
}

h1::after {
  content: "";
  display: block;
  width: 60px;
  height: 4px;
  background: linear-gradient(90deg, var(--primary-500), var(--primary-700));
  margin: 1rem auto 0;
  border-radius: 2px;
}

label {
  font-size: 1rem;
  margin-bottom: 0.5rem;
  color: var(--text-primary);
  font-weight: 600;
}

input[type="text"],
input[type="email"],
input[type="password"],
textarea,
select {
  padding: 0.875rem 1rem;
  margin-bottom: 1.5rem;
  border: 1px solid var(--border-color);
  border-radius: 12px;
  font-size: 1rem;
  background: #ffffff;
  color: var(--text-primary);
  transition: border-color var(--transition-base),
    box-shadow var(--transition-base), background-color var(--transition-base);
}

input[type="text"]:focus,
input[type="email"]:focus,
input[type="password"]:focus,
textarea:focus,
select:focus {
  outline: none;
  border-color: var(--primary-500);
  box-shadow: var(--focus-ring);
  background: var(--surface-glass);
}

select {
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'%3E%3Cpath fill='none' stroke='%232563eb' stroke-width='2' d='m6 9l6 6l6-6'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 16px center;
  background-size: 18px;
}

/* Results Page Styles */
.Result {
  background: linear-gradient(
      135deg,
      rgba(15, 23, 42, 0.6),
      rgba(37, 99, 235, 0.35)
    ),
    url("../assets/images/result_bg.jpg") center / cover no-repeat;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  background-color: var(--bg-primary);
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  margin: 0;
  position: relative;
}

.Result::before {
  content: "";
  position: fixed;
  inset: 0;
  background: linear-gradient(
    160deg,
    rgba(15, 23, 42, 0.55) 0%,
    rgba(30, 64, 175, 0.35) 100%
  );
  backdrop-filter: blur(3px);
  z-index: -1;
}

/* Accessibility and Motion Preferences */
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 1ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 1ms !important;
    scroll-behavior: auto !important;
  }
}

    </style>
</head>
<body>
    <?php
        include ("../includes/header.php");
    ?>
    <div class="attemptExam">
        <h1><i class="fas fa-pen-to-square"></i> Attempt Exam</h1>
        <form method="post" action="">
            <div class="form-field">
                <label for="exam">Select Exam:</label>
                <?php
                    $exams = "SELECT E_Name FROM exam";
                    $result = $conn->query($exams);
                    if($result && $result->num_rows > 0){
                ?>
                <select name="exam" id="exam" required>
                    <option value="" disabled selected>Select an exam</option>
                    <?php
                        while ($row = $result->fetch_assoc()) {
                            $examName = $row['E_Name'];
                            echo "<option value=\"" . htmlspecialchars($examName) . "\">" . htmlspecialchars($examName) . "</option>";
                        }
                    ?>
                </select>
                <?php
                    }
                    else {
                        echo "<p style='color: #721c24;'>No Exam found</p>";
                    }
                ?>
            </div>

            <div class="form-field">
                <label for="employee-id">Employee ID:</label>
                <input type="text" name="employee-id" id="employee-id" placeholder="Enter Employee ID" required>
            </div>

            <div class="form-field">
                <label for="quiz-password">Quiz Password</label>
                <input type="password" name="quiz-password" id="quiz-password" placeholder="Enter Quiz Password" required>
            </div>

            <input type="submit" value="Attempt Exam">
        </form>
    </div>
    <?php
        include ("../includes/footer.php");
    ?>
    <script src="../scripts/mainScript.js"></script>
</body>
</html>
