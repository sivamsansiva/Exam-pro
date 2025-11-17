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
    }
    else {
        alert("You clicked Cancel!");
        window.location.href = "admin.php";
    }
}