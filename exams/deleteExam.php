<?php

include('../config/config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] == 'staff'){
    header("Location: ../auth/login.php");
    exit();
}

// Delete Exam Operation
if(isset($_GET['deleteid'])){
    $examId = (int)$_GET['deleteid'];

    // Check if any staff has registered for this exam
    $regCheckStmt = $conn->prepare("SELECT COUNT(*) as count FROM exam_registration WHERE exam_id = ?");
    $regCheckStmt->bind_param("i", $examId);
    $regCheckStmt->execute();
    $regCount = $regCheckStmt->get_result()->fetch_assoc()['count'];
    $regCheckStmt->close();

    if ($regCount > 0) {
        $_SESSION['delete_error'] = 'Cannot delete exam: ' . $regCount . ' staff member(s) have registered for this exam.';

        if ($_SESSION['role'] == 'manager') {
            header('Location: ../dashboards/manager_dashboard.php');
        }
        elseif ($_SESSION['role'] == 'admin') {
            header('Location: ../dashboards/admin_dashboard.php');
        }
        elseif ($_SESSION['role'] == 'examiner') {
            header('Location: ../dashboards/examiner_dashboard.php');
        }
        exit();
    }

    // Only examiners can delete exams
    if ($_SESSION['role'] !== 'examiner') {
        $_SESSION['delete_error'] = 'Only examiners can delete exams.';

        if ($_SESSION['role'] == 'manager') {
            header('Location: ../dashboards/manager_dashboard.php');
        }
        elseif ($_SESSION['role'] == 'admin') {
            header('Location: ../dashboards/admin_dashboard.php');
        }
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM exam WHERE id = ?");
    $stmt->bind_param("i", $examId);
    $result = $stmt->execute();

    if($result){
        $_SESSION['delete_success'] = 'Exam deleted successfully.';
        header('Location: ../dashboards/examiner_dashboard.php');
    }else{
        $_SESSION['delete_error'] = 'Error deleting exam: ' . $conn->error;
        header('Location: ../dashboards/examiner_dashboard.php');
    }
    exit();
}

?>
