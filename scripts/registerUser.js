function validateForm() {
    // Get form values
    const gender = document.querySelector('input[name="gender"]:checked');
    const dob = document.getElementById("dob").value;
    const age = document.getElementById("age").value;
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

    // Regular expressions for validation
    const passwordPattern = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;

    // Validate gender
    if (!gender) {
        alert("Please select your gender.");
        return false;
    }

    // Validate Date of Birth (ensure user is at least 21 years old)
    if (!dob) {
        alert("Please enter your date of birth.");
        return false;
    }

    const dobDate = new Date(dob);
    const currentDate = new Date();
    const ageFromDOB = currentDate.getFullYear() - dobDate.getFullYear();
    
    // User must be at least 21 years old
    if (ageFromDOB < 21 || (ageFromDOB === 21 && currentDate < new Date(dobDate.setFullYear(dobDate.getFullYear() + 21)))) {
        alert("You must be at least 21 years old.");
        return false;
    }

    // Validate age input (age should be between 21 and 40)
    if (age < 21 || age > 40) {
        alert("Age must be between 21 and 40.");
        return false;
    }

    // Password validation
    if (!passwordPattern.test(password)) {
        alert("Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, and one number.");
        return false;
    }

    // Confirm password validation
    if (password !== confirmPassword) {
        alert("Passwords do not match! Please try again.");
        return false;
    }

    // If everything is valid, return true to allow form submission
    return true;
}

function enableButton() {
    document.getElementById("submitbtn").disabled = !document.getElementById("checkbox").checked;
}
