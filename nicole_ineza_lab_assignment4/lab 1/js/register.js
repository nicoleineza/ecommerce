document.getElementById("registerForm").addEventListener("submit", function(event) {
    event.preventDefault();

    let formData = new FormData(this);
    const loadingSpinner = document.getElementById("loading-spinner");
    const messageDiv = document.getElementById("message");
    loadingSpinner.style.display = "block"; 
    messageDiv.style.display = "none"; 

    fetch("../Controllers/customercontroller.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        loadingSpinner.style.display = "none"; 
        messageDiv.className = ''; 
        messageDiv.style.display = 'block'; 
        
        
        if (data.status === "success") {
            messageDiv.classList.add('success');
            messageDiv.textContent = data.message;
            setTimeout(() => {
                window.location.href = "../views/login.php";  
            }, 3000);  
        } else {
            messageDiv.classList.add('error');
            messageDiv.textContent = data.message; 
        }
    })
    .catch(error => {
        loadingSpinner.style.display = "none"; 
        messageDiv.classList.add('error');
        messageDiv.textContent = "An unexpected error occurred."; 
        messageDiv.style.display = "block"; 
        console.error("Error:", error);
    });
});
