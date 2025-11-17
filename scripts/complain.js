// JavaScript form validation
document.getElementById("complaintForm").onsubmit = function(event) 
{
    event.preventDefault(); // Prevent default form submission behavior

    let emp_id = document.getElementById("emp_id").value;
    let emp_email = document.getElementById("emp_email").value;
    let c_title = document.getElementById("c_title").value;
    let c_detail = document.getElementById("c_detail").value;

    let errorMessage = document.getElementById("errorMessage");
    let successMessage = document.getElementById("successMessage");

    // Clear any previous messages
    errorMessage.innerHTML = "";
    successMessage.innerHTML = "";

    // Validate fields
    if (!emp_id || !emp_email || !c_title || !c_detail) {
        errorMessage.innerHTML = "All fields are required!";
        return;
    }

    if (!validateEmail(emp_email)) {
        errorMessage.innerHTML = "Please enter a valid email address.";
        return;
    }

    // If validation is successful, show a success message
    successMessage.innerHTML = "Complaint submitted successfully!";
    
    // Reset the form after successful submission
    document.getElementById("complaintForm").reset();
};

// Function to validate email format
function validateEmail(email) {
    var re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return re.test(String(email).toLowerCase());
}
