document.getElementById("registerForm").addEventListener("submit", function(event) {
    let email = document.getElementById("email").value;
    let ashesiEmailPattern = /^[a-zA-Z0-9._%+-]+@ashesi\.edu\.gh$/;
    let contactPattern = /^\+?[1-9]\d{1,14}$/; 

    if (!ashesiEmailPattern.test(email)) {
        alert("Please enter a valid Ashesi email.");
        event.preventDefault();
    }

    let contact = document.getElementById("contact").value;
    if (!contactPattern.test(contact)) {
        alert("Please enter a valid phone number in E.164 format.");
        event.preventDefault();
    }
});
