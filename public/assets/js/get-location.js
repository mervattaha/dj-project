// get-location.js

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else {
        console.log("Geolocation is not supported by this browser.");
    }
}

function showPosition(position) {
    const latitude = position.coords.latitude;
    const longitude = position.coords.longitude;

    fetch('/set-location.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `latitude=${latitude}&longitude=${longitude}`
    })
    .then(response => response.text())
    .then(data => {
        console.log('Location data sent:', data);
        // Optionally refresh the page or update the UI here
    });
}

function showError(error) {
    console.log("Error getting location: ", error.message);
}

// Call the function to get the location when needed
getLocation();
