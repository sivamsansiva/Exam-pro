document.getElementById('resultForm').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent the default form submission

    // Show loader
    document.getElementById('loader').style.display = 'block';

    // Simulate a delay to mimic processing time (like querying a database)
    setTimeout(function () {
      // Hide the loader after delay
      document.getElementById('loader').style.display = 'none';

      if (typeof resultData !== 'undefined') {
        // Show the result in a popup
        if (!resultData.error) {
          document.getElementById('popupTitle').innerText = 'Exam Result';
          document.getElementById('popupMessage').innerHTML =
            'Exam ID: ' + resultData.eid + '<br>' +
            'Employee ID: ' + resultData.cid + '<br>' +
            'Result: ' + resultData.result;
        } else {
          // If no result is found
          document.getElementById('popupTitle').innerText = 'Error';
          document.getElementById('popupMessage').innerText = resultData.error;
        }

        // Show the popup
        document.getElementById('popup').style.display = 'block';
      }

    }, 2000); // Simulate a 2-second delay

    // Close button functionality
    document.getElementById('closePopup').addEventListener('click', function () {
      document.getElementById('popup').style.display = 'none'; // Hide the popup
      document.getElementById('resultForm').reset(); // Reset the form
    });
  });