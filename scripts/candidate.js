function validateForm() {
    let dob = document.forms["profileForm"]["DOB"].value;
    let nic = document.forms["profileForm"]["NIC"].value;
    let email = document.forms["profileForm"]["Email"].value;

    let dobError = document.getElementById("dobError");
    let nicError = document.getElementById("nicError");
    let emailError = document.getElementById("emailError");

    let isValid = true;

    // Clear previous error messages
    dobError.textContent = "";
    nicError.textContent = "";
    emailError.textContent = "";

    // Validate Date of Birth
    const dobDate = new Date(dob);
    const today = new Date();
    const maxDOB = new Date();
    maxDOB.setFullYear(today.getFullYear() - 21); // Set maxDOB to today - 21 years

    if (dobDate > today) {
        dobError.textContent = "Date of Birth cannot be in the future.";
        isValid = false;
    } 
    else if (dobDate > maxDOB) {
        dobError.textContent = "You must be at least 21 years old.";
        isValid = false;
    }

    // Validate NIC (assuming it's supposed to be a specific format, e.g., 9 or 12 digits)
    let nicPattern = /^[0-9]{9}[Vv]$|^[0-9]{12}$/;
    if (!nicPattern.test(nic)) {
        nicError.textContent = "NIC must be 9 digits followed by 'V' or 12 digits.";
        isValid = false;
    }

    // Validate Email format
    let emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if (!emailPattern.test(email)) {
        emailError.textContent = "Invalid email format.";
        isValid = false;
    }

    return isValid;
}

function updateGreeting() {
    var firstName = document.querySelector('input[name="F_Name"]').value;
    var lastName = document.querySelector('input[name="L_Name"]').value;
    var greetingName = document.getElementById("greetingName");

    greetingName.textContent = firstName + " " + lastName;
}

// Attach event listeners to update the greeting whenever the first or last name is changed
document.querySelector('input[name="F_Name"]').addEventListener("input", updateGreeting);
document.querySelector('input[name="L_Name"]').addEventListener("input", updateGreeting);