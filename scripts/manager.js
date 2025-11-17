
const modal = document.getElementById("myModal");
    const btn = document.getElementById("openPopupBtn");
    const span = document.getElementsByClassName("close")[0];

    btn.onclick = function () {
        modal.style.display = "block";
    }

    span.onclick = function () {
        modal.style.display = "none";
    }

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    function ConfirmDelete_Exam() {
        // var E_ID = eid;
        var result = confirm("Do you want to Delete?");
        if (result) {
            alert("You clicked ok !");
            // window.location.href = ";
    }else {
        alert("You clicked Cancel!");
        window.location.href = "admin.php";
    }
    }

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