
// Add New Exam
function addExam() {
    const examList = document.getElementById('exam-list').querySelector('ul');
    const newExam = document.createElement('li');
    newExam.innerHTML = 'New Exam <button class="btn edit-btn" onclick="editExam()">Edit</button> <button class="btn delete-btn" onclick="deleteExam()">Delete</button>';
    examList.appendChild(newExam);
}
// Add Exam
function addExam() {
    window.location.href = 'addExam.php';
}

// Edit Exam
function editExam() {
    alert('Edit Exam feature is coming soon!');
    window.location.href = 'updateExam.php';
}

// Delete Exam
function deleteExam() {
    alert('Delete Exam feature is coming soon!');
    window.location.href = 'deleteExam.php';
}

// Function to update profile information via AJAX
function updateProfile() {
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;

    // Perform basic validation (optional)
    if (name === '' || email === '') {
        alert('Please fill out all fields.');
        return;
    }

    // Prepare data to send to the server
    const formData = new FormData();
    formData.append('name', name);
    formData.append('email', email);

    // Send the data using AJAX
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_profile.php', true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            // Success message
            alert('Profile updated successfully!');
            document.getElementById('name').disabled = true;
            document.getElementById('email').disabled = true;
            document.getElementById('saveBtn').disabled = true;
        } else {
            // Error handling
            alert('Error updating profile.');
        }
    };
    xhr.send(formData);
}